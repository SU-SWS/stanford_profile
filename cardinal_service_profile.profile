<?php

/**
 * @file
 * cardinal_service_profile.profile
 */

require_once 'cardinal_service_profile.inc';

/**
 * Implements hook_install_tasks().
 */
function cardinal_service_profile_install_tasks(&$install_state) {
  return ['cardinal_service_profile_final_task' => []];
}

/**
 * Perform final tasks after the profile has completed installing.
 *
 * @param array $install_state
 *   Current install state.
 */
function cardinal_service_profile_final_task(array &$install_state) {
  \Drupal::service('plugin.manager.install_tasks')->runTasks($install_state);
}
