<?php

/**
 * @file
 * stanford_profile.profile
 */

use Drupal\Component\Utility\Html;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\node\NodeInterface;
use Drupal\Core\Site\Settings;
use Drupal\Core\Cache\Cache;

/**
 * Implements hook_install_tasks().
 */
function stanford_profile_install_tasks(&$install_state) {
  return ['stanford_profile_final_task' => []];
}

/**
 * Implements hook_menu_links_discovered_alter().
 */
function stanford_profile_menu_links_discovered_alter(&$links) {
  if (isset($links['admin_toolbar_tools.media_page'])) {
    // Alter the "Media" link for /admin/content/media path.
    $links['admin_toolbar_tools.media_page']['title'] = t('All Media');
  }
  if (isset($links['system.admin_content'])) {
    // Change the node list page for the /admin/content path.
    $links['system.admin_content']['title'] = t('All Content');
  }
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
 * Implements hook_preprocess_HOOK().
 */
function stanford_profile_preprocess_block__help(&$variables) {
  if (\Drupal::routeMatch()->getRouteName() == 'help.main') {
    // Removes the help text from core help module. Its not helpful, and we're
    // going to provide our own help text.
    // @see help_help()
    unset($variables['content']);
  }
}

/**
 * Implements hook_help_section_info_alter().
 */
function stanford_profile_help_section_info_alter(array &$info) {
  // Change "Module overviews" header.
  $info['hook_help']['title'] = t('For Developers');
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

  // When a menu item is added as a child of another menu item clear the parent
  // pages cache so that the block shows up as it doesn't get invalidated just
  // by the menu cache tags.
  $parent_id = $entity->getParentId();
  if (!empty($parent_id)) {
    [$entity_name, $uuid] = explode(':', $parent_id);
    $menu_link_content = \Drupal::entityTypeManager()->getStorage($entity_name)->loadByProperties(['uuid' => $uuid]);
    if (is_array($menu_link_content)) {
      $parent_item = array_pop($menu_link_content);
      $params = $parent_item->getUrlObject()->getRouteParameters();
      if (isset($params['node'])) {
        Cache::invalidateTags(['node:' . $params['node']]);
      }
    }
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
 * Implements hook_field_widget_WIDGET_TYPE_form_alter().
 */
function stanford_profile_field_widget_options_select_form_alter(&$element, FormStateInterface $form_state, $context) {
  if ($context['items']->getFieldDefinition()->getName() == 'layout_selection') {
    $element['#description'] = t('Choose a layout to display the page as a whole. Choose "- None -" to keep the default layout setting.');
  }
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

/**
 * Implements hook_form_FORM_ID_alter().
 */
function stanford_profile_form_menu_edit_form_alter(array &$form, FormStateInterface $form_state) {
  $read_only = Settings::get('config_readonly', FALSE);
  if (!$read_only) {
    return;
  }

  // If the form is locked, hide the config you cannot change from users without
  // the know how.
  $access = \Drupal::currentUser()->hasPermission('Administer menus and menu items');
  $form['label']['#access'] = $access;
  $form['description']['#access'] = $access;
  $form['id']['#access'] = $access;

  // Remove the warning message if the user does not have access.
  if (!$access) {
    \Drupal::messenger()->deleteByType("warning");
  }
}

/**
 * Implements hook_form_FORM_ID_alter().
 */
function stanford_profile_form_config_pages_stanford_basic_site_settings_form_alter(array &$form, FormStateInterface $form_state) {
  $form['#validate'][] = 'stanford_profile_config_pages_stanford_basic_site_settings_form_validate';
  $form['#submit'][] = 'stanford_profile_config_pages_stanford_basic_site_settings_form_submit';
}

/**
 * Validates form values.
 *
 * @param array $form
 *   The form array.
 * @param \Drupal\Core\Form\FormStateInterface $form_state
 *   The form state interface object.
 */
function stanford_profile_config_pages_stanford_basic_site_settings_form_validate(array $form, FormStateInterface $form_state) {
  $element = $form_state->getValue('su_site_url');
  $uri = $element['0']['uri'];
  if (!empty($uri)) {
    $match = preg_match('/^http(s)?:\/\/.*\.stanford.edu/i', $uri);
    if (!$match) {
      $form_state->setErrorByName('su_site_url', t('Only valid stanford.edu domain names allowed.'));
    }
  }
}

/**
 * Set drupal state values based on field values.
 *
 * @param array $form
 *   The form array.
 * @param \Drupal\Core\Form\FormStateInterface $form_state
 *   The form state interface object.
 */
function stanford_profile_config_pages_stanford_basic_site_settings_form_submit(array $form, FormStateInterface $form_state) {
  $element = $form_state->getValue('su_site_url');
  $uri = $element['0']['uri'];
  if (!empty($uri)) {
    \Drupal::state()->set('xmlsitemap_base_url', $uri);
  }
}
