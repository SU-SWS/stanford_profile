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
    'stanford_profile_post_update_8014' => '8.x-2.9',
    'stanford_profile_post_update_8015' => '8.x-2.9',
    'stanford_profile_post_update_8200' => '11.4.0',
    'stanford_profile_post_update_8201' => '11.4.0',
    'stanford_profile_post_update_8202' => '11.4.0',
    'stanford_profile_post_update_update_field_defs' => '11.4.0',
    'stanford_profile_post_update_samlauth' => '11.4.0',
    'stanford_profile_post_update_site_orgs' => '11.4.0',
  ];
}

/**
 * Create default past event and event series node pages if content exists.
 */
function stanford_profile_post_update_event_pages() {
  $node_storage = \Drupal::entityTypeManager()->getStorage('node');
  $events = $node_storage->getQuery()
    ->accessCheck(FALSE)
    ->condition('type', 'stanford_event')
    ->count()
    ->execute();

  $default_content_creator = \Drupal::service('stanford_profile_helper.default_content');
  if ($events) {
    $default_content_creator->createDefaultContent('86a411a2-0b05-41bc-ae15-2184b8e81ea4');
  }
  $event_series = $node_storage->getQuery()
    ->accessCheck(FALSE)
    ->condition('type', 'stanford_event-series')
    ->count()
    ->execute();
  if ($event_series) {
    $default_content_creator->createDefaultContent('ddd5aefb-6b7a-4cd7-aa72-e8c106598bb6');
  }
}
