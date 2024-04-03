<?php

/**
 * @file
 * cardinal_service_profile.profile
 */

use Drupal\config_pages\ConfigPagesInterface;
use Drupal\Core\Installer\InstallerKernel;

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

/**
 * Implements hook_ENTITY_TYPE_presave().
 */
function cardinal_service_profile_config_pages_presave(ConfigPagesInterface $config_page) {
  // During install, rebuild the router when saving a config page. This prevents
  // an error if the config page route doesn't exist for it yet. Event
  // subscriber doesn't work for this since it's during installation.
  if (InstallerKernel::installationAttempted()) {
    \Drupal::service('router.builder')->rebuild();
  }
}
