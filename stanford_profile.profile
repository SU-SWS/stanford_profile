<?php

/**
 * @file
 * stanford_profile.profile
 */

use Drupal\stanford_profile\Hook\InstallHooks;

/**
 * Implements hook_install_tasks().
 *
 * This must remain procedural — Drupal core's
 * HookCollectorPass::checkForProceduralOnlyHooks() explicitly denies OOP
 * #[Hook] attribute support for install_tasks.
 */
function stanford_profile_install_tasks(&$install_state) {
  return [
    'stanford_profile_final_task' => [
      'function' => InstallHooks::class . '::finalTask',
    ],
  ];
}
