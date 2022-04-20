<?php

namespace Drupal\stanford_courses_importer\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * Provides a 'ExploreCoursesInstructors' migrate process plugin.
 *
 * @MigrateProcessPlugin(
 *  id = "explore_courses_instructors"
 * )
 */
class ExploreCoursesInstructors extends ProcessPluginBase {

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

    $instructors_array = [];

    $sections = $xml->section;
    foreach ($sections as $section) {

      $instructors = $section->schedules->schedule->instructors->instructor;
      foreach ($instructors as $instructor) {
        $instructors_array[] = trim((string) $instructor->name);
      }
    }

    $instructors_array = array_unique($instructors_array);
    return $instructors_array;
  }

}
