<?php

/**
 * @file
 * stanford_profile.install
 */

use Drupal\Core\Site\Settings;
use Drupal\Core\Config\FileStorage;

/**
 * Implements hook_removed_post_updates().
 */
function stanford_profile_removed_post_updates() {
  return [
    'stanford_profile_post_update_8001' => '8.x-1.13',
    'stanford_profile_post_update_8003' => '8.x-1.13',
    'stanford_profile_post_update_8013' => '8.x-1.13',
  ];
}

/**
 * Send out notification message about events and person importer.
 */
function stanford_profile_post_update_8014() {
  \Drupal::service('module_installer')->install(['stanford_notifications']);
  /** @var \Drupal\stanford_notifications\NotificationServiceInterface $notifications */
  $notifications = \Drupal::service('notification_service');

  $message = 'New: You can now create "Events" content. See <a href="https://userguide.sites.stanford.edu/tour/events">the user guide</a> for more information';
  $notifications->addNotification($message, [
    'site_manager',
    'site_editor',
    'contributor',
  ]);

  $message = 'New: You can now import "Person" content from Stanford Who. See <a href="https://userguide.sites.stanford.edu/tour/person/person-importer">the user guide</a> for more information';
  $notifications->addNotification($message, [
    'site_manager',
    'site_editor',
    'contributor',
  ]);

  $message = 'Update: We changed some things about how buttons are aligned. See <a href="https://userguide.sites.stanford.edu/tour/paragraphs/text-area/working-with-buttons">the user guide page</a> for more information';
  $notifications->addNotification($message, [
    'site_manager',
    'site_editor',
    'contributor',
  ]);

}

/**
 * Create blocks for sites with custom themes that were added to stanford_basic.
 */
function stanford_profile_post_update_8015() {
  $theme_name = \Drupal::config('system.theme')->get('default');
  // Default theme is good. Just end if so.
  if ($theme_name == "stanford_basic") {
    return;
  }

  // Not stanford_basic, we have to create two config page blocks.
  // Copy the blocks from stanford_basic and rename them.
  //
  // Names of things.
  $basic_global_name = 'block.block.stanford_basic_config_pages_stanford_global_msg';
  $basic_super_name = 'block.block.stanford_basic_config_pages_stanford_super_footer';
  $my_global_name = 'block.block.' . $theme_name . '_config_pages_stanford_global_msg';
  $my_super_name = 'block.block.' . $theme_name . '_config_pages_stanford_super_footer';

  // Resources.
  $config_path = Settings::get('config_sync_directory');
  $source = new FileStorage($config_path);
  $config_storage = \Drupal::service('config.storage');
  $config_factory = \Drupal::service('config.factory');

  // Get the configuration out of the filesystem as it may not have been imported
  // yet...
  $basic_global_config = $config_factory
    ->getEditable($my_global_name)
    ->setData(
      $source->read($basic_global_name)
    );
  $basic_super_config = $config_factory
    ->getEditable($my_super_name)
    ->setData(
      $source->read($basic_super_name)
    );

  // Change a few ids.
  $basic_global_config->set('id', $theme_name . '_config_pages_stanford_global_msg');
  $basic_super_config->set('id', $theme_name . '_config_pages_super_global_msg');
  $basic_global_config->set('theme', $theme_name);
  $basic_super_config->set('theme', $theme_name);
  $basic_global_config->set('dependencies.theme', [$theme_name]);
  $basic_super_config->set('dependencies.theme', [$theme_name]);

  // Remove the UUID.
  $basic_global_config->clear('uuid');
  $basic_super_config->clear('uuid');

  // Add it to the DB.
  $basic_global_config->save();
  $basic_super_config->save();
}
