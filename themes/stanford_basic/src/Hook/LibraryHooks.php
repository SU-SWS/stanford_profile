<?php

declare(strict_types=1);

namespace Drupal\stanford_basic\Hook;

use Drupal\config_pages\ConfigPagesLoaderServiceInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Swaps in the site-uploaded Algolia search script for the theme library.
 */
class LibraryHooks {

  public function __construct(
    protected EntityTypeManagerInterface $entityTypeManager,
    // NULL when the config_pages module isn't installed.
    #[Autowire(service: 'config_pages.loader')]
    protected ?ConfigPagesLoaderServiceInterface $configPagesLoader = NULL,
  ) {}

  /**
   * Implements hook_library_info_alter().
   */
  #[Hook('library_info_alter')]
  public function libraryInfoAlter(&$libraries, $extension): void {
    if ($extension == 'stanford_basic' && $this->configPagesLoader) {
      $js_file_id = $this->configPagesLoader->getValue('stanford_basic_site_settings', 'su_site_algolia_file', 0, 'target_id');
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
