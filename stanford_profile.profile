<?php
/**
 * @file
 * stanford_profile.profile
 * Enables modules and site configuration for a standard site installation.
 */

use Drupal\Component\Utility\Html;
use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\stanford_profile\InstallTasksInterface;

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
  /** @var \Drupal\stanford_profile\InstallTasksInterface $install_tasks */
  $install_tasks = \Drupal::service('stanford_profile.install_tasks');
  $site_name = $install_vars['forms']['install_configure_form']['site_name'] ?? InstallTasksInterface::DEFAULT_SITE;
  $install_tasks->setSiteSettings(Html::escape($site_name));
}

/**
 * Implements hook_ENTITY_TYPE_insert().
 */
function stanford_profile_menu_link_content_presave(MenuLinkContent $entity) {
  // For new menu link items created on a node form (normally), set the expanded
  // attribute so all menu items are expanded by default.
  if ($entity->isNew()) {
    $entity->set('expanded', TRUE);
  }
}
