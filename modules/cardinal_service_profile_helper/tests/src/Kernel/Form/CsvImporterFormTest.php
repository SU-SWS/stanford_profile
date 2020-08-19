<?php

namespace Drupal\Tests\cardinal_service_profile_helper\Kernel\Form;

use Drupal\cardinal_service_profile_helper\Form\CsvImporterForm;
use Drupal\Core\Form\FormState;
use Drupal\file\Entity\File;
use Drupal\KernelTests\KernelTestBase;
use Drupal\migrate_plus\Entity\Migration;
use Drupal\migrate_plus\Entity\MigrationGroup;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;

/**
 * Class CsvImporterFormTest.
 *
 * @group cardinal_service_profile_helper
 * @coversDefaultClass \Drupal\cardinal_service_profile_helper\Form\CsvImporterForm
 */
class CsvImporterFormTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'cardinal_service_profile_helper',
    'node',
    'field',
    'user',
    'file',
    'stanford_migrate',
    'migrate_plus',
    'migrate',
    'migrate_source_csv',
  ];

  /**
   * Create file entity.
   *
   * @var \Drupal\file\FileInterface
   */
  protected $file;

  /**
   * {@inheritDoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installEntitySchema('file');
    $this->installEntitySchema('node');
    $this->installEntitySchema('user');
    $this->installEntitySchema('migration');
    $this->installSchema('file', ['file_usage']);

    NodeType::create([
      'type' => 'su_spotlight',
      'label' => 'Spotlights',
    ])->save();


    $this->file = File::create([
      'uri' => 'temporary://test.csv',
    ]);
    $this->file->save();
    file_put_contents($this->file->getFileUri(), "foo,bar\nbaz,foo");
  }

  /**
   * The form should have some type of structure.
   */
  public function testFormStructure() {
    $form = \Drupal::formBuilder()->getForm(CsvImporterForm::class);
    $this->assertArrayHasKey('csv', $form);
    $this->assertArrayHasKey('migration', $form);
  }

  /**
   * The form will validate the csv is the correct structure.
   */
  public function testFormValidation() {
    $form_state = new FormState();
    $form = \Drupal::formBuilder()
      ->buildForm(CsvImporterForm::class, $form_state);

    $form_state->setTriggeringElement(['#name' => 'foo_bar']);
    $form_state->getFormObject()->validateForm($form, $form_state);
    $this->assertFalse($form_state::hasAnyErrors());

    $form_state->setTriggeringElement(['#name' => 'import']);
    $form_state->setValues(['csv' => [9999], 'migration' => 'csv_spotlight']);
    $form_state->getFormObject()->validateForm($form, $form_state);
    $this->assertTrue($form_state::hasAnyErrors());

    $form_state->clearErrors();
    $form_state->setValue(['csv', 0], $this->file->id());
    $form_state->getFormObject()->validateForm($form, $form_state);
    $this->assertTrue($form_state::hasAnyErrors());

    $this->createMigrationEntity();
    file_put_contents($this->file->getFileUri(), "id,status,title\n1,0,foo\n2,1,bar");
    $form_state->clearErrors();
    $form_state->getFormObject()->validateForm($form, $form_state);
    $this->assertTrue($form_state::hasAnyErrors());

    file_put_contents($this->file->getFileUri(), "id,title,status\n1,foo,1\n2,bar,1");
    $form_state->clearErrors();
    $form_state->getFormObject()->validateForm($form, $form_state);
    $this->assertFalse($form_state::hasAnyErrors());
  }

  /**
   * Submitting the form will create some nodes.
   */
  public function testFormSubmit() {
    $this->assertCount(0, Node::loadMultiple());
    $this->createMigrationEntity();
    file_put_contents($this->file->getFileUri(), "id,title,status\n1,foo,1\n2,bar,1");
    $form_state = new FormState();

    $form = \Drupal::formBuilder()
      ->buildForm(CsvImporterForm::class, $form_state);

    $form_state->setValues([
      'csv' => [$this->file->id()],
      'migration' => 'csv_spotlight',
    ]);
    $form_state->getFormObject()->submitForm($form, $form_state);

    $this->assertCount(2, Node::loadMultiple());
  }

  /**
   * Create a migration entity for testing.
   */
  protected function createMigrationEntity() {
    MigrationGroup::create(['id' => 'foo_bar'])->save();

    Migration::create([
      'id' => 'csv_spotlight',
      'migration_group' => 'foo_bar',
      'migration_tags' => [],
      'migration_dependencies' => [],
      'source' => [
        'plugin' => 'csv',
        'path' => $this->file->getFileUri(),
        'ids' => ['id'],
        'constants' => ['type' => 'su_spotlight'],
        'fields' => [
          ['name' => 'id', 'selector' => 'id'],
          ['name' => 'title', 'selector' => 'title'],
          ['name' => 'status', 'selector' => 'status'],
        ],
      ],
      'process' => [
        'type' => 'constants/type',
        'title' => 'title',
        'status' => 'status,',
      ],
      'destination' => ['plugin' => 'entity:node'],
    ])->save();
    drupal_flush_all_caches();
  }

}
