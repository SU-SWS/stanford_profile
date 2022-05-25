<?php

namespace Drupal\Tests\jumpstart_ui\Unit\Plugin\Block;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Session\AccountInterface;
use Drupal\Tests\UnitTestCase;
use Drupal\jumpstart_ui\Plugin\Block\SkipNavAnchorBlock;

/**
 * Class SkipNavAnchorBlockTest
 *
 * @package Drupal\Tests\jumpstart_ui\Unit\Plugin\Block
 * @covers \Drupal\jumpstart_ui\Plugin\Block\SkipNavAnchorBlock
 */
class SkipNavAnchorBlockTest extends UnitTestCase {

  /**
   * The block plugin.
   *
   * @var \Drupal\jumpstart_ui\Plugin\Block\SkipNavAnchorBlock
   */
  protected $block;

  /**
   * The mocked path validator.
   *
   * @var \Drupal\Core\Path\PathValidatorInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $pathValidator;

  /**
   * The mocked path cache_contexts_manager.
   *
   * @var \Drupal\Core\Cache\Context\CacheContextsManager|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $cacheContextsManager;

  /**
   * The mocked account
   *
   * @var \Drupal\Core\Session\AccountInterface||\Prophecy\Prophet
   */
  protected $account;

  /**
   * {@inheritDoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $container = new ContainerBuilder();
    $container->set('string_translation', $this->getStringTranslationStub());

    $this->pathValidator = $this->createMock('Drupal\\Core\\Path\\PathValidatorInterface');
    $container->set('path.validator', $this->pathValidator);

    $this->cacheContextsManager = $this
      ->getMockBuilder('Drupal\\Core\\Cache\\Context\\CacheContextsManager')
      ->disableOriginalConstructor()
      ->getMock();
    $container->set('cache_contexts_manager', $this->cacheContextsManager);

    $this->account = $this->prophesize(AccountInterface::class);

    $this->block = new SkipNavAnchorBlock([], 'jumpstart_ui_skipnav_main_anchor', ['provider' => 'jumpstart_ui']);
    \Drupal::setContainer($container);
  }

  /**
   * Test access to the block. All types with access_content should see it.
   */
  public function testAccess() {
    $this->cacheContextsManager->method('assertValidTokens')->willReturn(TRUE);
    $this->assertTrue($this->block->access($this->account->reveal()));
  }

  /**
   * Test build render array is structured correctly.
   */
  public function testBuild() {
    $build = $this->block->build();
    $this->assertCount(1, $build);
    $this->assertArrayHasKey('anchor', $build);
    $this->assertTrue($build['anchor']['#type'] == 'html_tag');
    $this->assertTrue($build['anchor']['#value'] == 'Main content start');
    $this->assertTrue($build['anchor']['#attributes']['id'] == 'main-content');

    // Build #2
    $build = $this->block->build();
    $this->assertTrue($build['anchor']['#attributes']['id'] == 'main-content--2');
  }

}
