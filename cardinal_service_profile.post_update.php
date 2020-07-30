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

/**
 * Combine graduation year and major into a text field for spotlights.
 */
function cardinal_service_profile_post_update_csd_233() {
  FieldStorageConfig::create([
    'uuid' => '8a5eaa06-a940-4765-b76c-3352f060ca3e',
    'entity_type' => 'node',
    'type' => 'string',
    'field_name' => 'su_spotlight_grad_area',
  ])->save();
  FieldConfig::create([
    'uuid' => '350b870c-00b0-44c8-8537-f3ea98588872',
    'field_name' => 'su_spotlight_grad_area',
    'label' => 'Graduation Year and Area',
    'entity_type' => 'node',
    'bundle' => 'su_spotlight',
  ])->save();
  $nodes = \Drupal::entityTypeManager()
    ->getStorage('node')
    ->loadByProperties(['type' => 'su_spotlight']);

  $term_storage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
  foreach ($nodes as $node) {
    $year = (int) $node->get('su_spotlight_graduation_year')->getString();
    $year = substr($year, 2);
    $major_tid = (int) $node->get('su_spotlight_major')->getString();
    $major = $term_storage->load($major_tid)->label();

    $year_major = $year ?: $major;
    if ($year && $major) {
      $year_major = "'$year, $major";
    }
    $node->set('su_spotlight_grad_area', $year_major)->save();
  }

  foreach ($term_storage->loadByProperties(['vid' => 'su_school_majors']) as $term) {
    $term->delete();
  }
}
