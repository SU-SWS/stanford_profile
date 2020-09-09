<?php

namespace Drupal\cardinal_service_profile_helper\Form;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class CsvImporterForm.
 */
class CsvImporterForm extends FormBase {

  /**
   * Entity Type Manager Service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * File System service.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * Cache service.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cache;

  /**
   * Database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('file_system'),
      $container->get('cache.default'),
      $container->get('database')
    );
  }

  /**
   * CsvImporterForm constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   Entity Type Manager Service.
   * @param \Drupal\Core\File\FileSystemInterface $fileSystem
   *   File system service.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache
   *   Caching service.
   * @param \Drupal\Core\Database\Connection $database
   *   Database connection service.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager, FileSystemInterface $fileSystem, CacheBackendInterface $cache, Connection $database) {
    $this->entityTypeManager = $entityTypeManager;
    $this->fileSystem = $fileSystem;
    $this->cache = $cache;
    $this->database = $database;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'csv_importer_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['help'] = $this->getHelpText();
    $form['migration'] = [
      '#type' => 'select',
      '#title' => $this->t('Content Type'),
      '#required' => TRUE,
      '#options' => [
        'csv_spotlight' => 'Spotlight',
        'csv_opportunities' => 'Opportunity',
      ],
      '#empty_option' => $this->t('- Select -'),
    ];
    $form['csv'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('CSV File'),
      '#upload_location' => 'temporary://',
      '#upload_validators' => ['file_validate_extensions' => ['csv']],
      '#required' => TRUE,
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#name' => 'import',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
    if ($form_state->getTriggeringElement()['#name'] !== 'import') {
      return;
    }

    /** @var \Drupal\file\FileInterface $file */
    $file = $this->entityTypeManager->getStorage('file')
      ->load($form_state->getValue(['csv', 0]));
    if (!$file || !file_exists($file->getFileUri())) {
      $form_state->setError($form['csv'], $this->t('Unable to load file'));
      return;
    }

    $finput = fopen($file->getFileUri(), 'r');
    $header = fgetcsv($finput);
    fclose($finput);
    $migration = $this->getMigration($form_state->getValue('migration'));

    if (!$migration) {
      $form_state->setError($form['migration'], $this->t('No migration by that name. Please check the configuration.'));
      return;
    }

    $migration_fields = $migration->getSourceConfiguration()['fields'];
    array_walk($migration_fields, function (&$field) {
      $field = $field['selector'];
    });

    foreach ($header as $key => $header_value) {
      $header_value = preg_replace('/ .*?$/', '', $header_value);
      if (!isset($migration_fields[$key]) || $migration_fields[$key] != $header_value) {
        $form_state->setError($form['csv'], $this->t('Invalid headers order.'));
        return;
      }
    }

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $before_count = $this->getNodeCount();
    // Invalidate the migrations so that we can alter the plugin after setting
    // the state for the file path.
    Cache::invalidateTags(['migration_plugins']);
    $db_table = 'migrate_map_' . $form_state->getValue('migration');
    if ($this->database->schema()->tableExists($db_table)) {
      $this->database->truncate($db_table)->execute();
    }

    /** @var \Drupal\file\FileInterface $file */
    $file = $this->entityTypeManager->getStorage('file')
      ->load($form_state->getValue(['csv', 0]));
    $file_path = $this->fileSystem->realpath($file->getFileUri());
    $migration_id = $form_state->getValue('migration');

    // Set the cache for the csv file path for only 4 minutes since it will be
    // fast for the importer.
    $this->cache->set('migration:csv_path', [
      'migration' => $migration_id,
      'path' => $file_path,
    ], time() + 240);

    try {
      $migration = $this->getMigration($migration_id);
      stanford_migrate_execute_migration($migration, $migration->id());
      $file->delete();

      $count = $this->getNodeCount() - $before_count;
      $this->messenger()
        ->addStatus($this->t('Imported %count items.', ['%count' => $count]));
      // @codeCoverageIgnoreStart
    }
    catch (\Exception $e) {
      $this->logger('cardinal_service')
        ->error($this->t('CSV Importer failed: @message', ['@message' => $e->getMessage()]));
      $this->messenger()
        ->addError($this->t('Unable to import CSV. Review the logs for more information'));
    }
    // @codeCoverageIgnoreEnd

    $db_table = 'migrate_map_' . $form_state->getValue('migration');
    if ($this->database->schema()->tableExists($db_table)) {
      $this->database->truncate($db_table)->execute();
    }
  }

  /**
   * Get a list of links for the available importers.
   *
   * @return array
   *   Help text markup.
   */
  protected function getHelpText() {
    $replacements = [
      '@opportunities' => Link::createFromRoute($this->t('Opportunities'), 'cardinal_service.csv_template', ['migration' => 'csv_opportunities'])
        ->toString(),
      '@stories' => Link::createFromRoute($this->t('Spotlight'), 'cardinal_service.csv_template', ['migration' => 'csv_spotlight'])
        ->toString(),
    ];
    $help[] = [
      '#markup' => $this->t('Download an empty CSV template for @opportunities or @stories.', $replacements),
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];
    $help[] = [
      '#markup' => $this->t('Leave the "existing_id" column empty unless you wish to update an existing piece of content. Be aware, this will overwrite all existing field information. For multiple value fields, separate each value with a semicolon.'),
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];
    return $help;
  }

  /**
   * Get a migration object for the given migration id.
   *
   * @param string $migration_id
   *   Migration entity ID.
   *
   * @return \Drupal\migrate\Plugin\MigrationInterface|false
   *   Migration plugin object.
   */
  protected function getMigration($migration_id) {
    try {
      $migrations = stanford_migrate_migration_list();

      foreach ($migrations as $group) {
        if (isset($group[$migration_id])) {
          return $group[$migration_id];
        }
      }
      // @codeCoverageIgnoreStart
    }
    catch (\Exception $e) {
      return FALSE;
    }
    //@codeCoverageIgnoreEnd
  }

  /**
   * Get the number of nodes in the database.
   *
   * @return int
   *   Number of nodes.
   */
  protected function getNodeCount() {
    return (int) $this->database->select('node', 'n')
      ->fields('n')
      ->countQuery()
      ->execute()
      ->fetchField();
  }

}
