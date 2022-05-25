<?php

namespace Drupal\stanford_events_importer\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Do some math on a date string.
 *
 * Available configuration keys:
 * - operation: "addition"|"subtraction"
 * - values:
 *
 * Examples:
 *
 * @code
 * process:
 *   plugin: stanford_events_datemath
 *   operation: addition
 *   source: some_date_string
 *   values:
 *     - another_date_string
 *     - constants/some_number
 * @endcode
 *
 * This will perform the mathematical operation on the date strings.
 *
 * @MigrateProcessPlugin(
 *   id = "stanford_events_datemath"
 * )
 */
class DateMath extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // The base number to start with.
    $start_date = new \DateTime($value);
    $result_date = $start_date->format('U');

    // Nothing to do. Just convert to minutes and be done.
    if (empty($this->configuration['operation']) || empty($this->configuration['values'])) {
      return floor($result_date / 60);
    }

    // Fetch and run.
    $values = $this->configuration['values'];
    foreach ($values as &$item) {
      if (is_string($item) && $row->hasSourceProperty($item)) {
        $item = $row->getSourceProperty($item);
      }
    }

    // The modifier dates.
    foreach ($values as $date) {
      $raw_time = new \DateTime($date);
      $mod_time = $raw_time->format('U');
      switch ($this->configuration['operation']) {
        case 'subtraction':
          $result_date -= $mod_time;
          break;

        default:
          $result_date += $mod_time;
      }
    }

    // Resulting value in minutes.
    return floor($result_date / 60);
  }

}
