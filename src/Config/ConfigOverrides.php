<?php

namespace Drupal\stanford_profile\Config;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ConfigFactoryOverrideInterface;
use Drupal\Core\Config\StorageInterface;
use Drupal\Core\Installer\InstallerKernel;
use Drupal\Core\State\StateInterface;

/**
 * Config overrides for stanford profile.
 *
 * @package Drupal\stanford_profile\Config
 */
class ConfigOverrides implements ConfigFactoryOverrideInterface {

  /**
   * State service.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * Config factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * ConfigOverrides constructor.
   *
   * @param \Drupal\Core\State\StateInterface $state
   *   State service.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   Config factory service.
   */
  public function __construct(StateInterface $state, ConfigFactoryInterface $config_factory = NULL) {
    $this->state = $state;
    if ($config_factory) {
      $this->configFactory = $config_factory;
    }
  }

  /**
   * {@inheritDoc}
   */
  public function loadOverrides($names) {
    $overrides = [];
    if (in_array('system.site', $names)) {
      $overrides['system.site']['page'] = [
        403 => $this->state->get('stanford_profile.403_page'),
        404 => $this->state->get('stanford_profile.404_page'),
        'front' => $this->state->get('stanford_profile.front_page'),
      ];
    }

    // Add to the config ignore so that it will ignore the current theme's
    // settings config.
    if (in_array('config_ignore.settings', $names) && $this->configFactory) {
      // We have to get the original values to add to the array.
      $existing_ignored = $this->configFactory->getEditable('config_ignore.settings')
        ->getOriginal('ignored_config_entities', FALSE);
      $themes = $this->configFactory->getEditable('core.extension')
        ->getOriginal('theme');
      foreach (array_keys($themes) as $theme_name) {
        $existing_ignored[] = "$theme_name.settings";
      }
      $overrides['config_ignore.settings']['ignored_config_entities'] = $existing_ignored;

      // When installing a site, we don't want to ignore any configs.
      if (InstallerKernel::installationAttempted()) {
        foreach ($overrides['config_ignore.settings']['ignored_config_entities'] as &$ignored) {
          $ignored = 'foo';
        }
      }
    }
    $this->setOverridesGoogleTag($names, $overrides);

    return $overrides;
  }

  /**
   * Disable google tag manager entities when not on prod environment.
   *
   * @param array $names
   *   Config names.
   * @param array $overrides
   *   Keyed array of config overrides.
   */
  protected function setOverridesGoogleTag(array $names, array &$overrides) {
    if ($this->isProdEnv()) {
      return;
    }

    foreach ($names as $name) {
      if (strpos($name, 'google_tag.container.') === 0) {
        $overrides[$name]['status'] = FALSE;
      }
    }
  }

  /**
   * Check if this is Acquia's prod environment.
   *
   * @return bool
   *   Is Acquia environment.
   */
  protected function isProdEnv() {
    $ah_env = $_ENV['AH_SITE_ENVIRONMENT'] ?? '';
    return $ah_env == 'prod' || preg_match('/^\d*live$/', $ah_env);
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
    return 'StanfordProfileConfigOverride';
  }

  /**
   * {@inheritDoc}
   */
  public function getCacheableMetadata($name) {
    return new CacheableMetadata();
  }

}
