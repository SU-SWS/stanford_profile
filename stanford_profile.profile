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
 * #[Hook] attribute support for install_tasks. The task callback resolves a
 * dependency-injected instance of InstallHooks via the class resolver so
 * InstallHooks::finalTask() can use constructor-injected services instead of
 * static \Drupal::service() calls.
 */
function stanford_profile_install_tasks(&$install_state) {
  return [
    'stanford_profile_final_task' => [
      'function' => [\Drupal::classResolver(InstallHooks::class), 'finalTask'],
    ],
  ];
}
