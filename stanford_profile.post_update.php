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
 * Update the new config_ignore settings prior to config import.
 */
function stanford_profile_post_update_8015() {
  $config_name = "config_ignore.settings";
  $config_path = Settings::get('config_sync_directory');
  $source = new FileStorage($config_path);
  $config_storage = \Drupal::service('config.storage');
  $config_storage->write($config_name, $source->read($config_name));
}
