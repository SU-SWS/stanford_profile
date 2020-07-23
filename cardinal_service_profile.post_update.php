<?php

/**
 * @file
 * cardinal_service_profile.install
 */

use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field\Entity\FieldConfig;

/**
 * Implements hook_removed_post_updates().
 */
function cardinal_service_profile_removed_post_updates() {
  return [];
}

/**
 * Move field data for spotlights.
 */
function cardinal_service_profile_post_update_spotlight() {
  _cardinal_service_create_quote_field();
  _cardinal_service_create_context_field();

  $nodes = \Drupal::entityTypeManager()
    ->getStorage('node')
    ->loadByProperties(['type' => 'su_spotlight']);

  /** @var \Drupal\node\NodeInterface $node */
  foreach ($nodes as $node) {
    if ($body = $node->get('body')->getValue()) {

      $quote = [
        'value' => '<p>' . $body[0]['summary'] . '</p>',
        'format' => $body[0]['format'],
      ];

      $student_name = $node->get('su_spotlight_student_name')->getString();
      if ($student_name == $node->label()) {
        $node->set('su_spotlight_student_name', NULL);
      }

      if (!empty($body[0]['value'])) {
        $node->set('su_spotlight_context', strip_tags($body[0]['value']));
      }

      $node->set('su_spotlight_quote', $quote)
        ->set('body', NULL)
        ->save();
    }
  }
}

/**
 * Create the quote field on spotlight nodes.
 *
 * @throws \Drupal\Core\Entity\EntityStorageException
 */
function _cardinal_service_create_quote_field() {
  FieldStorageConfig::create([
    'uuid' => '5e126799-c3f1-4b09-bb97-006e8ac0aad2',
    'entity_type' => 'node',
    'type' => 'text_with_summary',
    'field_name' => 'su_spotlight_quote',
  ])->save();
  FieldConfig::create([
    'uuid' => '65cfa3f8-3ad4-4462-9e9c-4d13311fd777',
    'field_name' => 'su_spotlight_quote',
    'label' => 'Quote',
    'entity_type' => 'node',
    'bundle' => 'su_spotlight',
    'settings' => ['display_summary' => TRUE],
  ])->save();
}

/**
 * Create the context field on spotlight nodes.
 *
 * @throws \Drupal\Core\Entity\EntityStorageException
 */
function _cardinal_service_create_context_field() {
  FieldStorageConfig::create([
    'uuid' => '7bf4acd8-9984-4dc0-b544-112575255a5b',
    'entity_type' => 'node',
    'type' => 'string_long',
    'field_name' => 'su_spotlight_context',
  ])->save();
  FieldConfig::create([
    'uuid' => '2a38e48b-2ea4-4ffd-96b5-ffd40667fe45',
    'field_name' => 'su_spotlight_context',
    'label' => 'Caption/Context',
    'entity_type' => 'node',
    'bundle' => 'su_spotlight',
  ])->save();
}
