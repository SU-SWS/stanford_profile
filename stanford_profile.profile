<?php

/**
 * @file
 * stanford_profile.profile
 */

/**
 * Implements hook_install_tasks().
 */
function stanford_profile_install_tasks(&$install_state) {
  return ['stanford_profile_final_task' => []];
}

/**
 * Perform final tasks after the profile has completed installing.
 *
 * @param array $install_state
 *   Current install state.
 */
function stanford_profile_final_task(array &$install_state) {
  \Drupal::service('plugin.manager.install_tasks')->runTasks($install_state);
}

function mytest() {
  $module = 'stanford_person';
  $path = drupal_get_path('module', $module);
  $info = \Drupal\Core\Serialization\Yaml::decode(file_get_contents("$path/$module.info.yml"));
  $missing = [];
  foreach (glob("$path/content/*") as $default_content) {
    $entity_type = basename($default_content);
    foreach (glob("$path/content/$entity_type/*.json") as $file) {
      $uuid = basename($file, '.json');

      if (!isset($info['default_content'][$entity_type]) || !in_array($uuid, $info['default_content'][$entity_type])) {
        $info['default_content'][$entity_type][] = $uuid;
      }
    }
  }
  dpm(\Drupal\Core\Serialization\Yaml::encode($info['default_content']));
}
