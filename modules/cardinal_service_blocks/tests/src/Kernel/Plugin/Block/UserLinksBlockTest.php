<?php

namespace Drupal\Tests\cardinal_service_blocks\Kernel\Plugin\Block;

use Drupal\cardinal_service_blocks\Plugin\Block\UserLinksBlock;
use Drupal\Core\Session\AccountInterface;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\user\Traits\UserCreationTrait;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UserLinksBlockTest.
 *
 * @group cardinal_service_blocks
 * @coversDefaultClass \Drupal\cardinal_service_blocks\Plugin\Block\UserLinksBlock
 */
class UserLinksBlockTest extends KernelTestBase {

  use UserCreationTrait;

  /**
   * {@inheritDoc}
   */
  protected static $modules = [
    'cardinal_service_blocks',
    'system',
    'block',
    'user',
    'field',
    'text',
  ];

  /**
   * Block object.
   *
   * @var \Drupal\cardinal_service_blocks\Plugin\Block\UserLinksBlock
   */
  protected $block;

  /**
   * {@inheritDoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installSchema('system', ['sequences']);
    $this->installEntitySchema('user');

    FieldStorageConfig::create([
      'field_name' => 'su_display_name',
      'entity_type' => 'user',
      'type' => 'text',
      'cardinality' => 1,
    ])->save();
    FieldConfig::create([
      'entity_type' => 'user',
      'field_name' => 'su_display_name',
      'bundle' => 'user',
      'label' => 'Display Name',
    ])->save();

    $this->block = UserLinksBlock::create(\Drupal::getContainer(), [], '', ['provider' => 'cardinal_service_blocks']);
  }

  /**
   * Cache contexts and tags should be valid.
   */
  public function testLinksBlockCaches() {
    $contexts = $this->block->getCacheContexts();
    $this->assertTrue(in_array('url.path', $contexts));
    $this->assertTrue(in_array('url.query_args', $contexts));
    $this->assertEmpty($this->block->getCacheTags());

    $new_account = $this->createMock(AccountInterface::class);
    $new_account->method('isAuthenticated')->willReturn(TRUE);
    $new_account->method('id')->willReturn(123);
    \Drupal::currentUser()->setAccount($new_account);
    $this->assertNotEmpty($this->block->getCacheTags());
  }

  /**
   * Anonymous users get a plain link.
   */
  public function testLoggedOutUser() {
    $build = $this->block->build();
    $this->assertEqual($build['#type'], 'link');
    $this->assertEqual($build['#url']->getRouteName(), 'user.login');
    $this->assertEmpty($build['#url']->getOption('query'));

    $destination = '/opportunities?foo=bar';
    $request = Request::create($destination);
    \Drupal::requestStack()->push($request);

    $build = $this->block->build();
    $this->assertNotEmpty($build['#url']->getOption('query'));
    $this->assertEqual($build['#url']->getOption('query')['destination'], htmlspecialchars('/opportunities?foo=bar'));
  }

  /**
   * Authenticated users get the dropbutton.
   */
  public function testAuthenticatedUser() {
    \Drupal::currentUser()->setAccount($this->createUser([]));
    $build = $this->block->build();
    $this->assertEqual($build['#type'], 'dropbutton');
    $this->assertCount(4, $build['#links']);
  }

}
