<?php

namespace Drupal\stanford_profile_config_overrides\Config;

use Drupal\config_pages\ConfigPagesLoaderServiceInterface;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Config\ConfigFactoryOverrideInterface;
use Drupal\Core\Config\StorageInterface;

/**
 * Configuration overrides for the profile.
 *
 * @package Drupal\stanford_profile_config_overrides\Config
 */
class ConfigOverrides implements ConfigFactoryOverrideInterface {

  /**
   * @var \Drupal\config_pages\ConfigPagesLoaderServiceInterface
   */
  protected $configPages;

  public function __construct(ConfigPagesLoaderServiceInterface $config_pages) {
    $this->configPages = $config_pages;
  }

  /**
   * {@inheritDoc}
   */
  public function loadOverrides($names) {
    if (in_array('domain_301_redirect.settings', $names)) {
      $site_url = $this->configPages->getValue('stanford_basic_site_settings', 'su_site_url', 0, 'uri');

      return [
        'domain_301_redirect.settings' => [
          'enabled' => !empty($site_url),
          'domain' => str_replace('http://', 'https://', $site_url),
        ],
      ];
    }
    return [];
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
  public function getCacheableMetadata($name) {
    return new CacheableMetadata();
  }

  /**
   * {@inheritDoc}
   */
  public function getCacheSuffix() {
    return 'ConfigOverrides';
  }

}
