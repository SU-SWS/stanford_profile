<?php

/**
 * @file
 * cardinal_service_profile.install
 */

use Drupal\block\Entity\Block;

/**
 * Implements hook_removed_post_updates().
 */
function cardinal_service_profile_removed_post_updates() {
  return [
    'cardinal_service_profile_post_update_spotlight' => '8.x-2.1',
    'cardinal_service_profile_post_update_csd_233' => '8.x-2.1',
  ];
}

/**
 * Disable the core search module.
 */
function cardinal_service_profile_post_update_8200(){
  \Drupal::service('module_installer')->uninstall(['search']);
}

/**
 * Set the default image for Opportunities if not set.
 */
function cardinal_service_profile_post_update_8201(&$sandbox) {
  $node_storage = \Drupal::entityTypeManager()->getStorage('node');
  $media_id = '7021';
  if (empty($sandbox['ids'])) {
    $sandbox['ids'] = $node_storage->getQuery()
      ->accessCheck(FALSE)
      ->condition('type', 'su_opportunity')
      ->condition('su_opp_image', NULL, 'IS NULL')
      ->execute();
    $sandbox['total'] = count($sandbox['ids']);
  }
  $node_ids = array_splice($sandbox['ids'], 0, 10);

  foreach ($node_storage->loadMultiple($node_ids) as $node) {
    $node->set('su_opp_image', $media_id)->save();
  }

  $sandbox['#finished'] = count($sandbox['ids']) ? 1 - count($sandbox['ids']) / $sandbox['total'] : 1;
}

/**
 * Add the main anchor block to the search page.
 */
function cardinal_service_profile_post_update_8202() {
  $theme_name = \Drupal::config('system.theme')->get('default');
  if (!in_array($theme_name, [
    'stanford_basic',
    'minimally_branded_subtheme',
  ])) {
    Block::create([
      'id' => "{$theme_name}_main_anchor",
      'theme' => $theme_name,
      'region' => 'content',
      'weight' => -10,
      'plugin' => 'jumpstart_ui_skipnav_main_anchor',
      'settings' => [
        'id' => 'jumpstart_ui_skipnav_main_anchor',
        'label' => 'Main content anchor target',
        'label_display' => 0,
      ],
      'visibility' => [
        'request_path' => [
          'id' => 'request_path',
          'negate' => FALSE,
          'pages' => '/search',
        ],
      ],
    ])->save();
  }
}
