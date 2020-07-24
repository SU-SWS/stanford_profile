<?php

namespace Drupal\stanford_profile\Config;

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
   * @param \Drupal\config_pages\ConfigPagesLoaderServiceInterface|null $config_pages_loader
   *   Config pages service.
   * @param \Drupal\Core\Config\ConfigFactoryInterface|null $config_factory
   *   Config factory service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface|null $entity_type_manager
   *   Entity type manager interface.
   * @param \Drupal\Core\StreamWrapper\StreamWrapperManagerInterface|null $stream_wrapper_manager
   *   Stream wrapper manager interface.
   */
  public function __construct(
    StateInterface $state,
    ConfigPagesLoaderServiceInterface $config_pages_loader = NULL,
    ConfigFactoryInterface $config_factory = NULL,
    EntityTypeManagerInterface $entity_type_manager = NULL,
    StreamWrapperManagerInterface $stream_wrapper_manager = NULL
  ) {
    $this->state = $state;

    if ($config_pages_loader) {
      $this->configPagesLoader = $config_pages_loader;
    }

    if ($config_factory) {
      $this->configFactory = $config_factory;
    }

    if ($entity_type_manager) {
      $this->entityTypeManager = $entity_type_manager;
    }

    if ($stream_wrapper_manager) {
      $this->streamWrapperManager = $stream_wrapper_manager;
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
    }

    // Theme settings override.
    $this->setLockupOverrides($names, $overrides);

    // GTM overrides.
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

    // Failed to load the config page for some reason.
    if (!$config_page) {
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
    $overrides[$theme_name . '.settings']['lockup']['option'] = $config_page->get('su_lockup_options')->getString();
    $overrides[$theme_name . '.settings']['lockup']['line1'] = $config_page->get('su_line_1')->getString();
    $overrides[$theme_name . '.settings']['lockup']['line2'] = $config_page->get('su_line_2')->getString();
    $overrides[$theme_name . '.settings']['lockup']['line3'] = $config_page->get('su_line_3')->getString();
    $overrides[$theme_name . '.settings']['lockup']['line4'] = $config_page->get('su_line_4')->getString();
    $overrides[$theme_name . '.settings']['lockup']['line5'] = $config_page->get('su_line_5')->getString();
    $overrides[$theme_name . '.settings']['use_logo'] = ($config_page->get('su_use_theme_logo')->getString() == "1") ? TRUE : FALSE;
    $overrides[$theme_name . '.settings']['logo']['use_default'] = $overrides[$theme_name . '.settings']['use_logo'];
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
    $file_field = $config_page->get('su_upload_logo_image')->first();

    if (!$file_field) {
      return;
    }

    $fid = $file_field->getValue()['target_id'];
    if (!$fid) {
      return;
    }

    $file = $this->entityTypeManager->getStorage('file')->load($fid);
    $file_uri = $file->getFileUri();
    $wrapper = $this->streamWrapperManager->getViaUri($file_uri);
    $file_path = $wrapper->getExternalUrl();

    $overrides[$theme_name . '.settings']['logo']['path'] = $file_path;
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
    $ah_env = $_ENV['AH_SITE_ENVIRONMENT'] ?? NULL;
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
