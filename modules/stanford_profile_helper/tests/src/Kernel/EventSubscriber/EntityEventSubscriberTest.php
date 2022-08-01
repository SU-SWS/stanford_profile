<?php

namespace Drupal\Tests\stanford_profile_helper\Kernel\EventSubscriber;

use Drupal\Core\Render\RenderContext;
use Drupal\KernelTests\KernelTestBase;
use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;
use Drupal\node\NodeInterface;
use Drupal\path_alias\Entity\PathAlias;
use Drupal\preprocess_event_dispatcher\Event\NodePreprocessEvent;
use Drupal\redirect\Entity\Redirect;
use Drupal\stanford_profile_helper\StanfordDefaultContentInterface;
use Drupal\stanford_profile_helper\StanfordProfileHelper;

/**
 * Test the event subscriber.
 *
 * @coversDefaultClass \Drupal\stanford_profile_helper\EventSubscriber\EntityEventSubscriber
 */
class EntityEventSubscriberTest extends KernelTestBase {

  /**
   * {@inheritDoc}
   */
  protected static $modules = [
    'config_pages',
    'core_event_dispatcher',
    'hook_event_dispatcher',
    'preprocess_event_dispatcher',
    'default_content',
    'hal',
    'node',
    'serialization',
    'stanford_profile_helper',
    'system',
    'user',
    'path_alias',
    'rabbit_hole',
    'rh_node',
    'menu_link_content',
    'link',
    'redirect',
  ];

  /**
   * {@inheritDoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installEntitySchema('node');
    $this->installEntitySchema('user');
    $this->installEntitySchema('path_alias');
    $this->installEntitySchema('menu_link_content');
    $this->installEntitySchema('redirect');
    $this->installConfig('system');
    $this->setInstallProfile('test_stanford_profile_helper');

    NodeType::create(['type' => 'stanford_event', 'name' => 'Event'])->save();

    $entity = $this->createMock(NodeInterface::class);
    $entity->method('label')->willReturn('Foo Bar');

    $default_content_mock = $this->createMock(StanfordDefaultContentInterface::class);
    $default_content_mock->method('createDefaultContent')
      ->willReturnReference($entity);

    $container = \Drupal::getContainer();
    $container->set('stanford_profile_helper.default_content', $default_content_mock);
    \Drupal::setContainer($container);
  }

  /**
   * Entity Pre-save event listener.
   */
  public function testNodePresave() {
    $nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple();
    $this->assertEmpty($nodes);
    $node = Node::create(['type' => 'stanford_event', 'title' => 'Foo Bar']);
    $node->save();
    $nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple();
    $this->assertCount(2, $nodes);

    \Drupal::state()
      ->delete('stanford_profile_helper.default_content.stanford_event');
    $node = Node::create(['type' => 'stanford_event', 'title' => 'Bar Foo']);
    $node->save();
    $nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple();
    $this->assertCount(3, $nodes);

    $variables = [
      'node' => $node,
      'page' => FALSE,
      'content' => [],
    ];
    $service = \Drupal::service('preprocess_event.service');
    $service->createAndDispatchKnownEvents('node', $variables);
    $this->assertArrayNotHasKey('rh_message', $variables['content']);

    $rabbit_hole_behavior = \Drupal::entityTypeManager()
      ->getStorage('behavior_settings')
      ->create([
        'id' => 'node_type_stanford_event',
        'action' => 'access_denied',
        'redirect' => 'http://foo.bar',
        'redirect_code' => 301,
      ]);
    $rabbit_hole_behavior->save();

    $variables['page'] = TRUE;;
    $service->createAndDispatchKnownEvents('node', $variables);
    $this->assertArrayNotHasKey('rh_message', $variables['content']);

    $rabbit_hole_behavior->set('action', 'page_redirect')->save();

    $service->createAndDispatchKnownEvents('node', $variables);
    $this->assertArrayHasKey('rh_message', $variables['content']);
  }

  /**
   * Test menu item events.
   */
  public function testMenuItems() {
    $node = Node::create(['type' => 'stanford_event', 'title' => 'Foo Bar']);
    $node->save();
    PathAlias::create([
      'path' => '/node/' . $node->id(),
      'alias' => '/foo/bar',
    ])->save();

    $parent_item = MenuLinkContent::create([
      'title' => 'Parent',
      'description' => 'Llama Gabilondo',
      'link' => 'entity:node/' . $node->id(),
      'weight' => 0,
      'menu_name' => 'main',
    ]);
    $parent_item->save();

    $menu_item = MenuLinkContent::create([
      'title' => 'Llama Gabilondo',
      'description' => 'Llama Gabilondo',
      'link' => 'internal:/foo/bar',
      'weight' => 0,
      'menu_name' => 'main',
      'parent' => 'menu_link_content:' . $parent_item->uuid(),
    ]);
    $this->assertEquals(SAVED_NEW, $menu_item->save());
    $this->assertEquals('entity:node/' . $node->id(), $menu_item->get('link')
      ->get(0)
      ->get('uri')
      ->getString());
    $this->assertEquals(SAVED_UPDATED, $menu_item->save());
    $this->assertNull($menu_item->delete());
  }

  public function testRedirects(){
    $node = Node::create(['type' => 'stanford_event', 'title' => 'Foo Bar']);
    $node->save();
    PathAlias::create([
      'path' => '/node/' . $node->id(),
      'alias' => '/foo/bar',
    ])->save();

    $redirect =Redirect::create([
      'redirect_redirect' => 'internal:/foo/bar',
      'redirect_source' => '/bar/foo',
    ]);
    $redirect->save();

    $this->assertEquals('entity:node/' . $node->id(), $redirect->get('redirect_redirect')->getString());
  }

}
