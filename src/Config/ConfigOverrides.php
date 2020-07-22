<?php

namespace Drupal\stanford_profile\Config;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ConfigFactoryOverrideInterface;
use Drupal\Core\Config\StorageInterface;
use Drupal\Core\State\StateInterface;
use Drupal\config_pages\ConfigPagesLoaderServiceInterface;
use Drupal\file\Entity\File;

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
   * ConfigOverrides constructor.
   *
   * @param \Drupal\Core\State\StateInterface $state
   *   State service.
   * @param \Drupal\config_pages\ConfigPagesLoaderServiceInterface $config_pages_loader
   *   Config pages service.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   Config factory service.
   */
  public function __construct(StateInterface $state, ConfigPagesLoaderServiceInterface $config_pages_loader = NULL, ConfigFactoryInterface $config_factory = NULL) {
    $this->state = $state;

    if ($config_pages_loader) {
      $this->configPagesLoader = $config_pages_loader;
    }

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
    }

    // Theme settings override.
    if ($this->configFactory && !in_array('system.theme', $names)) {
      $theme_info = $this->configFactory->get('system.theme');
      // Active default theme.
      if ($this->configPagesLoader && in_array($theme_info->get('default') . '.settings', $names)) {
        $theme_name = $theme_info->get('default');
        $config_page = $this->configPagesLoader->load('lockup_settings');

        // Do the overrides.
        if ($config_page) {
          $overrides[$theme_name . '.settings']['lockup']['option'] = $config_page->get('su_lockup_options')->getString();
          $overrides[$theme_name . '.settings']['lockup']['line1'] = $config_page->get('su_line_1')->getString();
          $overrides[$theme_name . '.settings']['lockup']['line2'] = $config_page->get('su_line_2')->getString();
          $overrides[$theme_name . '.settings']['lockup']['line3'] = $config_page->get('su_line_3')->getString();
          $overrides[$theme_name . '.settings']['lockup']['line4'] = $config_page->get('su_line_4')->getString();
          $overrides[$theme_name . '.settings']['lockup']['line5'] = $config_page->get('su_line_5')->getString();
          $overrides[$theme_name . '.settings']['use_logo'] = ($config_page->get('su_use_theme_logo')->getString() == "1") ? TRUE : FALSE;

          // If the file upload is available we need to change the path to
          // a relative path to the files directory.
          $fid = $config_page->get('su_upload_logo_image')->first()->getValue()['target_id'];
          if ($fid) {
            $file_uri = File::load($fid)->getFileUri();
            $file_path = file_url_transform_relative(file_create_url($file_uri));
            $overrides[$theme_name . '.settings']['logo']['use_default'] = $overrides[$theme_name . '.settings']['use_logo'];
            $overrides[$theme_name . '.settings']['logo']['path'] = $file_path;
          }
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
    return 'StanfordProfileConfigOverride';
  }

  /**
   * {@inheritDoc}
   */
  public function getCacheableMetadata($name) {
    return new CacheableMetadata();
  }

}
