<?php

/**
 * @file
 * stanford_profile.install
 */

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
function cardinal_service_profile_post_update_8013() {
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

  $database = \Drupal::database();
  $query = $database->select('node_revision__su_page_components', 'nrspc');
  $query->condition('nrspc.su_page_components_settings', '%"index":3%', 'LIKE');
  $num_rows = $query->countQuery()->execute()->fetchField();
  if ($num_rows >= 1) {
    $message = 'Update: All Paragraphs within a page will now support a maximum of 3 items per row where some allowed 4 items before. Any existing layouts with 4 items in a row will be grandfathered in but new content will be limited to 3 items per row.';
    $notifications->addNotification($message, [
      'site_manager',
      'site_editor',
      'contributor',
    ]);
  }
}
