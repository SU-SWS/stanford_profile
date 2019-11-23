<?php
/**
 * @file
 * stanford_profile.profile
 * Enables modules and site configuration for a standard site installation.
 */

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\menu_link_content\Entity\MenuLinkContent;

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
 * Implements hook_preprocess_html().
 */
function stanford_profile_preprocess_html(&$vars) {
  try {
    $local_footer = \Drupal\config_pages\Entity\ConfigPages::config('stanford_local_footer');
    if ($local_footer) {
      $block_view = \Drupal::entityTypeManager()->getViewBuilder('config_pages')->view($local_footer);
      $vars['global_footers']['localfooter'] = $block_view;
      $vars["#attached"]['library'][] = "stanford_profile_styles/local_footer";
    }
  }
  catch(\Exception $e) {
    // Nothing really to do. We just don't show it.
  }
}
