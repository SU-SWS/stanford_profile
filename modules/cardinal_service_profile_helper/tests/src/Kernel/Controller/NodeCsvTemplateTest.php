<?php

namespace Drupal\Tests\cardinal_service_profile_helper\Kernel\Controller;

use Drupal\cardinal_service_profile_helper\Controller\NodeCsvTemplate;
use Drupal\KernelTests\KernelTestBase;
use Drupal\migrate_plus\Entity\MigrationInterface;

/**
 * Class NodeCsvTemplateTest
 *
 * @group cardinal_service_profile
 * @coversDefaultClass \Drupal\cardinal_service_profile_helper\Controller\NodeCsvTemplate
 */
class NodeCsvTemplateTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'cardinal_service_profile_helper',
  ];

  /**
   * Mock migration entity.
   *
   * @var \Drupal\migrate_plus\Entity\MigrationInterface
   */
  protected $migration;

  /**
   * {@inheritDoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->migration = $this->createMock(MigrationInterface::class);
    $this->migration->method('id')->willReturn('page');
    $this->migration->source = ['fields' => [
      ['label' => 'title','selector' => 'title'],
      ['label' => 'field_foo','selector' => 'field_foo'],
      ['label' => 'field_bar','selector' => 'field_bar'],
      ['label' => 'field_baz','selector' => 'field_baz'],
    ]];
  }

  /**
   * The controller should provide a CSV file.
   */
  public function testTemplateController() {
    $controller = NodeCsvTemplate::create(\Drupal::getContainer());
    $response = $controller->getTemplate($this->migration);

    $this->assertEquals('text/csv', $response->headers->get('Content-Type'));
    $this->assertEqual('title (title),field_foo (field_foo),field_bar (field_bar),field_baz (field_baz)', file_get_contents('temporary://page.csv'));
  }

}
