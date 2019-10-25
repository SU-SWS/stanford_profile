<?php

/**
 * @file
 * stanford_profile.profile
 */

use Drupal\menu_link_content\Entity\MenuLinkContent;

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
 *
 * @throws \Drupal\Component\Plugin\Exception\PluginException
 */
function stanford_profile_final_task(array &$install_state) {
  /** @var \Drupal\stanford_profile\InstallTaskManager $install_task_manager */
  $install_task_manager = \Drupal::service('plugin.manager.install_tasks');
  foreach ($install_task_manager->getDefinitions() as $definition) {
    /** @var \Drupal\stanford_profile\InstallTaskInterface $plugin */
    $plugin = $install_task_manager->createInstance($definition['id']);
    $plugin->runTask($install_state);
  }

  // We install some menu links, so we have to rebuild the router, to ensure the
  // menu links are valid.
  \Drupal::service('router.builder')->rebuildIfNeeded();
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
