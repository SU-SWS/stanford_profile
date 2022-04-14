<?php

namespace Drupal\Tests\stanford_profile_helper\Unit\Plugin\Validation\Constraint;

use Drupal\stanford_profile_helper\Plugin\Validation\Constraint\GlobalMessageConstraint;
use Drupal\stanford_profile_helper\Plugin\Validation\Constraint\GlobalMessageConstraintValidator;
use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Tests\UnitTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class GlobalMessageConstraintValidatorTest.
 *
 * @group stanford_profile_helper
 * @coversDefaultClass \Drupal\stanford_profile_helper\Plugin\Validation\Constraint\GlobalMessageConstraintValidator
 */
class GlobalMessageConstraintValidatorTest extends UnitTestCase {

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
    $validator = new TestGlobalMessageConstraintValidator();
    $validator->initialize($this->getContext());

    $field = $this->createMock(FieldItemListInterface::class);
    $field->method('getString')->willReturn('foo');
    $entity = $this->createMock(FieldableEntityInterface::class);
    $entity->method('get')->willReturn($field);
    $entity->method('hasField')->willReturn(TRUE);
    $field_value = $this->createMock(FieldItemListInterface::class);
    $field_value->method('getEntity')->willReturn($entity);

    $constraint = new GlobalMessageConstraint();
    $validator->validate($field_value, $constraint);
    $this->assertFalse($validator->hasErrors());
  }

  /**
   * The message is turned on, but there are no content field values.
   */
  public function testErrorValidation() {
    $validator = new TestGlobalMessageConstraintValidator();
    $validator->initialize($this->getContext());

    $entity = $this->createMock(FieldableEntityInterface::class);
    $entity->method('get')
      ->will($this->returnCallback([$this, 'getFieldCallback']));
    $entity->method('hasField')->willReturn(TRUE);

    $field_value = $this->createMock(FieldItemListInterface::class);
    $field_value->method('getEntity')->willReturn($entity);

    $constraint = new GlobalMessageConstraint();
    $validator->validate($field_value, $constraint);
    $this->assertTrue($validator->hasErrors());
  }

  /**
   * The message is turned on, and there is only one content field with a value.
   */
  public function testValidValidation() {
    $this->fieldValueReturned = TRUE;
    $validator = new TestGlobalMessageConstraintValidator();
    $validator->initialize($this->getContext());

    $entity = $this->createMock(FieldableEntityInterface::class);
    $entity->method('get')
      ->will($this->returnCallback([$this, 'getFieldCallback']));
    $entity->method('hasField')->willReturn(TRUE);

    $field_value = $this->createMock(FieldItemListInterface::class);
    $field_value->method('getEntity')->willReturn($entity);

    $constraint = new GlobalMessageConstraint();
    $validator->validate($field_value, $constraint);
    $this->assertFalse($validator->hasErrors());
  }

  /**
   * Mock entity get field callback.
   *
   * @param string $field_name
   *   Field machine name.
   *
   * @return \Drupal\Core\Field\FieldItemListInterface|\PHPUnit\Framework\MockObject\MockObject
   *   Mocked field list object.
   */
  public function getFieldCallback($field_name) {
    $field = $this->createMock(FieldItemListInterface::class);
    if ($field_name == 'su_global_msg_enabled') {
      $field->method('getString')->wilLReturn('foo');
    }
    elseif ($this->fieldValueReturned) {
      $field->method('getString')->wilLReturn('foo');
      $this->fieldValueReturned = TRUE;
    }
    return $field;
  }

  /**
   * Build a context object for the validator.
   *
   * @return \Symfony\Component\Validator\Context\ExecutionContext
   */
  protected function getContext() {
    $validator = $this->createMock(ValidatorInterface::class);
    $translator = $this->createMock(TranslatorInterface::class);
    return new ExecutionContext($validator, '', $translator);
  }

}

/**
 * Testable validator.
 */
class TestGlobalMessageConstraintValidator extends GlobalMessageConstraintValidator {

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
