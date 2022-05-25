<?php

namespace Drupal\Tests\stanford_events_importer\Kernel\Plugin\Field\FieldWidget;

use Drupal\KernelTests\KernelTestBase;
use Drupal\Core\Form\FormState;
use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\node\Entity\NodeType;
use Drupal\node\Entity\Node;
use Drupal\stanford_events_importer\StanfordEventsImporter;
use Drupal\Core\Cache\CacheBackendInterface;

/**
 * Class StanfordEventsImporterAPIURLFieldWidgetTest.php
 *
 * @group stanford_events_importer
 * @coversDefaultClass \Drupal\stanford_events_importer\Plugin\Field\FieldWidget\StanfordEventsImporterAPIURLFieldWidget
 */
class StanfordEventsImporterAPIURLFieldWidgetTest extends KernelTestBase {

  /**
   * {@inheritDoc}
   */
  public static $modules = [
    'system',
    'node',
    'user',
    'link',
    'field',
    'stanford_events',
    'stanford_events_importer',
  ];

  /**
   * {@inheritDoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('user');
    $this->installEntitySchema('node');
    $this->installConfig(['system', 'field', 'link']);

    NodeType::create(['type' => 'page'])->save();

    $field_storage = FieldStorageConfig::create([
      'field_name' => 'su_event_xml_url',
      'entity_type' => 'node',
      'type' => 'link',
      'cardinality' => -1,
    ]);
    $field_storage->save();

    $field = FieldConfig::create([
      'field_name' => 'su_event_xml_url',
      'entity_type' => 'node',
      'bundle' => 'page',
    ]);
    $field->save();

    // Set the cache so we don't have to fetch xml as we have other tests
    // for that.
    $key_val = [
      'one' => 'One',
      'two' => 'Two',
      'three' => 'Three',
    ];

    // Categories.
    \Drupal::cache()
      ->set(StanfordEventsImporter::CACHE_KEY_CAT, $key_val, CacheBackendInterface::CACHE_PERMANENT, ['stanford_events_importer']);

    // Orgs.
    \Drupal::cache()
      ->set(StanfordEventsImporter::CACHE_KEY_ORG, $key_val, CacheBackendInterface::CACHE_PERMANENT, ['stanford_events_importer']);

  }

  /**
   * Test the entity form is displayed correctly.
   */
  public function testWidgetForm() {
    $node = Node::create([
      'type' => 'page',
    ]);
    /** @var \Drupal\Core\Entity\Display\EntityFormDisplayInterface $entity_form_display */
    $entity_form_display = EntityFormDisplay::create([
      'targetEntityType' => 'node',
      'bundle' => 'page',
      'mode' => 'default',
      'status' => TRUE,
    ]);
    $entity_form_display->setComponent('su_event_xml_url', ['type' => 'stanford_events_importer_apiurl_field_widget'])
      ->removeComponent('created')
      ->save();

    $node->set('su_event_xml_url', [
      [
        'uri' => 'https://events-legacy.stanford.edu/xml/drupal/v2.php?featured',
        'title' => '',
        'options' => ''
      ],
      [
        'uri' => 'https://events-legacy.stanford.edu/xml/drupal/v2.php?today',
        'title' => '',
        'options' => ''
      ],
      [
        'uri' => 'https://events-legacy.stanford.edu/xml/drupal/v2.php?organization=23&bookmarked',
        'title' => '',
        'options' => ''
      ],
      [
        'uri' => 'https://events-legacy.stanford.edu/xml/drupal/v2.php?category=23',
        'title' => '',
        'options' => ''
      ],
    ]);

    $form = [];
    $form_state = new FormState();


    $entity_form_display->buildForm($node, $form, $form_state);

    $widget_value = $form['su_event_xml_url']['widget'][0];
    $this->assertIsArray($widget_value);
    $this->assertEquals($widget_value['_other']['organization']['#options']['one'], "One");
    $this->assertEquals($widget_value['_other']['category']['#options']['two'], "Two");
  }

}
