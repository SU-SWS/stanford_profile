<?php

/**
 * @file
 * stanford_courses.post_update.php
 */

use \Drupal\menu_link_content\Entity\MenuLinkContent;

/**
 * Create the "All" courses menu link.
 */
function stanford_courses_post_update_menu_link() {
  MenuLinkContent::create([
    'uuid' => 'b05dadbf-c502-4962-affc-f943eb4f13cc',
    'title' => 'All',
    'link' => ['uri' => 'internal:/courses'],
    'menu_name' => 'courses-menu',
    'weight' => -99,
  ])->save();
}
