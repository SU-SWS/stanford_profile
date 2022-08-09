<?php

namespace Drupal\Tests\jumpstart_ui\Unit\Plugin\paragraphs\Behavior;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Form\FormState;
use Drupal\jumpstart_ui\Plugin\paragraphs\Behavior\ListParagraphBehavior;
use Drupal\paragraphs\ParagraphInterface;
use Drupal\paragraphs\ParagraphsTypeInterface;
use Drupal\Tests\UnitTestCase;

/**
 * Class ListParagraphBehaviorTest
 *
 * @group jumpstart_ui
 * @coversDefaultClass \Drupal\jumpstart_ui\Plugin\paragraphs\Behavior\ListParagraphBehavior
 */
class ListParagraphBehaviorTest extends UnitTestCase {

  /**
   * Keyed array of behavior settings.
   *
   * @var array
   */
  protected $paragraphBehavior = [];

  /**
   * {@inheritDoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $field_manager = $this->createMock(EntityFieldManagerInterface::class);

    $container = new ContainerBuilder();
    $container->set('entity_field.manager', $field_manager);
    $container->set('string_translation', $this->getStringTranslationStub());
    \Drupal::setContainer($container);
  }

  /**
   * Test the behavior methods.
   */
  public function testBehavior() {
    $plugin = ListParagraphBehavior::create(\Drupal::getContainer(), [], '', []);
    $paragraph_type = $this->createMock(ParagraphsTypeInterface::class);
    $paragraph_type->method('id')->willReturn('foo');
    $this->assertFalse($plugin::isApplicable($paragraph_type));

    $paragraph_type = $this->createMock(ParagraphsTypeInterface::class);
    $paragraph_type->method('id')->willReturn('stanford_lists');
    $this->assertTrue($plugin::isApplicable($paragraph_type));

    $form = [];
    $form_state = new FormState();

    $paragraph = $this->createMock(ParagraphInterface::class);
    $paragraph->method('getBehaviorSetting')
      ->will($this->returnCallback([$this, 'getParagraphBehavior']));
    $plugin->buildBehaviorForm($paragraph, $form, $form_state);
    $this->assertNotEmpty($form);

    $build = [];
    $display = $this->createMock(EntityViewDisplayInterface::class);
    $plugin->view($build, $paragraph, $display, 'default');
    $this->assertEquals([], $build);


    $build = ['su_list_view' => [], '#cache' => []];
    $plugin->view($build, $paragraph, $display, 'default');
    $this->assertEquals(['su_list_view' => [], '#cache' => []], $build);

    $build = ['su_list_view' => [], '#cache' => []];
    $this->paragraphBehavior['hide_empty'] = TRUE;
    $plugin->view($build, $paragraph, $display, 'default');
    $this->assertEquals(['#cache' => []], $build);

    $build = ['su_list_view' => [], '#cache' => []];
    $this->paragraphBehavior['hide_empty'] = FALSE;
    $this->paragraphBehavior['empty_message'] = 'Foo Bar Baz';
    $plugin->view($build, $paragraph, $display, 'default');
    $this->assertEquals([
      'su_list_view' => ['#markup' => 'Foo Bar Baz'],
      '#cache' => [],
    ], $build);

  }

  /**
   * Callback for paragraph get behavior setting.
   */
  public function getParagraphBehavior($plugin_id, $key, $default = NULL) {
    return $this->paragraphBehavior[$key] ?? $default;
  }

}
