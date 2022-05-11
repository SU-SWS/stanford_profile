<?php

/**
 * @file
 * stanford_person_importer.post_update.php
 */

/**
 * Invalidate all migration profiles that don't have a photo.
 */
function stanford_person_importer_post_update_8001(&$sandbox) {
  if (!\Drupal::database()
    ->schema()
    ->tableExists('migrate_map_su_stanford_person')) {
    return;
  }

  $nids = \Drupal::entityTypeManager()->getStorage('node')
    ->getQuery()
    ->accessCheck(FALSE)
    ->condition('type', 'stanford_person')
    ->condition('su_person_photo', NULL, 'IS NULL')
    ->execute();

  if ($nids) {
    \Drupal::database()->update('migrate_map_su_stanford_person')
      ->fields(['hash' => ''])
      ->condition('destid1', array_values($nids), 'IN')
      ->execute();
  }
}

/**
 * Delete imported images that are only 1px x 1px.
 */
function stanford_person_importer_post_update_8002(&$sandbox) {
  if (!\Drupal::database()
    ->schema()
    ->tableExists('migrate_map_su_stanford_person')) {
    return;
  }
  $media_storage = \Drupal::entityTypeManager()->getStorage('media');
  $mids = $media_storage->getQuery()
    ->condition('bundle', 'image')
    ->condition('field_media_image.width', 2, '<')
    ->condition('field_media_image.height', 2, '<')
    ->execute();
  if (empty($mids)) {
    return;
  }
  $node_storage = \Drupal::entityTypeManager()->getStorage('node');
  $nids = $node_storage->getQuery()
    ->condition('su_person_photo', $mids, 'IN')
    ->execute();
  if (empty($nids)) {
    return;
  }

  /** @var \Drupal\node\NodeInterface $node */
  foreach ($node_storage->loadMultiple($nids) as $node) {
    $field_default = $node->getFieldDefinition('su_person_photo')
      ->getDefaultValue($node);
    $node->set('su_person_photo', $field_default)->save();
  }

  \Drupal::database()->update('migrate_map_su_stanford_person')
    ->fields(['hash' => ''])
    ->condition('destid1', $nids, 'IN')
    ->execute();

  $file_storage = \Drupal::entityTypeManager()->getStorage('file');
  foreach ($media_storage->loadMultiple($mids) as $media) {
    $field_value = $media->get('field_media_image')->getValue();
    $media->delete();
    if ($file = $file_storage->load($field_value[0]['target_id'])) {
      $file->delete();
    }
  }
  \Drupal::messenger()->addMessage(t('Re-run the Person Importer to rebuild the images that were incorrectly imported the first time.'));
}
