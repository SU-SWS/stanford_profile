<?php

namespace Drupal\stanford_courses_importer\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * Provides a 'ExploreCoursesQuarters' migrate process plugin.
 *
 * @MigrateProcessPlugin(
 *  id = "explore_courses_quarters"
 * )
 */
class ExploreCoursesQuarters extends ProcessPluginBase {

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

    $quarters = [];
    $sections = $xml->section;
    foreach ($sections as $section) {
      $quarters[] = substr(trim((string) $section->term), 10);
    }

    $quarters = array_unique($quarters);
    return $quarters;
  }

}
