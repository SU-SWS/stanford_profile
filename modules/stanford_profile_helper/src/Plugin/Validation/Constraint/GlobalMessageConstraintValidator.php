<?php

namespace Drupal\stanford_profile_helper\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the UniqueInteger constraint.
 */
class GlobalMessageConstraintValidator extends ConstraintValidator {

  /**
   * Global message enabling field name.
   */
  const ENABLER_FIELD = 'su_global_msg_enabled';

  /**
   * {@inheritdoc}
   */
  public function validate($value, Constraint $constraint) {

    $is_valid = FALSE;

    $resource_fields = [
      'su_global_msg_label',
      'su_global_msg_header',
      'su_global_msg_message',
      'su_global_msg_link',
    ];

    if (
      $value->getEntity()->hasField(self::ENABLER_FIELD) &&
      $value->getEntity()->get(self::ENABLER_FIELD)->getString()
    ) {

      foreach ($resource_fields as $field) {
        if (!empty($value->getEntity()->get($field)->getString())) {
          $is_valid = TRUE;
        }
      }

      if (!$is_valid) {
        $this->context->addViolation($constraint->fieldsNotPopulated);
      }

    }
  }

}
