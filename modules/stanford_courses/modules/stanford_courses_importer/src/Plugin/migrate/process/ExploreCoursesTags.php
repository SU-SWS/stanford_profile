<?php

namespace Drupal\stanford_courses_importer\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * Provides a 'ExploreCoursesTags' migrate process plugin.
 *
 * @MigrateProcessPlugin(
 *  id = "explore_courses_tags"
 * )
 */
class ExploreCoursesTags extends ProcessPluginBase {

  /**
   * {@inheritDoc}.
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (!is_string($value)) {
      return '';
    }

    try {
      $xml = new \SimpleXMLElement($value);
    }
    catch (\Throwable $e) {
      return '';
    }

    $tags = [];
    // Concatenate the name and organization to make the tag.
    foreach ($xml->tag as $tag) {
      $tags[] = (string) $tag->organization . "::" . (string) $tag->name;
    }

    return implode(';', $tags);
  }

}
