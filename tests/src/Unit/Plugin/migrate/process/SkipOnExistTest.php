<?php

namespace Drupal\Tests\cardinal_service_profile\Unit\Plugin\migrate\process;

use Drupal\cardinal_service_profile\Plugin\migrate\process\SkipOnExist;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Database\Query\Select;
use Drupal\Core\Database\Schema;
use Drupal\Core\Database\StatementInterface;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\MigrateSkipRowException;
use Drupal\migrate\Row;
use Drupal\Tests\UnitTestCase;

/**
 * Class SkipOnExistTest.
 *
 * @group cardinal_service_profile
 * @coversDefaultClass \Drupal\cardinal_service_profile\Plugin\migrate\process\SkipOnExist
 */
class SkipOnExistTest extends UnitTestCase {

  /**
   * @var \Drupal\cardinal_service_profile\Plugin\migrate\process\SkipOnExist
   */
  protected $processPlugin;

  /**
   * If the schema method should return the table exists.
   *
   * @var bool
   */
  protected $migrateTableExists = FALSE;

  /**
   * Number of rows in the database that match the source ids.
   *
   * @var int
   */
  protected $migrateTableCount = 0;

  /**
   * {@inheritDoc}
   */
  protected function setUp() {
    parent::setUp();

    $config_factory = $this->createMock(ConfigFactoryInterface::class);
    $config_factory->method('listAll')->willReturn(['foo', 'bar', 'baz']);

    $schema = $this->createMock(Schema::class);
    $schema->method('tableExists')
      ->willReturnReference($this->migrateTableExists);
    $schema->method('fieldExists')
      ->willReturnReference($this->migrateTableExists);

    $statement = $this->createMock(StatementInterface::class);
    $statement->method('fetchField')
      ->willReturnReference($this->migrateTableCount);

    $select = $this->createMock(Select::class);
    $select->method('fields')->willReturnSelf();
    $select->method('condition')->willReturnSelf();
    $select->method('countQuery')->willReturnSelf();
    $select->method('execute')->willReturn($statement);

    $database = $this->createMock(Connection::class);
    $database->method('schema')->willReturn($schema);
    $database->method('select')->willReturn($select);

    $container = new ContainerBuilder();
    $container->set('config.factory', $config_factory);
    $container->set('database', $database);

    $config = [];
    $this->processPlugin = SkipOnExist::create($container, $config, '', []);
  }

  /**
   * Migration Tables haven't been created.
   */
  public function testTablesDontExist() {
    $migration = $this->createMock(MigrateExecutableInterface::class);
    $row = $this->createMock(Row::class);
    $row->method('getSourceIdValues')
      ->willReturn(['guid' => $this->randomMachineName()]);
    $this->assertNull($this->processPlugin->transform('foo', $migration, $row, NULL));
  }

  /**
   * When the migration tables exist, it will throw an exception.
   */
  public function testTablesExistSkip() {
    $this->migrateTableExists = TRUE;
    $migration = $this->createMock(MigrateExecutableInterface::class);
    $row = $this->createMock(Row::class);
    $row->method('getSourceIdValues')
      ->willReturn(['guid' => $this->randomMachineName()]);
    $this->assertNull($this->processPlugin->transform('foo', $migration, $row, NULL));

    $this->migrateTableCount = 1;
    $this->expectException(MigrateSkipRowException::class);
    $this->processPlugin->transform('foo', $migration, $row, NULL);
  }

}
