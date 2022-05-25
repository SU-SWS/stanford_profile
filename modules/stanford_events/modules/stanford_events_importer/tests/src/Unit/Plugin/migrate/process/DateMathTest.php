<?php

namespace Drupal\Tests\stanford_events_importer\Unit\Plugin\migrate\process;

use Drupal\stanford_events_importer\Plugin\migrate\process\DateMath;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Drupal\Tests\UnitTestCase;

/**
 * Class DateMathTest.
 *
 * @group stanford_events_importer
 * @coversDefaultClass \Drupal\stanford_events_importer\Plugin\migrate\process\DateMath
 */
class DateMathTest extends UnitTestCase {

  /**
   * [protected description]
   * @var [type]
   */
  public function testTransform() {
    $end_value = "2022-06-11 01:00:00 -0700";
    $value = "2022-06-11 00:00:00 -0700";

    $configuration = [
      'operation' => 'subtraction',
      'values' => [
        $value
      ],
    ];

    $migrate = $this->createMock(MigrateExecutableInterface::class);
    $row = $this->createMock(Row::class);

    // Subtract.
    $plugin = new DateMath($configuration, '', []);
    $duration = $plugin->transform($end_value, $migrate, $row, '');
    $this->assertEquals(60, $duration);

    // Add.
    $configuration['operation'] = "addition";
    $plugin = new DateMath($configuration,  '', []);
    $duration = $plugin->transform($end_value, $migrate, $row, '');
    $this->assertEquals(55164420, $duration);

    // Nana
    unset($configuration['operation']);
    $plugin = new DateMath($configuration,  '', []);
    $duration = $plugin->transform($end_value, $migrate, $row, '');
    $this->assertEquals(27582240, $duration);
  }

}
