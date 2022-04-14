<?php

namespace Drupal\Tests\stanford_profile_helper\Unit\Plugin\Filter;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\stanford_profile_helper\Plugin\Filter\Mathjax;
use Drupal\Tests\UnitTestCase;

/**
 * Class MathjaxTest.
 *
 * @coversDefaultClass \Drupal\stanford_profile_helper\Plugin\Filter\Mathjax
 */
class MathjaxTest extends UnitTestCase {

  /**
   * The filter shouldn't create extra divs.
   */
  public function testMathjaxFilter() {
    $container = new ContainerBuilder();
    $configs = ['mathjax.settings' => ['config_type' => 0]];
    $container->set('config.factory', $this->getConfigFactoryStub($configs));
    $mathjax = Mathjax::create($container, [], '', ['provider' => 'stanford_profile_helper']);
    $text = '<div>FooBar</div>';
    $processed = $mathjax->process($text, 'en');
    $this->assertEquals($text, (string) $processed);
    $this->assertEmpty($processed->getAttachments());

    $text = '<div>Foo \(a \ne 0\) Bar</div>';
    $processed = $mathjax->process($text, 'en');
    $this->assertEquals($text, (string) $processed);
    $this->assertNotEmpty($processed->getAttachments());

    $text = '<div>Foo \[a \ne 0\] Bar</div>';
    $processed = $mathjax->process($text, 'en');
    $this->assertEquals($text, (string) $processed);
    $this->assertNotEmpty($processed->getAttachments());

    $text = '<div>Foo $$a \ne 0$$ Bar</div>';
    $processed = $mathjax->process($text, 'en');
    $this->assertEquals($text, (string) $processed);
    $this->assertNotEmpty($processed->getAttachments());
  }

}
