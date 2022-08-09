<?php

namespace Drupal\Tests\stanford_profile_helper\Kernel\Plugin\Field\FieldWidget;

use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Form\FormState;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;
use Drupal\stanford_profile_helper\Plugin\Field\FieldWidget\SamlRoleMappingWidget;
use Drupal\user\Entity\Role;
use Drupal\user\Entity\User;

/**
 * Class SamlRoleMappingWidgetTest.
 *
 * @package Drupal\Tests\stanford_profile_helper\Kernel\Plugin\Field\FieldWidget
 */
class SamlRoleMappingWidgetTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'stanford_profile_helper',
    'node',
    'field',
    'user',
    'text',
    'config_pages',
    'rabbit_hole',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('node');
    $this->installEntitySchema('user');
    $this->installConfig('system');
    $this->installSchema('system', ['sequences']);
    $this->installSchema('node', ['node_access']);

    NodeType::create(['type' => 'page'])->save();
    FieldStorageConfig::create([
      'type' => 'string_long',
      'field_name' => 'su_simplesaml_roles',
      'entity_type' => 'node',
    ])->save();
    FieldConfig::create([
      'field_name' => 'su_simplesaml_roles',
      'entity_type' => 'node',
      'bundle' => 'page',
    ])->save();
    EntityFormDisplay::create([
      'targetEntityType' => 'node',
      'bundle' => 'page',
      'mode' => 'default',
      'status' => TRUE,
    ])->setComponent('su_simplesaml_roles', ['type' => 'saml_role_mapping'])
      ->save();

    Role::create(['id' => 'student', 'label' => 'Student'])->save();
  }

  /**
   * Test Widget application based on field name.
   */
  public function testWidgetApplication() {
    $field = $this->createMock(FieldDefinitionInterface::class);
    $field->method('getName')->willReturn('foo');
    $this->assertFalse(SamlRoleMappingWidget::isApplicable($field));

    $field = $this->createMock(FieldDefinitionInterface::class);
    $field->method('getName')->willReturn('su_simplesaml_roles');
    $this->assertTrue(SamlRoleMappingWidget::isApplicable($field));
  }

  /**
   * Test the field widget form element.
   */
  public function testSamlFieldWidgetForm() {
    /** @var \Drupal\user\UserInterface $user */
    $user = User::create(['name' => $this->randomMachineName()]);
    $user->save();
    $node = Node::create([
      'type' => 'page',
      'title' => 'Foo',
      'su_simplesaml_roles' => 'student:eduPersonEntitlement,=,foo:bar',
    ]);
    $node->setOwner($user);
    $node->save();
    $form = \Drupal::service('entity.form_builder')->getForm($node);
    $this->assertArrayHasKey('su_simplesaml_roles', $form);

    $this->assertArrayHasKey('student:eduPersonEntitlement,=,foo:bar', $form['su_simplesaml_roles']['widget'][0]['role_population']);

    $form_state = new FormState();
    $form_state->setTriggeringElement([
      '#parents' => [
        'su_simplesaml_roles',
        0,
        'role_population',
        'add',
        'add_mapping',
      ],
    ]);
    $this->assertArrayHasKey('widget', SamlRoleMappingWidget::addMapping($form, $form_state));
    $form_state->setValues([
      'su_simplesaml_roles' => [
        [
          'role_population' => [
            'add' => ['workgroup' => 'bar', 'role_id' => 'foo'],
          ],
        ],
      ],
    ]);

    SamlRoleMappingWidget::addMappingCallback($form, $form_state);
    $this->assertTrue(in_array('foo:eduPersonEntitlement,=,bar', $form_state->get('mappings')));

    $form_state->setTriggeringElement(['#mapping' => 'foo:eduPersonEntitlement,=,bar']);
    SamlRoleMappingWidget::removeMappingCallback($form, $form_state);
    $this->assertFalse(in_array('foo:eduPersonEntitlement,=,bar', $form_state->get('mappings')));
  }

  /**
   * The widget massage form values will change the resulting values.
   */
  public function testMassageFormValues() {
    $config = [
      'field_definition' => FieldConfig::load('node.page.su_simplesaml_roles'),
      'settings' => [],
      'third_party_settings' => [],
    ];
    $widget = SamlRoleMappingWidget::create(\Drupal::getContainer(), $config, '', []);
    $values = [
      [
        'role_population' => [
          'foo:eduPersonEntitlement,=,bar' => 'foo:eduPersonEntitlement,=,bar',
          'add' => ['workgroup' => 'foo', 'role_id' => 'bar'],
        ],
      ],
    ];
    $form = [];
    $form_state = new FormState();
    $massaged_values = $widget->massageFormValues($values, $form, $form_state);
    $this->assertEquals('administrator:eduPersonEntitlement,=,uit:sws|foo:eduPersonEntitlement,=,bar|bar:eduPersonEntitlement,=,foo', $massaged_values[0]);
  }

}
