<?php

namespace Drupal\cardinal_service_profile\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Drupal\migrate_plus\Plugin\migrate\process\SkipOnValue;

/**
 * Skip the row or the process if the time is different.
 *
 * @MigrateProcessPlugin(
 *   id = "skip_on_time_compare"
 * )
 */
class SkipOnTime extends SkipOnValue {

  /**
   * Transforms the value being compared from the previous process data.
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $original_value = strtotime($value);
    $comparison_value = strtotime($this->configuration['value']);
    switch ($this->configuration['compare']) {
      case '>':
        if ($original_value > $comparison_value) {
          $this->configuration['value'] = $value;
        }
        break;
      case '<':
        if ($original_value < $comparison_value) {
          $this->configuration['value'] = $value;
        }
        break;

    }
    return parent::transform($value, $migrate_executable, $row, $destination_property);
  }

}
