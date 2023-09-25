<?php

namespace Drupal\Tests\cardinal_service_blocks\Kernel\Plugin\Block;

use Drupal\cardinal_service_blocks\Plugin\Block\NewsletterBlock;
use Drupal\Core\Form\FormState;
use Drupal\KernelTests\KernelTestBase;

/**
 * Class NewsletterBlockTest
 *
 * @group cardinal_service_profile
 * @coversDefaultClass \Drupal\cardinal_service_blocks\Plugin\Block\NewsletterBlock
 */
class NewsletterBlockTest extends KernelTestBase {

  /**
   * {@inheritDoc}
   */
  protected static $modules = [
    'cardinal_service_blocks',
    'system',
    'block',
  ];

  public function testBlock() {
    $block = NewsletterBlock::create(\Drupal::getContainer(), [], '', ['provider' => 'foo']);
    $this->assertEquals('https://stanford.us1.list-manage.com/subscribe/post?u=a77525a849b0888cf8d90460f&id=807864fbe3', $block->defaultConfiguration()['url']);

    $form = [];
    $form_state = new FormState();
    $this->assertCount(2, $block->blockForm($form, $form_state));

    $form_state->setValues([
      'url' => 'http://google.com',
      'intro' => ['value' => 'Lorem Ipsum', 'format' => 'html'],
    ]);

    $block->blockSubmit($form, $form_state);
    $this->assertEquals('http://google.com', $block->getConfiguration()['url']);
    $this->assertEquals('Lorem Ipsum', $block->getConfiguration()['intro']['value']);
    $this->assertEquals('html', $block->getConfiguration()['intro']['format']);

    $build = $block->build();
    $this->assertCount(2, $build);
    $this->assertEquals('http://google.com', $build['form']['#action']);
  }

}
