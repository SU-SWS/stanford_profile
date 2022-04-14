<?php

namespace Drupal\Tests\jumpstart_ui\Unit\Plugin\paragraphs\Behavior;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Entity\Display\EntityDisplayInterface;
use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\Core\Form\FormState;
use Drupal\jumpstart_ui\Plugin\paragraphs\Behavior\HeroPatternBehavior;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\paragraphs\Entity\ParagraphsType;
use Drupal\paragraphs\ParagraphInterface;
use Drupal\Tests\UnitTestCase;

/**
 * Class HeroPatternBehaviorTest
 *
 * @group jumpstart_ui
 * @coversDefaultClass \Drupal\jumpstart_ui\Plugin\paragraphs\Behavior\HeroPatternBehavior
 */
class HeroPatternBehaviorTest extends UnitTestCase {

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
   * The paragraph behavior should only be available to hero pattern displays.
   */
  public function testApplication() {
    $display_ids = [];

    $query = $this->createMock(QueryInterface::class);
    $query->method('condition')->willReturnSelf();
    $query->method('execute')->willReturnReference($display_ids);

    $entity_storage = $this->createMock(EntityStorageInterface::class);
    $entity_storage->method('getQuery')->wilLReturn($query);
    $entity_storage->method('loadMultiple')
      ->will($this->returnCallback([$this, 'loadMultipleDisplayCallback']));

    $entity_manager = $this->createMock(EntityTypeManagerInterface::class);
    $entity_manager->method('getStorage')->willReturn($entity_storage);

    $container = new ContainerBuilder();
    $container->set('entity_type.manager', $entity_manager);
    \Drupal::setContainer($container);

    $paragraph_type = $this->createMock(ParagraphsType::class);
    $paragraph_type->method('id')->willReturn('foo');
    $this->assertFalse(HeroPatternBehavior::isApplicable($paragraph_type));

    $display_ids = ['paragraph.foo.not_hero'];
    $this->assertFalse(HeroPatternBehavior::isApplicable($paragraph_type));

    $display_ids = ['paragraph.foo.hero'];
    $this->assertTrue(HeroPatternBehavior::isApplicable($paragraph_type));
  }

  public function testForm() {
    $plugin = HeroPatternBehavior::create(\Drupal::getContainer(), [], '', []);
    $paragraph = $this->createMock(ParagraphInterface::class);
    $paragraph->method('getBehaviorSetting')->willReturn('right');
    $form = [];
    $form_state = new FormState();
    $form = $plugin->buildBehaviorForm($paragraph, $form, $form_state);
    $this->assertArrayHasKey('overlay_position', $form);
    $this->assertEquals('right', $form['overlay_position']['#default_value']);
  }

  public function testView() {
    $plugin = HeroPatternBehavior::create(\Drupal::getContainer(), [], '', []);
    $build = [];
    $paragraph = $this->createMock(Paragraph::class);
    $paragraph->method('getBehaviorSetting')->willReturn('right');
    $display = $this->createMock(EntityViewDisplayInterface::class);
    $plugin->view($build, $paragraph, $display, 'foo');
    $this->assertEquals('overlay-right', $build['#attributes']['class'][0]);
  }

  /**
   * Load and get mock display entities.
   *
   * @param array $ids
   *   Array of display ids.
   *
   * @return array
   *   Keyed array of mock displays.
   */
  public function loadMultipleDisplayCallback($ids = []) {
    $return = [];
    foreach ($ids as $id) {
      $ds_settings = NULL;
      switch ($id) {
        case 'paragraph.foo.hero':
          $ds_settings = ['id' => 'pattern_hero'];
          break;
      }
      $return[$id] = $this->createMock(EntityDisplayInterface::class);
      $return[$id]->method('getThirdPartySetting')
        ->willReturn($ds_settings);
    }
    return $return;
  }

}