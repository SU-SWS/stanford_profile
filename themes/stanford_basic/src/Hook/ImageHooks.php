<?php

declare(strict_types=1);

namespace Drupal\stanford_basic\Hook;

use Drupal\Core\Hook\Attribute\Hook;

/**
 * Marks images without alt text as decorative.
 */
class ImageHooks {

  /**
   * Implements hook_preprocess_image().
   */
  #[Hook('preprocess_image')]
  public function preprocessImage(&$vars): void {
    // Decorative images get the role="presentation" attribute.
    if (!isset($vars['attributes']['alt'])) {
      $vars['attributes']['role'] = 'presentation';
    }
  }

}
