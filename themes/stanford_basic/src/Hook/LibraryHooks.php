<?php

declare(strict_types=1);

namespace Drupal\stanford_basic\Hook;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Hook\Attribute\Hook;

class LibraryHooks {

  public function __construct(protected EntityTypeManagerInterface $entityTypeManager) {}

  /**
   * Implements hook_library_info_alter().
   */
  #[Hook('library_info_alter')]
  public function libraryInfoAlter(&$libraries, $extension): void {
    if ($extension == 'stanford_basic' && \Drupal::hasService('config_pages.loader')) {
      /** @var \Drupal\config_pages\ConfigPagesLoaderServiceInterface $config_pages */
      $config_pages = \Drupal::service('config_pages.loader');
      $js_file_id = $config_pages->getValue('stanford_basic_site_settings', 'su_site_algolia_file', 0, 'target_id');
      if (!$js_file_id) {
        return;
      }

      /** @var \Drupal\file\FileInterface $file */
      $file = $this->entityTypeManager->getStorage('file')->load($js_file_id);
      if ($file) {
        $libraries['algolia-search']['js'] = [$file->createFileUrl() => ['minified' => TRUE]];
      }
    }
  }

}
