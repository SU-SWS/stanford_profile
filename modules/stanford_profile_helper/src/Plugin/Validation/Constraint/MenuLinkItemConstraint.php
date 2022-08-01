<?php

namespace Drupal\stanford_profile_helper\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Checks that the submitted value is a unique integer.
 *
 * @Constraint(
 *   id = "menu_link_item_url_constraint",
 *   label = @Translation("Menu Link Item", context = "Validation"),
 *   type = "string"
 * )
 */
class MenuLinkItemConstraint extends Constraint {

  public $absoluteLink = 'The link URL must not be an absolute URL. Please use relative links that start with "/" for local destinations.';

}
