<?php

namespace Drupal\Tests\stanford_profile_helper\Kernel\EventSubscriber;

use Drupal\config_pages\Entity\ConfigPages;
use Drupal\config_pages\Entity\ConfigPagesType;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\KernelTests\KernelTestBase;
use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;
use Drupal\node\NodeInterface;
use Drupal\path_alias\Entity\PathAlias;
use Drupal\redirect\Entity\Redirect;
use Drupal\stanford_profile_helper\StanfordDefaultContentInterface;
use Drupal\user\Entity\Role;

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
    'text',
    'field',
    'config_pages',
    'link',
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
    $this->installEntitySchema('field_storage_config');
    $this->installEntitySchema('config_pages');
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
    $role = Role::create(['id' => 'foo', 'label' => 'Foo']);
    $role->save();

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

  /**
   * Test redirect entities.
   */
  public function testRedirects() {
    $node = Node::create(['type' => 'stanford_event', 'title' => 'Foo Bar']);
    $node->save();
    PathAlias::create([
      'path' => '/node/' . $node->id(),
      'alias' => '/foo/bar',
    ])->save();

    $redirect = Redirect::create([
      'redirect_redirect' => 'internal:/foo/bar',
      'redirect_source' => '/bar/foo',
    ]);
    $redirect->save();

    $this->assertEquals('entity:node/' . $node->id(), $redirect->get('redirect_redirect')
      ->getString());
  }

  public function testFields() {
    $field_storage = FieldStorageConfig::create([
      'field_name' => 'field_foo',
      'entity_type' => 'node',
      'type' => 'text',
    ]);
    $field_storage->setThirdPartySetting('field_permissions', 'permission_type', 'public');
    $field_storage->save();

    $this->assertNull($field_storage->getThirdPartySetting('field_permissions', 'permission_type'));
  }

  public function testConfigPages() {
    ConfigPagesType::create([
      'id' => 'foo',
      'context' => [],
      'menu' => [],
    ])->save();
    $field_storage = FieldStorageConfig::create([
      'field_name' => 'su_site_url',
      'entity_type' => 'config_pages',
      'type' => 'link',
    ]);
    $field_storage->save();
    FieldConfig::create([
      'field_storage' => $field_storage,
      'bundle' => 'foo',
    ])->save();

    ConfigPages::create([
      'type' => 'foo',
      'su_site_url' => ['uri' => 'https://foo.bar'],
      'context' => 'a:0:{}',
    ])->save();

    $this->assertEquals('https://foo.bar', \Drupal::state()
      ->get('xmlsitemap_base_url'));
  }

}
