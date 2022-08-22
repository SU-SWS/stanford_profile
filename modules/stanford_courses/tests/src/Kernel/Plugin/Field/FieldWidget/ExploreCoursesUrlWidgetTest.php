<?php

namespace Drupal\Tests\stanford_courses\Kernel\Plugin\Field\FieldWidget;

use Drupal\KernelTests\KernelTestBase;
use Drupal\Core\Form\FormState;
use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\node\Entity\NodeType;
use Drupal\node\Entity\Node;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\ResponseInterface;
use Drupal\Core\Field\FieldDefinitionInterface;

use Drupal\stanford_courses\Plugin\Field\FieldWidget\ExploreCoursesUrlWidget;

/**
 * Class ExploreCoursesUrlWidgetTest.php.
 *
 * @group stanford_courses
 * @coversDefaultClass \Drupal\stanford_courses\Plugin\Field\FieldWidget\ExploreCoursesUrlWidget
 */
class ExploreCoursesUrlWidgetTest extends KernelTestBase {

  /**
   * {@inheritDoc}.
   */
  public static $modules = [
    'system',
    'node',
    'user',
    'link',
    'field',
    'stanford_courses',
  ];

  /**
   * {@inheritDoc}.
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('user');
    $this->installEntitySchema('node');
    $this->installConfig(['system', 'field', 'link']);

    NodeType::create(['type' => 'page'])->save();

    $field_storage = FieldStorageConfig::create([
      'field_name' => 'su_explore_course_url',
      'entity_type' => 'node',
      'type' => 'link',
      'cardinality' => -1,
    ]);
    $field_storage->save();

    $field = FieldConfig::create([
      'field_name' => 'su_explore_course_url',
      'entity_type' => 'node',
      'bundle' => 'page',
    ]);
    $field->save();

    $guzzle_client = $this->createMock(ClientInterface::class);
    $guzzle_client->method('requestAsync')
      ->will($this->returnCallback([$this, 'requestAsyncCallback']));

  }

  /**
   * Test the settings form
   */
  public function testSettingsForm() {
    $field_def = $this->createMock(FieldDefinitionInterface::class);
    $config = [
      'field_definition' => $field_def,
      'settings' => [],
      'third_party_settings' => [],
    ];
    $definition = [];
    $widget = ExploreCoursesUrlWidget::create(\Drupal::getContainer(), $config, '', $definition);

    $summary = $widget->settingsSummary();
    $this->assertCount(1, $summary);
    $this->assertEquals('API version: 20200810', (string) $summary[0]);

    $config = [
      'field_definition' => $field_def,
      'settings' => [
        'api_version' => '20200810',
      ],
      'third_party_settings' => [],
    ];

    $widget = ExploreCoursesUrlWidget::create(\Drupal::getContainer(), $config, '', $definition);
    $summary = $widget->settingsSummary();
    $this->assertCount(1, $summary);
    $this->assertEquals('API version: 20200810', (string) $summary[0]);

    $form = [];
    $form_state = new FormState();
    $element = $widget->settingsForm($form, $form_state);
    $element['#parents'] = [];

    $widget->validateApi($element, $form_state, $form);
    $this->assertCount(1, $form_state->getErrors());

  }

  /**
   * Test Url Validation.
   */
  public function testUrlValidation() {
    $field_def = $this->createMock(FieldDefinitionInterface::class);
    $config = [
      'field_definition' => $field_def,
      'settings' => [],
      'third_party_settings' => [],
    ];
    $definition = [];
    $widget = ExploreCoursesUrlWidget::create(\Drupal::getContainer(), $config, '', $definition);
    $element = ['#value' => '', '#parents' => []];
    $form = [];
    $form_state = new FormState();
    $widget->validateUrl($element, $form_state, $form);
    $this->assertCount(0, $form_state->getErrors());
    $element['#value'] = "https://explorecourses.stanford.edu?test=test";
    $widget->validateUrl($element, $form_state, $form);
    $this->assertCount(0, $form_state->getErrors());
    $element['#value'] = "https://bad-data.com";
    $widget->validateUrl($element, $form_state, $form);
    $this->assertCount(1, $form_state->getErrors());
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
    $entity_form_display->setComponent('su_explore_course_url', ['type' => 'explore_courses_url', 'settings' => ['api_version' => '20200810']])
      ->removeComponent('created')
      ->save();

    $node->set('su_explore_course_url', [
      [
        'uri' => 'https://explorecourses.stanford.edu/search?view=catalog',
        'title' => '',
        'options' => '',
      ],
    ]);

    $form = [];
    $form_state = new FormState();
    $entity_form_display->buildForm($node, $form, $form_state);
    $widget_value = $form['su_explore_course_url']['widget'][0];

    $this->assertIsArray($widget_value);
    $this->assertEquals($widget_value['uri']['#default_value'], 'https://explorecourses.stanford.edu/search?view=catalog');

    $field_def = $this->createMock(FieldDefinitionInterface::class);
    $config = [
      'field_definition' => $field_def,
      'settings' => [
        'api_version' => '20200810',
      ],
      'third_party_settings' => [],
    ];

    $widget = ExploreCoursesUrlWidget::create(\Drupal::getContainer(), $config, '', []);
    $form = [];
    $form_state = new FormState();

    $massaged_values = $widget->massageFormValues([0 => ['uri' => 'https://explorecourses.stanford.edu/search?q=all%20courses&view=catalog&page=0']], $form, $form_state);
    $this->assertCount(1, $massaged_values);
    $this->assertEquals('https://explorecourses.stanford.edu/search?q=all%20courses&view=xml-20200810&page=0', $massaged_values[0]['uri']);

  }

  /**
   * Provide a little xml needed by the settings form validator.
   */
  public function requestAsyncCallback($method, $uri, $options) {
    $data = "<xml><deprecated>false</deprecated><latestVersion>20200810</latestVersion></xml>";
    $response = $this->createMock(ResponseInterface::class);
    $response->method('getBody')->willReturn($data);

    $promise = $this->createMock(PromiseInterface::class);
    $promise->method('wait')->willReturn($response);
    return $promise;
  }

}
