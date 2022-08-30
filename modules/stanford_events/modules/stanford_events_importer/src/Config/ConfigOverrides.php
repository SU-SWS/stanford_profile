<?php

namespace Drupal\stanford_events_importer\Config;

use Drupal\config_pages\ConfigPagesLoaderServiceInterface;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Config\ConfigFactoryOverrideInterface;
use Drupal\Core\Config\StorageInterface;

/**
 * Configuration overrides for events importer migration entity.
 */
class ConfigOverrides implements ConfigFactoryOverrideInterface {

  /**
   * Config pages loader service.
   *
   * @var \Drupal\config_pages\ConfigPagesLoaderServiceInterface
   */
  protected $configPages;

  /**
   * ConfigOverrides constructor.
   *
   * @param \Drupal\config_pages\ConfigPagesLoaderServiceInterface $config_pages
   *   Config pages loader service.
   */
  public function __construct(ConfigPagesLoaderServiceInterface $config_pages) {
    $this->configPages = $config_pages;
  }

  /**
   * {@inheritDoc}
   */
  public function createConfigObject($name, $collection = StorageInterface::DEFAULT_COLLECTION) {
    return NULL;
  }

  /**
   * {@inheritDoc}
   */
  public function getCacheSuffix() {
    return 'StanfordPersonImporterConfigOverride';
  }

  /**
   * {@inheritDoc}
   */
  public function getCacheableMetadata($name) {
    return new CacheableMetadata();
  }

  /**
   * {@inheritDoc}
   */
  public function loadOverrides($names) {
    $overrides = [];
    if (in_array('migrate_plus.migration.stanford_localist_importer', $names)) {
      $bookmark_urls = $this->configPages->getValue('stanford_events_importer', 'su_localist_bookmark', [], 'uri');
      $feed_urls = $this->configPages->getValue('stanford_events_importer', 'su_localist_url', [], 'uri');
      $urls = [...$bookmark_urls, ...$feed_urls];
      asort($urls);
      $overrides['migrate_plus.migration.stanford_localist_importer']['source']['urls'] = array_values($urls);
    }
    return $overrides;
  }

}
