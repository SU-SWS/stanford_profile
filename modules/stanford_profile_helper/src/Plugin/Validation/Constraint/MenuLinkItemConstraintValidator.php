<?php

namespace Drupal\stanford_profile_helper\Plugin\Validation\Constraint;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\path_alias\AliasManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the Menu link item constraint.
 */
class MenuLinkItemConstraintValidator extends ConstraintValidator implements ContainerInjectionInterface {

  /**
   * Current request.
   *
   * @var \Drupal\path_alias\AliasManagerInterface
   */
  protected $request;

  /**
   * Path alias manager service.
   *
   * @var \Drupal\path_alias\AliasManagerInterface
   */
  protected $aliasManager;

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('request_stack'),
      $container->get('path_alias.manager')
    );
  }

  /**
   * Validation constructor.
   *
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   Current request stack.
   * @param \Drupal\path_alias\AliasManagerInterface $alias_manager
   *   Path alias manager service.
   */
  public function __construct(RequestStack $request_stack, AliasManagerInterface $alias_manager) {
    $this->request = $request_stack->getCurrentRequest();
    $this->aliasManager = $alias_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function validate($value, Constraint $constraint) {
    /** @var \Drupal\menu_link_content\MenuLinkContentInterface $value */
    /** @var \Drupal\Core\Field\FieldItemInterface $link_value */
    $link_value = $value->get('link')->get(0);
    $link_uri = $link_value->get('uri')->getString();
    if (str_contains($link_uri, $this->request->getSchemeAndHttpHost())) {
      $this->context->addViolation($constraint->absoluteLink);
    }
  }

}
