<?php

namespace Drupal\Tests\stanford_profile_helper\Unit\Plugin\Validation\Constraint;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Field\FieldItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Core\TypedData\TypedDataInterface;
use Drupal\menu_link_content\MenuLinkContentInterface;
use Drupal\path_alias\AliasManagerInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\stanford_profile_helper\Plugin\Validation\Constraint\MenuLinkItemConstraint;
use Drupal\stanford_profile_helper\Plugin\Validation\Constraint\MenuLinkItemConstraintValidator;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class MenuLinkItemConstraintValidatorTest.
 *
 * @group stanford_profile_helper
 * @coversDefaultClass \Drupal\stanford_profile_helper\Plugin\Validation\Constraint\MenuLinkItemConstraintValidator
 */
class MenuLinkItemConstraintValidatorTest extends UnitTestCase {

  /**
   * Has the field value already been returned via the mock?
   *
   * @var bool
   */
  protected $fieldValueReturned = FALSE;

  /**
   * All fields are populated.
   */
  public function testNoErrorValidation() {
    $request = $this->createMock(Request::class);
    $request->method('getSchemeAndHttpHost')->willReturn('https://foobar.baz');

    $request_stack = $this->createMock(RequestStack::class);
    $request_stack->method('getCurrentRequest')->willReturn($request);
    $path_alias_manager = $this->createMock(AliasManagerInterface::class);

    $container = new ContainerBuilder();
    $container->set('request_stack', $request_stack);
    $container->set('path_alias.manager', $path_alias_manager);

    $link_uri = NULL;
    $property = $this->createMock(TypedDataInterface::class);
    $property->method('getString')->willReturnReference($link_uri);

    $field_value = $this->createMock(FieldItemInterface::class);
    $field_value->method('get')->willReturn($property);

    $field_value_list = $this->createMock(FieldItemListInterface::class);
    $field_value_list->method('get')->willReturn($field_value);

    $entity = $this->createMock(MenuLinkContentInterface::class);
    $entity->method('get')->willReturn($field_value_list);

    $validator = TestMenuLinkItemConstraintValidator::create($container);
    $validator->initialize($this->getContext());

    $link_uri = NULL;
    $validator->validate($entity, new MenuLinkItemConstraint());
    $this->assertFalse($validator->hasErrors());

    $link_uri = '/foo/bar';
    $validator->validate($entity, new MenuLinkItemConstraint());
    $this->assertFalse($validator->hasErrors());

    $link_uri = 'https://bar.foo/baz';
    $validator->validate($entity, new MenuLinkItemConstraint());
    $this->assertFalse($validator->hasErrors());

    $link_uri = 'https://foobar.baz/bar/foo';
    $validator->validate($entity, new MenuLinkItemConstraint());
    $this->assertTrue($validator->hasErrors());
  }

  protected function getContext() {
    $validator = $this->createMock(ValidatorInterface::class);
    $translator = $this->createMock(TranslatorInterface::class);
    return new ExecutionContext($validator, '', $translator);
  }

}

/**
 * Testable validator.
 */
class TestMenuLinkItemConstraintValidator extends MenuLinkItemConstraintValidator {

  /**
   * If the violation has errors.
   *
   * @return bool
   *   Violations exist.
   */
  public function hasErrors() {
    return $this->context->getViolations()->count() > 0;
  }

}
