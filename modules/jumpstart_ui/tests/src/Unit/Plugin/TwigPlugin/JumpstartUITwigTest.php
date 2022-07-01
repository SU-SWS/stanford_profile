<?php

namespace Drupal\Tests\jumpstart_ui\Unit\Plugin\TwigPlugin;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Render\Markup;
use Drupal\Core\Render\RendererInterface;
use Drupal\Tests\UnitTestCase;
use Drupal\jumpstart_ui\Plugin\TwigPlugin\JumpstartUITwig;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Class JumpstartUITwigTest
 *
 * @package Drupal\Tests\jumpstart_ui\Unit\Plugin\TwigPlugin
 * @covers \Drupal\jumpstart_ui\Plugin\TwigPlugin\JumpstartUITwig
 */
class JumpstartUITwigTest extends UnitTestCase {

  /**
   * Twig extension plugin instance.
   *
   * @var \Drupal\jumpstart_ui\Plugin\TwigPlugin\JumpstartUITwig
   */
  protected $twiggery;

  /**
   * {@inheritDoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $container = new ContainerBuilder();
    $container->set('string_translation', $this->getStringTranslationStub());

    $renderer = $this->createMock(RendererInterface::class);
    $renderer->method('render')->will($this->returnCallback(function($arg){
      return $arg['#markup'] ?? $arg;
    }));
    $this->twiggery = new JumpstartUITwig($renderer);

    \Drupal::setContainer($container);
  }

  /**
   * Test getUniqueId generation.
   */
  public function testgetUniqueId() {
    $key = "test";
    $this->assertEquals('test', $this->twiggery->getUniqueId($key));
    $this->assertEquals('test--2', $this->twiggery->getUniqueId($key));
    $this->assertEquals('test--3', $this->twiggery->getUniqueId($key));

    $def1 = $this->twiggery->getUniqueId();
    $def2 = $this->twiggery->getUniqueId();
    $this->assertStringContainsString('jumpstart-ui-', $def1);
    $this->assertStringContainsString('jumpstart-ui-', $def2);
    $this->assertNotEquals($def1, $def2);
  }

  /**
   * Ensure we packed all our bags.
   */
  public function testGetFunctions() {
    $functs = $this->twiggery->getFunctions();
    $this->assertInstanceOf(TwigFunction::class, $functs[0]);
    $this->assertEquals('getUniqueId', $functs[0]->getName());

    $filters = $this->twiggery->getFilters();
    $this->assertInstanceOf(TwigFilter::class, $filters[0]);
    $this->assertEquals('render_clean', $filters[0]->getName());
  }

  /**
   * Run the render_clean filter.
   */
  public function testsCleanFilter() {
    $markup = '<div><a><span><article><section>test</section></article></span></a>';
    $markup = Markup::create($markup);
    $this->assertEquals(['#markup' => 'test'], $this->twiggery->renderClean($markup));

    $markup = '<div><a><span><article><section>test</section></article></span></a>';
    $this->assertEquals(['#markup' => '<span>test</span>'], $this->twiggery->renderClean($markup, '<span>'));
  }

}
