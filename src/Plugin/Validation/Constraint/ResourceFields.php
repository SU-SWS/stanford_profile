<?php

namespace Drupal\cardinal_service_profile\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Resource fields constraint.
 *
 * @Constraint(
 *   id = "ResourceFields",
 *   label = @Translation("Resource Fields", context = "Validation")
 * )
 */
class ResourceFields extends Constraint {

  /**
   * All fields on the resources have to be populated if any of them are filled.
   *
   * @var string
   */
  public $fieldsNotPopulated = 'All resource fields are required.';

}
