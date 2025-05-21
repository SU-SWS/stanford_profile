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

/**
 * Create new header link block for the active theme.
 */
function stanford_profile_post_update_header_links_block() {
  $theme = \Drupal::config('system.theme')->get('default');
  if (in_array($theme, [
    'stanford_basic',
    'minimally_branded_subtheme',
    'stanford_profile_admin_theme',
  ])) {
    return;
  }
  \Drupal::entityTypeManager()->getStorage('block')->create([
    'id' => "{$theme}_header_links",
    'theme' => $theme,
    'region' => 'search',
    'plugin' => 'config_pages_block',
    'weight' => -6,
    'provider' => NULL,
    'settings' => [
      'id' => 'config_pages_block',
      'label' => 'Site Header Links',
      'label_display' => '0',
      'provider' => 'config_pages',
      'config_page_type' => 'stanford_basic_site_settings',
      'config_page_view_mode' => 'site_settings_header',
    ],
  ])->save();
}

/**
 * Create new header link block for the active theme.
 */
function stanford_profile_post_update_unpublished_site_banner() {
  $theme = \Drupal::config('system.theme')->get('default');
  if (in_array($theme, [
    'stanford_basic',
    'minimally_branded_subtheme',
    'stanford_profile_admin_theme',
  ])) {
    return;
  }
  \Drupal::entityTypeManager()->getStorage('block')->create([
    'id' => "{$theme}_unpublished_site",
    'theme' => $theme,
    'region' => 'content',
    'plugin' => 'simple_block:su_unpublished_site_banner',
    'weight' => -10,
    'provider' => NULL,
    'settings' => [
      'id' => 'simple_block:su_unpublished_site_banner',
      'label' => 'Unpublished Site Banner',
      'label_display' => '0',
      'provider' => 'simple_block',
    ],
    'visibility' => [
      'config_pages_values_access' => [
        'id' => 'config_pages_values_access',
        'negate' => FALSE,
        'config_page_field' => 'stanford_basic_site_settings|su_site_type|list_string',
        'operator' => '==',
        'condition_value' => 'pre_production',
      ],
      'request_path' => [
        'id' => 'request_path',
        'negate' => TRUE,
        'pages' => '/user/*',
      ],
    ],
  ])->save();
}
