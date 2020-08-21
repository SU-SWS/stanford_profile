<?php

namespace Drupal\Tests\cardinal_service_profile\Unit\Plugin\migrate\process;

use Drupal\cardinal_service_profile\Plugin\migrate\process\SkipOnEqual;
use Drupal\migrate\MigrateExecutable;
use Drupal\migrate\MigrateSkipProcessException;
use Drupal\migrate\Row;
use Drupal\Tests\UnitTestCase;

/**
 * Class SkipOnEqualTest.
 *
 * @group cardinal_service
 */
class SkipOnEqualTest extends UnitTestCase {

  /**
   * Should throw an exception that indicates skipping the process.
   */
  public function testSkipTransform() {
    $plugin = new SkipOnEqual([
      'compare' => '@bar',
      'method' => 'process',
    ], '', []);
    $migration = $this->createMock(MigrateExecutable::class);
    $row = $this->createMock(Row::class);
    $row->method('get')->willReturn('foo');

    $this->expectException(MigrateSkipProcessException::class);
    $plugin->transform('foo', $migration, $row, NULL);
  }

  /**
   * Should not throw an exception to skip the process.
   */
  public function testNoSkipTransform() {
    $plugin = new SkipOnEqual([
      'compare' => '@bar',
      'method' => 'process',
    ], '', []);
    $migration = $this->createMock(MigrateExecutable::class);
    $row = $this->createMock(Row::class);
    $row->method('get')->willReturn('bar');

    $new_value = $plugin->transform('foo', $migration, $row, NULL);
    $this->assertEquals('foo', $new_value);
  }

}
