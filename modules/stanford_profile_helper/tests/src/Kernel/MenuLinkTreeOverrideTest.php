<?php

namespace Drupal\Tests\stanford_profile_helper\Kernel;

use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\KernelTests\KernelTestBase;
use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;
use Drupal\stanford_profile_helper\MenuLinkTreeOverride;
use Drupal\system\Entity\Menu;
use Drupal\user\RoleInterface;

/**
 * Class MenuLinkTreeOverrideTest.
 *
 * @group stanford_profile_helper
 * @coversDefaultClass \Drupal\stanford_profile_helper\MenuLinkTreeOverride
 */
class MenuLinkTreeOverrideTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'stanford_profile_helper',
    'config_pages',
    'menu_link_content',
    'link',
    'user',
    'node',
  ];

  protected function setUp() {
    parent::setUp();
    $this->installEntitySchema('user');
    $this->installEntitySchema('node');
    $this->installEntitySchema('menu_link_content');
    NodeType::create(['type' => 'page', 'name' => 'page'])->save();
    user_role_grant_permissions(RoleInterface::ANONYMOUS_ID, ['access content']);
  }

  /**
   * Service decorator should override main service.
   */
  public function testMenuTree() {
    $node = Node::create(['title' => 'Foo', 'type' => 'page']);
    $node->save();

    Menu::create([
      'id' => 'menu_test',
      'label' => 'Test menu',
      'description' => 'Description text',
    ])->save();

    $menu_link = MenuLinkContent::create([
      'title' => 'Primary level node',
      'menu_name' => 'menu_test',
      'bundle' => 'menu_link_content',
      'parent' => '',
      'link' => [['uri' => 'entity:node/' . $node->id()]],
    ]);
    $menu_link->save();

    $menu_tree = \Drupal::menuTree();
    $this->assertInstanceOf(MenuLinkTreeOverride::class, $menu_tree);
    $tree = $menu_tree->load('menu_test', new MenuTreeParameters());
    $build = $menu_tree->build($tree);

    $this->assertNotEmpty(array_keys($build['#items']));
    $this->assertTrue(in_array('stanford_profile_helper:menu_links', $build['#cache']['tags']));
    $this->assertFalse(in_array('config:system.menu.menu_test', $build['#cache']['tags']));
    $this->assertFalse(in_array('node:' . $node->id(), $build['#cache']['tags']));
    $this->assertGreaterThan(0, $menu_tree->maxDepth());
  }

}
