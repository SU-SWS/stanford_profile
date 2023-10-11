<?php

/**
 * @file
 * stanford_profile.install
 */

use Drupal\block_content\Entity\BlockContent;
use Drupal\block\Entity\Block;
use Drupal\Core\Serialization\Yaml;

/**
 * Implements hook_removed_post_updates().
 */
function stanford_profile_removed_post_updates() {
  return [
    'stanford_profile_post_update_8001' => '8.x-1.13',
    'stanford_profile_post_update_8003' => '8.x-1.13',
    'stanford_profile_post_update_8013' => '8.x-1.13',
    'stanford_profile_post_update_8014' => '8.x-2.9',
    'stanford_profile_post_update_8015' => '8.x-2.9',
  ];
}

/**
 * Disable the core search module.
 */
function stanford_profile_post_update_8200() {
  \Drupal::service('module_installer')->uninstall(['search']);
}

/**
 * Create the courses intro block content.
 */
function stanford_profile_post_update_8201() {
  BlockContent::create([
    'uuid' => '2f343c04-f892-49bb-8d28-2c3f4653b02a',
    'type' => 'stanford_component_block',
    'info' => 'Courses Intro',
  ])->save();
}

/**
 * Add the main anchor block to the search page.
 */
function stanford_profile_post_update_8202() {
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

/**
 * Update field storage definitions.
 */
function stanford_profile_post_update_update_field_defs() {
  $um = \Drupal::entityDefinitionUpdateManager();
  foreach ($um->getChangeList() as $entity_type => $changes) {
    if (isset($changes['field_storage_definitions'])) {
      foreach ($changes['field_storage_definitions'] as $field_name => $status) {
        $um->updateFieldStorageDefinition($um->getFieldStorageDefinition($field_name, $entity_type));
      }
    }
  }
}

/**
 * Enable samlauth.
 */
function stanford_profile_post_update_samlauth() {
  if (\Drupal::moduleHandler()->moduleExists('stanford_samlauth')) {
    return;
  }
  $ignore_settings = \Drupal::configFactory()
    ->getEditable('config_ignore.settings');
  $ignored = $ignore_settings->get('ignored_config_entities');
  $ignored[] = 'samlauth.authentication:map_users_roles';
  $ignore_settings->set('ignored_config_entities', $ignored)->save();
  \Drupal::service('module_installer')->install(['stanford_samlauth']);
}

/**
 * Create site org vocab and terms.
 */
function stanford_profile_post_update_site_orgs() {
  $vocab_storage = \Drupal::entityTypeManager()
    ->getStorage('taxonomy_vocabulary');
  if (!$vocab_storage->load('site_owner_orgs')) {
    $vocab_storage->create([
      'uuid' => '0611ae1d-2ab4-46c3-9cc8-2259355f0852',
      'vid' => 'site_owner_orgs',
      'name' => 'Site Owner Orgs',
    ])->save();

    $profile_name = \Drupal::config('core.extension')->get('profile');
    $profile_path = \Drupal::service('extension.list.profile')
      ->getPath($profile_name);

    /** @var \Drupal\default_content\Normalizer\ContentEntityNormalizer $importer */
    $normalizer = \Drupal::service('default_content.content_entity_normalizer');

    $files = \Drupal::service('default_content.content_file_storage')
      ->scan("$profile_path/content/taxonomy_term");

    foreach ($files as $file) {
      $term = Yaml::decode(file_get_contents($file->uri));
      if ($term['_meta']['bundle'] == 'site_owner_orgs') {
        $normalizer->denormalize($term)->save();
      }
    }
  }
}
