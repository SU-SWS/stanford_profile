<?php

namespace Drupal\stanford_intranet\Config;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ConfigFactoryOverrideInterface;
use Drupal\Core\Config\StorageInterface;
use Drupal\Core\State\StateInterface;

/**
 * Class ConfigOverrider.
 *
 * @package Drupal\stanford_intranet\Config
 */
class ConfigOverrider implements ConfigFactoryOverrideInterface {

  /**
   * Config factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Core state service.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * ConfigOverrider constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   Config factory service.
   * @param \Drupal\Core\State\StateInterface $state
   *   Core state service.
   */
  public function __construct(ConfigFactoryInterface $config_factory, StateInterface $state) {
    $this->configFactory = $config_factory;
    $this->state = $state;
  }

  /**
   * {@inheritDoc}
   */
  public function loadOverrides($names) {
    $overrides = [];

    if (!$this->state->get('stanford_intranet', FALSE)) {
      return $overrides;
    }

    foreach ($names as $name) {
      if (strpos($name, 'field.storage.') === 0) {
        $scheme = $this->configFactory->getEditable($name)
          ->getOriginal('settings.uri_scheme', FALSE);
        // If the field isn't an file or image field, it won't have a upload
        // scheme.
        if ($scheme == 'public') {
          $overrides[$name]['settings']['uri_scheme'] = 'private';
        }
      }
    }

    return $overrides;
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
    return 'stanford_intranet';
  }

  /**
   * {@inheritDoc}
   */
  public function getCacheableMetadata($name) {
    return new CacheableMetadata();
  }

}
