<?php

/**
 * @file
 * cardinal_service_profile.install
 */

/**
 * Implements hook_removed_post_updates().
 */
function cardinal_service_profile_removed_post_updates() {
  return [
    'cardinal_service_profile_post_update_spotlight' => '8.x-2.1',
    'cardinal_service_profile_post_update_csd_233' => '8.x-2.1',
  ];
}
