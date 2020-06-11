<?php

namespace Drupal\cardinal_service_profile\Plugin\migrate\process;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\MigrateSkipRowException;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Migrate process plugin to query the other migrations for identical keys.
 *
 * @MigrateProcessPlugin(
 *   id = "skip_on_exist"
 * )
 */
class SkipOnExist extends ProcessPluginBase implements ContainerFactoryPluginInterface {

  /**
   * Config Factory Service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Database connection service.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * List of migration ids excluding those defined by the exclude config.
   *
   * @var string[]
   */
  protected $migrations = [];

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory'),
      $container->get('database')
    );
  }

  /**
   * {@inheritDoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactoryInterface $config_factory, Connection $database) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->configFactory = $config_factory;
    $this->database = $database;
  }

  /**
   * {@inheritDoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $source_ids = $row->getSourceIdValues();
    $migration_names = $this->getMigrationConfigs($source_ids);

    // Check each migration table for the identical source id values that also
    // have a destination ID. The destination id indicates an entity exists.
    foreach ($migration_names as $name) {
      $query = $this->database->select("migrate_map_$name", 'm')
        ->fields('m')
        ->condition('destid1', 0, '>');

      $i = 1;
      // The migration table consist of `sourceid1`, `sourceid2` etc for the
      // number of keys as defined in the migration configuration. We'll check
      // each of those source values against existing tables to determine if the
      // item already exists.
      foreach ($source_ids as $key_value) {
        $query->condition("sourceid$i", $key_value);
        $i++;
      }

      $count = $query->countQuery()->execute()->fetchField();

      // The query returned at least 1 item, then we will skip this row.
      if ((int) $count > 0) {
        throw new MigrateSkipRowException();
      }
    }
  }

  /**
   * List the migrations that have tables and same number of columns.
   *
   * @param array $source_ids
   *   Keyed array of source ids from the current migration.
   *
   * @return string[]
   *   List of migration names.
   */
  protected function getMigrationConfigs(array $source_ids) {
    // We've already loaded the migrations.
    if (!empty($this->migrations)) {
      return $this->migrations;
    }

    // Load all the config entities for migrations.
    $prefix = 'migrate_plus.migration.';
    $config_names = $this->configFactory->listAll($prefix);
    $schema = $this->database->schema();

    foreach ($config_names as $name) {
      $name = str_replace($prefix, '', $name);

      // Check if the migration table actually exists and if the table has a
      // field that matches the number of source ids. ie `sourceid3`.
      if (
        (isset($this->configuration['migrate_exclude']) && in_array($name, $this->configuration['migrate_exclude'])) ||
        !$schema->tableExists("migrate_map_$name") ||
        !$schema->fieldExists("migrate_map_$name", 'sourceid' . count($source_ids))
      ) {
        continue;
      }

      $this->migrations[] = $name;
    }

    return $this->migrations;
  }

}
