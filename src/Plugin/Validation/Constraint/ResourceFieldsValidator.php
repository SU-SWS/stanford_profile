<?php

namespace Drupal\cardinal_service_profile\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validator for resource field constraint.
 *
 * @package Drupal\cardinal_service_profile\Plugin\Validation\Constraint
 */
class ResourceFieldsValidator extends ConstraintValidator {

  /**
   * {@inheritDoc}
   */
  public function validate($value, Constraint $constraint) {
    $resource_fields = [
      'su_page_resource_type' => 0,
      'su_page_resource_audience' => 0,
      'su_page_resource_dimension' => 0,
    ];

    foreach (array_keys($resource_fields) as $resource_field) {
      if ($value->getEntity()->hasField($resource_field)) {
        $resource_fields[$resource_field] = $value->getEntity()
          ->get($resource_field)
          ->count();
      }
    }

    if (array_filter($resource_fields) && count(array_filter($resource_fields)) != count($resource_fields)) {
      $this->context->addViolation($constraint->fieldsNotPopulated);
    }
  }

}
