<?php

declare(strict_types=1);

namespace Drupal\jemison\Hook;

use Drupal\Core\Extension\ThemeHandlerInterface;
use Drupal\Core\Hook\Attribute\Hook;

class LibraryHooks {

  public function __construct(protected ThemeHandlerInterface $themeHandler) {}

  /**
   * Implements hook_library_info_alter().
   */
  #[Hook('library_info_alter')]
  public function libraryInfoAlter(&$libraries, $extension): void {
    // Keep libraries that are part of this theme.
    if ($this->themeHandler->themeExists($extension)) {
      return;
    }
    // Exclude libraries that are part of Stanford modules.
    if (preg_match('/(stanford_|jumpstart|react|ui_pattern)/', $extension)) {
      $libraries = [];
    }
  }

}
