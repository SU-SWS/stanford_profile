<?php

/**
 * @file
 * stanford_profile.install
 */

use Drupal\block_content\Entity\BlockContent;

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
function stanford_profile_post_update_8200(){
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

function stanford_profile_post_update_8202() {
  $fields = \Drupal::entityTypeManager()
    ->getStorage('field_storage_config')
    ->loadMultiple();

  /** @var \Drupal\field\FieldStorageConfigInterface $field */
  foreach ($fields as $field) {
    if ($field->getThirdPartySetting('field_encrypt', 'encrypt', FALSE)) {
      $field->unsetThirdPartySetting('field_encrypt', 'encrypt');
      $field->unsetThirdPartySetting('field_encrypt', 'properties');
      $field->unsetThirdPartySetting('field_encrypt', 'encryption_profile');
      $field->save();
    }
  }
  _stanford_profile_post_update_encrypt_fields();
  \Drupal::service('module_installer')->uninstall(['field_encrypt']);
}

function _stanford_profile_post_update_encrypt_fields(){
  $queue_factory = \Drupal::service('queue');
  $queue_manager = \Drupal::service('plugin.manager.queue_worker');
  /** @var \Drupal\Core\Queue\QueueInterface $queue */
  $queue = $queue_factory->get('cron_encrypted_field_update');
  /** @var \Drupal\Core\Queue\QueueWorkerInterface $queue_worker */
  $queue_worker = $queue_manager->createInstance('cron_encrypted_field_update');

  while ($item = $queue->claimItem()) {
    try {
      $queue_worker->processItem($item->data);
      $queue->deleteItem($item);
    }
    catch (\Exception $e) {
      $queue->releaseItem($item);
    }
  }
}
