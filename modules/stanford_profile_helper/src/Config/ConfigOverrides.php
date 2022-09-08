<?php

namespace Drupal\stanford_profile_helper\Config;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ConfigFactoryOverrideInterface;
use Drupal\Core\Config\StorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\State\StateInterface;
use Drupal\config_pages\ConfigPagesLoaderServiceInterface;
use Drupal\Core\StreamWrapper\StreamWrapperManagerInterface;

/**
 * Config overrides for stanford profile.
 *
 * @package Drupal\stanford_profile_helper\Config
 */
class ConfigOverrides implements ConfigFactoryOverrideInterface {

  /**
   * State service.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * Config pages loader service.
   *
   * @var \Drupal\config_pages\ConfigPagesLoaderServiceInterface
   */
  protected $configPagesLoader;

  /**
   * Config factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Config Entity Type Manager Interface.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * StreamWrapperInterface service.
   *
   * @var \Drupal\Core\StreamWrapper\StreamWrapperManagerInterface
   */
  protected $streamWrapperManager;

  /**
   * ConfigOverrides constructor.
   *
   * @param \Drupal\Core\State\StateInterface $state
   *   State service.
   * @param \Drupal\config_pages\ConfigPagesLoaderServiceInterface $config_pages_loader
   *   Config pages service.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   Config factory service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Entity type manager interface.
   * @param \Drupal\Core\StreamWrapper\StreamWrapperManagerInterface $stream_wrapper_manager
   *   Stream wrapper manager interface.
   */
  public function __construct(StateInterface $state, ConfigPagesLoaderServiceInterface $config_pages_loader, ConfigFactoryInterface $config_factory, EntityTypeManagerInterface $entity_type_manager, StreamWrapperManagerInterface $stream_wrapper_manager) {
    $this->state = $state;
    $this->configPagesLoader = $config_pages_loader;
    $this->configFactory = $config_factory;
    $this->entityTypeManager = $entity_type_manager;
    $this->streamWrapperManager = $stream_wrapper_manager;
  }

  /**
   * {@inheritDoc}
   */
  public function loadOverrides($names) {
    $overrides = [];

    // Theme settings override.
    $this->setLockupOverrides($names, $overrides);
    $this->setRolePermissionOverrides($names, $overrides);
    $this->setMainMenuOverrides($names, $overrides);
    // Overrides.
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
  protected function setLockupOverrides(array $names, array &$overrides) {
    // Validate we are working with the info we need.
    $default_theme = $this->configFactory->getEditable('system.theme')
      ->getOriginal('default', FALSE);

    if (!in_array("$default_theme.settings", $names)) {
      return;
    }

    // The lockup isn't enabled, so bail out.
    if (!$this->configPagesLoader->getValue('lockup_settings', 'su_lockup_enabled', 0, 'value')) {
      return;
    }

    // Override the lockup settings
    if ($lockup_overrides = $this->getLockupTextOverrides()) {
      $overrides[$default_theme . '.settings'] = $lockup_overrides;
    }

    // Get and set a file path that is relative to the site base dir.
    if ($logo = $this->getLogoUrl()) {
      $overrides[$default_theme . '.settings']['logo']['path'] = $logo;
    }
  }

  /**
   * Get the lockup text overrides.
   */
  protected function getLockupTextOverrides() {
    $overrides = [
      'lockup' => [
        'option' => $this->configPagesLoader->getValue('lockup_settings', 'su_lockup_options', 0, 'value'),
        'line1' => $this->configPagesLoader->getValue('lockup_settings', 'su_line_1', 0, 'value'),
        'line2' => $this->configPagesLoader->getValue('lockup_settings', 'su_line_2', 0, 'value'),
        'line3' => $this->configPagesLoader->getValue('lockup_settings', 'su_line_3', 0, 'value'),
        'line4' => $this->configPagesLoader->getValue('lockup_settings', 'su_line_4', 0, 'value'),
        'line5' => $this->configPagesLoader->getValue('lockup_settings', 'su_line_5', 0, 'value'),
      ],
      'logo' => [
        'use_default' => (bool) $this->configPagesLoader->getValue('lockup_settings', 'su_use_theme_logo', 0, 'value'),
      ],
    ];
    $overrides['lockup'] = array_filter($overrides['lockup']);
    $overrides['logo'] = array_filter($overrides['logo']);
    return array_filter($overrides);
  }

  /**
   * Get the lockup file/logo overrides.
   */
  protected function getLogoUrl(): ?string {

    $file_id = $this->configPagesLoader->getValue('lockup_settings', 'su_upload_logo_image', 0, 'target_id');
    if (!$file_id) {
      return NULL;
    }

    $file = $this->entityTypeManager->getStorage('file')->load($file_id);
    if (!$file) {
      return NULL;
    }

    $file_uri = $file->getFileUri();
    $wrapper = $this->streamWrapperManager->getViaUri($file_uri);
    return $wrapper->getExternalUrl();
  }

  /**
   * Add permissions to the role configs.
   *
   * @param array $names
   *   Array of config names.
   * @param array $overrides
   *   Keyed array of config overrides.
   */
  protected function setRolePermissionOverrides(array $names, array &$overrides) {
    if (in_array('user.role.site_manager', $names)) {
      // Arbitrary number that should be larger than the original permission
      // count. This allows the functionality to ADD permissions but not have
      // any affect on existing permissions. If we don't have this number high
      // enough, it will replace permissions instead of adding them.
      $counter = 500;
      foreach (array_keys($this->state->get('stanford_intranet.rids', [])) as $role_id) {
        // We only care about the custom roles.
        if (strpos($role_id, 'custm_') === FALSE) {
          continue;
        }
        $overrides['user.role.site_manager']['permissions'][$counter] = "assign $role_id role";
        $counter++;
      }
    }
  }

  /**
   * Add main menu config overrides.
   *
   * @param array $names
   *   Array of config names.
   * @param array $overrides
   *   Keyed array of config overrides.
   */
  protected function setMainMenuOverrides(array $names, array &$overrides) {
    foreach ($names as $name) {
      if (str_starts_with($name, 'block.block.')) {
        $block_plugin = $this->configFactory->getEditable($name)
          ->getOriginal('plugin', FALSE);
        $region = $this->configFactory->getEditable($name)
          ->getOriginal('region', FALSE);

        if ($block_plugin == 'system_menu_block:main' && $region == 'menu') {
          $menu_depth = (int) $this->configPagesLoader->getValue('stanford_basic_site_settings', 'su_site_menu_levels', 0, 'value');
          if ($menu_depth >= 1) {
            $overrides[$name]['settings']['depth'] = $menu_depth;
          }
        }
      }
    }
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
    return 'StanfordProfileHelperConfigOverride';
  }

  /**
   * {@inheritDoc}
   */
  public function getCacheableMetadata($name) {
    return new CacheableMetadata();
  }

}
