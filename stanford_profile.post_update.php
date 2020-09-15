<?php

/**
 * @file
 * stanford_profile.install
 */

use Drupal\Core\Site\Settings;
use Drupal\Core\Config\FileStorage;
use Drupal\react_paragraphs\Entity\ParagraphsRowType;
use Drupal\react_paragraphs\Entity\ParagraphRow;
use Drupal\paragraphs\Entity\Paragraph;

/**
 * Implements hook_removed_post_updates().
 */
function stanford_profile_removed_post_updates() {
  return [
    'stanford_profile_post_update_8001' => '8.x-1.13',
    'stanford_profile_post_update_8003' => '8.x-1.13',
    'stanford_profile_post_update_8013' => '8.x-1.13',
  ];
}

/**
 * Send out notification message about events and person importer.
 */
function stanford_profile_post_update_8014() {
  \Drupal::service('module_installer')->install(['stanford_notifications']);
  /** @var \Drupal\stanford_notifications\NotificationServiceInterface $notifications */
  $notifications = \Drupal::service('notification_service');

  $message = 'New: You can now create "Events" content. See <a href="https://userguide.sites.stanford.edu/tour/events">the user guide</a> for more information';
  $notifications->addNotification($message, [
    'site_manager',
    'site_editor',
    'contributor',
  ]);

  $message = 'New: You can now import "Person" content from Stanford Who. See <a href="https://userguide.sites.stanford.edu/tour/person/person-importer">the user guide</a> for more information';
  $notifications->addNotification($message, [
    'site_manager',
    'site_editor',
    'contributor',
  ]);

  $message = 'Update: We changed some things about how buttons are aligned. See <a href="https://userguide.sites.stanford.edu/tour/paragraphs/text-area/working-with-buttons">the user guide page</a> for more information';
  $notifications->addNotification($message, [
    'site_manager',
    'site_editor',
    'contributor',
  ]);

}

/**
 * Create blocks for sites with custom themes that were added to stanford_basic.
 */
function stanford_profile_post_update_8015() {
  $theme_name = \Drupal::config('system.theme')->get('default');
  // Default theme is good. Just end if so.
  if ($theme_name == "stanford_basic") {
    return;
  }

  // Not stanford_basic, we have to create two config page blocks.
  // Copy the blocks from stanford_basic and rename them.
  //
  // Names of things.
  $basic_global_name = 'block.block.stanford_basic_config_pages_stanford_global_msg';
  $basic_super_name = 'block.block.stanford_basic_config_pages_stanford_super_footer';
  $my_global_name = 'block.block.' . $theme_name . '_config_pages_stanford_global_msg';
  $my_super_name = 'block.block.' . $theme_name . '_config_pages_stanford_super_footer';

  // Resources.
  $config_path = Settings::get('config_sync_directory');
  $source = new FileStorage($config_path);
  $config_factory = \Drupal::service('config.factory');

  // Get the configuration out of the filesystem as it may not have been
  // imported yet...
  $basic_global_config = $config_factory
    ->getEditable($my_global_name)
    ->setData(
      $source->read($basic_global_name)
    );
  $basic_super_config = $config_factory
    ->getEditable($my_super_name)
    ->setData(
      $source->read($basic_super_name)
    );

  // Change a few ids.
  $basic_global_config->set('id', $theme_name . '_config_pages_stanford_global_msg');
  $basic_super_config->set('id', $theme_name . '_config_pages_super_global_msg');
  $basic_global_config->set('theme', $theme_name);
  $basic_super_config->set('theme', $theme_name);
  $basic_global_config->set('dependencies.theme', [$theme_name]);
  $basic_super_config->set('dependencies.theme', [$theme_name]);

  // Remove the UUID.
  $basic_global_config->clear('uuid');
  $basic_super_config->clear('uuid');

  // Add it to the DB.
  $basic_global_config->save();
  $basic_super_config->save();
}

/**
 * Restore missing content on unpublished nodes.
 */
function stanford_profile_post_update_8016() {
  $entity_type_manager = \Drupal::entityTypeManager();
  $field_name = "su_page_components";
  $entity_type = "node";
  $query = \Drupal::entityQuery('node');
  $query->condition('status', FALSE);
  $query->condition('type', 'stanford_page');
  $query->accessCheck(FALSE);
  $entity_ids = $query->execute();
  $entities = [];

  foreach ($entity_ids as $entity_id) {
    $entities["$entity_type:$entity_id:$field_name"] = "$entity_type:$entity_id:$field_name";
  }

  foreach ($entities as $item) {
    [$entity_type, $id, $field_name] = explode(':', $item);
    $entity = $entity_type_manager->getStorage($entity_type)->load($id);

    $rows = [];
    foreach ($entity->get($field_name)->getValue() as $field_item) {
      $field_item['settings'] = json_decode($field_item['settings'], TRUE);
      // Because the serializer is gone, the settings might be a double encoded
      // json string, so we will want to check to try and decode it again.
      if (!is_array($field_item['settings'])) {
        $field_item['settings'] = json_decode($field_item['settings'], TRUE);
      }
      $rows[$field_item['settings']['row']][] = $field_item;
    }

    $entity_row_field_data = [];

    foreach ($rows as $row_info) {
      $row_items = [];

      foreach ($row_info as $row_item) {
        /** @var \Drupal\paragraphs\ParagraphInterface $paragraph */
        $paragraph = Paragraph::load($row_item['target_id']);
        $paragraph->setBehaviorSettings('react', [
          'width' => $row_item['settings']['width'],
          'label' => $row_item['settings']['admin_title'],
        ]);
        $paragraph->save();
        $row_items[] = [
          'target_id' => $paragraph->id(),
          'target_revision_id' => $paragraph->getRevisionId(),
        ];
      }

      /** @var \Drupal\react_paragraphs\Entity\ParagraphsRowInterface $row */
      $row = ParagraphRow::create([
        'type' => "{$entity_type}_{$entity->bundle()}_row",
        'parent' => $id,
        'parent_type' => $entity->getEntityTypeId(),
        'parent_field_name' => $field_name,
      ]);
      $row->set($field_name, $row_items)->save();
      $entity_row_field_data[] = [
        'target_id' => $row->id(),
        'target_revision_id' => $row->getRevisionId(),
      ];
    }

    $entity->set($field_name, $entity_row_field_data);
    $entity->save();
  }
}
