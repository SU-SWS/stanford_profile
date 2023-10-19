<?php

/**
 * @file
 * stanford_profile.profile
 */

use Drupal\config_pages\Entity\ConfigPages;
use Drupal\Core\Installer\InstallerKernel;

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

/**
 * Implements hook_ENTITY_TYPE_presave().
 */
function stanford_profile_config_pages_presave(ConfigPages $config_page) {
  // During install, rebuild the router when saving a config page. This prevents
  // an error if the config page route doesn't exist for it yet. Event
  // subscriber doesn't work for this since it's during installation.
  if (InstallerKernel::installationAttempted()) {
    \Drupal::service('router.builder')->rebuild();
  }
}

/**
 * Implements hook_entity_field_access().
 */
function stanford_profile_entity_field_access($operation, \Drupal\Core\Field\FieldDefinitionInterface $field_definition, \Drupal\Core\Session\AccountInterface $account, \Drupal\Core\Field\FieldItemListInterface $items = NULL) {
  if (
    $field_definition->getName() == 'title' &&
    $items?->getEntity() instanceof \Drupal\node\NodeInterface &&
    $items?->getEntity()->bundle() == 'stanford_page' &&
    $items?->getEntity()->get('su_page_banner')->count()
  ) {
    $banner_id = $items->getEntity()
      ->get('su_page_banner')
      ->get(0)
      ->get('target_id')
      ->getString();

    $top_banner = \Drupal::entityTYpeManager()
      ->getStorage('paragraph')
      ->load($banner_id);

    return \Drupal\Core\Access\AccessResult::forbiddenIf($top_banner?->bundle() == 'stanford_top_banner' && $operation == 'view');
  }
  return \Drupal\Core\Access\AccessResult::neutral();
}
