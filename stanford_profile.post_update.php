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
    'stanford_profile_post_update_event_pages' => '12.0.0',
    'stanford_profile_post_update_header_links_block' =>'12.0.0',
    'stanford_profile_post_update_unpublished_site_banner' =>'12.0.0',
  ];
}

/**
 * Create new rabbit hole message block for subthemes.
 */
function stanford_profile_post_update_rabbit_hole_block() {
  $theme = \Drupal::config('system.theme')->get('default');
  if (in_array($theme, ['stanford_basic', 'minimally_branded_subtheme'])) {
    return;
  }
  \Drupal::entityTypeManager()->getStorage('block')->create([
    'id' => "{$theme}_rabbit_hole_message",
    'theme' => $theme,
    'region' => 'content',
    'weight' => -10,
    'plugin' => 'rabbit_hole_message',
    'settings' => [
      'id' => 'rabbit_hole_message',
      'label' => 'Rabbit Hole Message',
      'label_display' => 0,
      'provider' => 'stanford_profile_helper',
      'context_mapping' => ['node' => '@node.node_route_context:node'],
    ],
    'visibility' => [
      'user_role' => [
        'id' => 'user_role',
        'negate' => TRUE,
        'context_mapping' => ['user' => '@user.current_user_context:current_user'],
        'roles' => ['anonymous' => 'anonymous'],
      ],
    ],
  ])->save();
}
