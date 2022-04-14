<?php

namespace Drupal\Tests\jumpstart_ui\Unit\Layouts;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Form\FormState;
use Drupal\jumpstart_ui\Layouts\JumpstartUiLayouts;
use Drupal\Tests\UnitTestCase;

/**
 * Class JumpstartUiLayoutsTest.
 *
 * @group jumpstart_ui
 * @coversDefaultClass \Drupal\jumpstart_ui\Layouts\JumpstartUiLayouts
 */
class JumpstartUiLayoutsTest extends UnitTestCase {

  /**
   * {@inheritDoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $container = new ContainerBuilder();
    $container->set('string_translation', $this->getStringTranslationStub());
    \Drupal::setContainer($container);
  }

  /**
   * The form class should save the values appropriately.
   */
  public function testLayoutForm() {
    $object = new JumpstartUiLayouts(['label' => ''], '', []);
    $this->assertArrayHasKey('extra_classes', $object->defaultConfiguration());
    $this->assertArrayHasKey('centered', $object->defaultConfiguration());
    $this->assertArrayHasKey('columns', $object->defaultConfiguration());

    $form = [];
    $form_state = new FormState();
    $form = $object->buildConfigurationForm($form, $form_state);

    $this->assertArrayHasKey('extra_classes', $form);
    $this->assertArrayHasKey('centered', $form);
    $this->assertArrayHasKey('columns', $form);
    $this->assertArrayHasKey('label', $form);

    $form_state->setValue('extra_classes', 'foo bar_baz');
    $form_state->setValue('centered', FALSE);
    $form_state->setValue('columns', 'flex-6-of-12');
    $form_state->setValue('label', 'Admin Label');

    $object->submitConfigurationForm($form, $form_state);
    $config = $object->getConfiguration();

    $this->assertEquals('foo bar-baz', $config['extra_classes']);
    $this->assertEquals('flex-6-of-12', $config['columns']);
    $this->assertEquals('Admin Label', $config['label']);
    $this->assertNull($config['centered']);

    $form_state->setValue('extra_classes', '@!:fooBar?');
    $form_state->setValue('centered', TRUE);

    $object->submitConfigurationForm($form, $form_state);
    $config = $object->getConfiguration();

    $this->assertEquals('fooBar', $config['extra_classes']);
    $this->assertEquals('centered-container', $config['centered']);
  }

}
