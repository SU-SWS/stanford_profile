<?php

namespace Drupal\Tests\cardinal_service_profile\Unit\Plugin\migrate\process;

use Drupal\cardinal_service_profile\Plugin\migrate\process\SkipOnTime;
use Drupal\migrate\MigrateExecutable;
use Drupal\migrate\MigrateSkipProcessException;
use Drupal\migrate\Row;
use Drupal\Tests\UnitTestCase;

/**
 * Class SkipOnTimeTest.
 *
 * @coversDefaultClass \Drupal\cardinal_service_profile\Plugin\migrate\process\SkipOnTime
 */
class SkipOnTimeTest extends UnitTestCase {

  /**
   * Test the process plugin if the time is below the comparison value.
   */
  public function testProcessPlugin() {
    $plugin = new SkipOnTime([
      'compare' => '>',
      'value' => 'now +1day',
      'method' => 'process',
    ], '', []);

    $migration = $this->createMock(MigrateExecutable::class);
    $row = $this->createMock(Row::class);

    $time = time();
    $this->assertEquals($time, $plugin->transform($time, $migration, $row, NULL));

    $plugin = new SkipOnTime([
      'compare' => '<',
      'value' => 'now +1day',
      'method' => 'process',
    ], '', []);

    $migration = $this->createMock(MigrateExecutable::class);
    $row = $this->createMock(Row::class);

    $this->expectException(MigrateSkipProcessException::class);
    $plugin->transform($time, $migration, $row, NULL);

  }

  /**
   * Test the process plugin if the time is above the comparison value.
   */
  public function testProcessPluginOver() {
    $plugin = new SkipOnTime([
      'compare' => '<',
      'value' => 'now +1day',
      'method' => 'process',
    ], '', []);

    $migration = $this->createMock(MigrateExecutable::class);
    $row = $this->createMock(Row::class);

    $time = time() + 60 * 60 * 24 * 30;
    $this->assertEquals($time, $plugin->transform($time, $migration, $row, NULL));


    $plugin = new SkipOnTime([
      'compare' => '>',
      'value' => 'now +1day',
      'method' => 'process',
    ], '', []);

    $migration = $this->createMock(MigrateExecutable::class);
    $row = $this->createMock(Row::class);

    $this->expectException(MigrateSkipProcessException::class);
    $plugin->transform($time, $migration, $row, NULL);

  }

}
