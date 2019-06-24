<?php
/**
 * @file
 * stanford_profile.profile
 * Enables modules and site configuration for a standard site installation.
 */

/**
 * Implements hook_ENTITY_TYPE_insert().
 */
function jack_dor_profile_menu_link_content_presave(MenuLinkContentInterface $entity) {
  // For new menu link items created on a node form (normally), set the expanded
  // attribute so all menu items are expanded by default.
  if ($entity->isNew()) {
    $entity->set('expanded', TRUE);
  }
}