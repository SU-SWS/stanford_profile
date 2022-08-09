<?php

namespace Drupal\stanford_profile_helper\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Plugin implementation of the 'global_message_constraint'.
 *
 * @Constraint(
 *   id = "global_message_constraint",
 *   label = @Translation("Global message constraint", context = "Validation"),
 * )
 */
class GlobalMessageConstraint extends Constraint {

  public $fieldsNotPopulated = 'To enable a global message, at least one field must have a value: Label, Headline, Message, Action Link.';

}
