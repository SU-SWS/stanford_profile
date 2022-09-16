<?php

/**
 * @file
 * stanford_profile.profile
 */

use Drupal\Core\Site\Settings;

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

function mikes() {
  $api_url = Settings::get('stanford_profile_snow_api_url', 'https://stanford.service-now.com/api/stu/su_acsf_site_requester_information/requestor');
  $site_name = 'metrics20220514';
//  $site_name = 'default';
  $response = \Drupal::httpClient()->request('GET', $api_url, [
    'query' => ['website_address' => $site_name],
    'auth' => [
      Settings::get('stanford_profile_snow_api_user'),
      Settings::get('stanford_profile_snow_api_pass'),
    ],
  ]);

dpm((string) $response->getBody());
}
