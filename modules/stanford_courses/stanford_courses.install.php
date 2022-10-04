<?php

/**
 * @file
 * Stanford_courses.install.
 */

/**
 * Drop migrate_map_stanford_courses table and recreate upon next import.
 */
function stanford_courses_update_9001() {
  $database = \Drupal::database();
  if ($database->schema()->tableExists('migrate_map_stanford_courses')) {
    // Remove previously imported courses and let them be recreated.
    $query = $database->query("SELECT `destid1` FROM {migrate_map_stanford_courses}");
    $result = $query->fetchAll();
    foreach ($result as $obsolete_node_id) {
      $node = \Drupal::entityTypeManager()->getStorage('node')->load($obsolete_node_id->destid1);
      if ($node) {
        $node->delete();
      }
    }
    $database->schema()->dropTable('migrate_map_stanford_courses');
  }
}
