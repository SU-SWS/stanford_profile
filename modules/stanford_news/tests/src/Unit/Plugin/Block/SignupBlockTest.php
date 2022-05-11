<?php

namespace Drupal\Tests\stanford_news\Unit\Plugin\Block;

use Drupal\Core\Form\FormState;
use Drupal\Core\Session\AccountInterface;
use Drupal\stanford_news\Plugin\Block\SignupBlock;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class SignupBlockTest.
 *
 * @group stanford_news
 * @coversDefaultClass \Drupal\stanford_news\Plugin\Block\SignupBlock
 */
class SignupBlockTest extends UnitTestCase {

  /**
   * @var \Drupal\stanford_news\Plugin\Block\SignupBlock
   */
  protected $blockObject;

  /**
   * {@inheritDoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $config = [
      "id" => "signup_block",
      "label" => "Newsletter Signup",
      "label_display" => "visible",
      "form_action" => "my-form-action",
    ];
    $this->blockObject = new SignupBlock($config, '', ["provider" => "stanford_news"]);

    $container = new ContainerBuilder();
    $container->set('string_translation', $this->getStringTranslationStub());
    \Drupal::setContainer($container);
  }

  public function testBuild() {
    $build = $this->blockObject->build();
    $this->assertArrayEquals([
      '#theme' => 'signup_block',
      '#form_action' => 'my-form-action',
    ], $build);
  }

  public function testAccess() {
    $account = $this->createMock(AccountInterface::class);
    $this->assertTrue($this->blockObject->access($account));

    $config = $this->blockObject->getConfiguration();
    $config['form_action'] = NULL;
    $this->blockObject->setConfiguration($config);
    $this->assertFalse($this->blockObject->access($account));
  }

  public function testForm() {
    $form = [];
    $form_state = new FormState();
    $block_form = $this->blockObject->blockForm($form, $form_state);
    $this->assertArrayHasKey('form_action', $block_form);

    $form_state->setValue('form_action', 'foo-bar');
    $this->blockObject->blockSubmit($block_form, $form_state);
    $this->assertEquals('foo-bar', $this->blockObject->getConfiguration()['form_action']);
  }

}
