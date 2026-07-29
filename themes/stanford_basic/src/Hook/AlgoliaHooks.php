<?php

declare(strict_types=1);

namespace Drupal\stanford_basic\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Render\Element;
use Drupal\Core\StringTranslation\StringTranslationTrait;

class AlgoliaHooks {

  use StringTranslationTrait;

  /**
   * Cached algolia settings, keyed to avoid repeat config_pages lookups.
   *
   * @var array|bool|null
   */
  protected static array|bool|null $algoliaSettings = NULL;

  /**
   * Implements hook_preprocess_HOOK().
   */
  #[Hook('preprocess_page__search')]
  public function preprocessPageSearch(&$variables): void {
    $algolia_settings = self::getAlgoliaSettings();
    if (!$algolia_settings) {
      return;
    }
    $variables['#attached']['library'][] = 'stanford_basic/algolia-search';
    $variables['#attached']['drupalSettings']['stanfordAlgolia'] = $algolia_settings;

    foreach (Element::children($variables['page']['content']) as $child_key) {
      $element = &$variables['page']['content'][$child_key];
      if (isset($element['#plugin_id']) && $element['#plugin_id'] == 'system_main_block') {
        $variables['page']['content'][$child_key] = [
          '#type' => 'container',
          'noscript' => [
            '#type' => 'html_tag',
            '#tag' => 'noscript',
            '#value' => $this->t('Enable Javascript to view search results.'),
          ],
          '#attributes' => [
            'id' => 'algolia-search',
            'class' => ['centered-container'],
          ],
        ];
      }
    }
  }

  /**
   * Get the user entered Algolia search settings.
   *
   * @return array|bool
   *   Keyed array of algolia settings, false if not configured.
   */
  public static function getAlgoliaSettings(): array|bool {
    if (self::$algoliaSettings !== NULL) {
      return self::$algoliaSettings;
    }

    if (!\Drupal::hasService('config_pages.loader')) {
      return self::$algoliaSettings = FALSE;
    }
    /** @var \Drupal\config_pages\ConfigPagesLoaderServiceInterface $config_page */
    $config_page = \Drupal::service('config_pages.loader');

    $use_algolia = (bool) $config_page->getValue('stanford_basic_site_settings', 'su_site_algolia_ui', 0, 'value');
    if (!$use_algolia) {
      return self::$algoliaSettings = FALSE;
    }
    $algolia_app = $config_page->getValue('stanford_basic_site_settings', 'su_site_algolia_id', 0, 'value');
    $algolia_search_key = $config_page->getValue('stanford_basic_site_settings', 'su_site_algolia_search', 0, 'value');
    $search_index = $config_page->getValue('stanford_basic_site_settings', 'su_site_algolia_index', 0, 'value');
    $federated = (bool) $config_page->getValue('stanford_basic_site_settings', 'su_site_algolia_fed', 0, 'value');

    return self::$algoliaSettings = [
      'appId' => $algolia_app,
      'searchKey' => $algolia_search_key,
      'index' => $search_index,
      'federatedSearch' => $federated,
    ];
  }

}
