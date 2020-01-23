<?php

/**
 * @file
 * stanford_profile.profile
 */

use Drupal\Component\Utility\Html;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\node\NodeInterface;

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
 * Implements hook_ENTITY_TYPE_insert().
 */
function stanford_profile_menu_link_content_presave(MenuLinkContent $entity) {
  // For new menu link items created on a node form (normally), set the expanded
  // attribute so all menu items are expanded by default.
  if ($entity->isNew()) {
    $entity->set('expanded', TRUE);
  }
}

/**
 * Implements hook_preprocess_HOOK().
 */
function stanford_profile_preprocess_input__submit__paragraph_action(&$variables) {
  // Change the top banner field button from "Add @type" to "Add Top @type".
  if ($variables['element']['#name'] == 'su_page_banner_stanford_banner_add_more') {
    $variables['attributes']['value'] = t('Add Top @type', $variables['attributes']['value']->getArguments());
  }
}

/**
 * Implements hook_entity_field_access().
 */
function stanford_profile_entity_field_access($operation, FieldDefinitionInterface $field_definition, AccountInterface $account, FieldItemListInterface $items = NULL) {
  if ($field_definition->getType() == 'entity_reference' && $field_definition->getSetting('handler') == 'layout_library') {
    $entity_type = $field_definition->getTargetEntityTypeId();
    $bundle = $field_definition->getTargetBundle();
    if (!$account->hasPermission("choose layout for $entity_type $bundle")) {
      return AccessResult::forbidden();
    }
  }
  return AccessResult::neutral();
}

/**
 * Implements hook_preprocess_toolbar().
 */
function stanford_profile_preprocess_toolbar(&$variables) {
  array_walk($variables['tabs'], function (&$tab, $key) {
    if (isset($tab['attributes'])) {
      $tab['attributes']->addClass(Html::cleanCssIdentifier("$key-tab"));
    }
  });
}

/**
 * Implements hook_contextual_links_alter().
 */
function stanford_profile_contextual_links_alter(array &$links, $group, array $route_parameters) {
  if ($group == 'paragraph') {
    // Paragraphs edit module clone link does not function correctly. Remove it
    // from available links. Also remove delete to avoid unwanted delete.
    unset($links['paragraphs_edit.delete_form']);
    unset($links['paragraphs_edit.clone_form']);
  }
}

/**
 * Implements hook_node_access().
 */
function stanford_profile_node_access(NodeInterface $node, $op, AccountInterface $account) {
  if ($op == 'delete') {
    $site_config = \Drupal::config('system.site');
    $node_urls = [$node->toUrl()->toString(), "/node/{$node->id()}"];

    // If the node is configured to be the home page, 404, or 403, prevent the
    // user from deleting. Unfortunately this only works for roles without the
    // "Bypass content access control" permission.
    if (array_intersect($node_urls, $site_config->get('page'))) {
      return AccessResult::forbidden();
    }
  }
  return AccessResult::neutral();
}
