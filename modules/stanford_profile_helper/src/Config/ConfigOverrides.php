<?php

namespace Drupal\stanford_profile_helper\Config;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ConfigFactoryOverrideInterface;
use Drupal\Core\Config\StorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\State\StateInterface;
use Drupal\config_pages\ConfigPagesLoaderServiceInterface;
use Drupal\config_pages\ConfigPagesInterface;
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
  public function __construct(
    StateInterface $state,
    ConfigPagesLoaderServiceInterface $config_pages_loader,
    ConfigFactoryInterface $config_factory,
    EntityTypeManagerInterface $entity_type_manager,
    StreamWrapperManagerInterface $stream_wrapper_manager
  ) {
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

    // Avoid circular loops.
    if (!$this->configFactory || in_array('system.theme', $names)) {
      return;
    }

    // Validate we are working with the info we need.
    $theme_info = $this->configFactory->get('system.theme');
    if (!$this->configPagesLoader || !$theme_info || !in_array($theme_info->get('default') . '.settings', $names)) {
      return;
    }

    // Get the default theme from the config.
    $theme_name = $theme_info->get('default');
    $config_page = $this->configPagesLoader->load('lockup_settings');

    // Failed to load the config page or not enabled.
    if (!$config_page || $config_page->get('su_lockup_enabled')->getString() == "1") {
      return;
    }

    // Do the overrides.
    $this->setLockupTextOverrides($overrides, $theme_name, $config_page);

    // Get and set a file path that is relative to the site base dir.
    $this->setLockupFileOverrides($overrides, $theme_name, $config_page);

  }

  /**
   * Set the lockup text overrides.
   *
   * @param array $overrides
   *   The array of overrides.
   * @param string $theme_name
   *   The name of the default theme.
   * @param \Drupal\config_pages\ConfigPagesInterface $config_page
   *   A config page object.
   */
  protected function setLockupTextOverrides(array &$overrides, $theme_name, ConfigPagesInterface $config_page) {
    $overrides[$theme_name . '.settings'] = [
      'lockup' => [
        'option' => $config_page->get('su_lockup_options')->getString(),
        'line1' => $config_page->get('su_line_1')->getString(),
        'line2' => $config_page->get('su_line_2')->getString(),
        'line3' => $config_page->get('su_line_3')->getString(),
        'line4' => $config_page->get('su_line_4')->getString(),
        'line5' => $config_page->get('su_line_5')->getString(),
      ],
      'logo' => [
        'use_default' => ($config_page->get('su_use_theme_logo')->getString() == "1") ? TRUE : FALSE,
      ],
    ];
  }

  /**
   * Set the lockup file/logo overrides.
   *
   * @param array $overrides
   *   The array of overrides.
   * @param string $theme_name
   *   The name of the default theme.
   * @param \Drupal\config_pages\ConfigPagesInterface $config_page
   *   A config page object.
   */
  protected function setLockupFileOverrides(array &$overrides, $theme_name, ConfigPagesInterface $config_page) {

    $file_field = $config_page->get('su_upload_logo_image')->getValue();
    if (empty($file_field[0]['target_id'])) {
      return;
    }

    $file = $this->entityTypeManager->getStorage('file')->load($file_field[0]['target_id']);
    if (!$file) {
      return;
    }

    $file_uri = $file->getFileUri();
    $wrapper = $this->streamWrapperManager->getViaUri($file_uri);
    $file_path = $wrapper->getExternalUrl();

    $overrides[$theme_name . '.settings']['logo']['path'] = $file_path;
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
