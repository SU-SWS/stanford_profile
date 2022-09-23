<?php

namespace Drupal\Tests\stanford_policy\Kernel\EventSubscriber;

use Drupal\config_pages\ConfigPagesLoaderServiceInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;
use Drupal\user\Entity\Role;
use Drupal\user\RoleInterface;

/**
 * @coversDefaultClass \Drupal\stanford_policy\EventSubscriber\StanfordPolicySubscriber
 */
class StanfordPolicySubscriberTest extends KernelTestBase {

  protected static $modules = [
    'node',
    'user',
    'system',
    'field',
    'text',
    'book',
    'stanford_policy',
    'stanford_fields',
    'config_pages',
    'hook_event_dispatcher',
    'core_event_dispatcher',
  ];

  protected $configPageValues = [];

  /**
   * @var \Drupal\node\NodeInterface
   */
  protected $book;

  /**
   * @var \Drupal\node\NodeInterface
   */
  protected $node;

  /**
   * @var \Drupal\node\NodeInterface
   */
  protected $childNode;

  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('node');
    $this->installEntitySchema('user');
    $this->installSchema('book', 'book');

    $config_pages_loader = $this->createMock(ConfigPagesLoaderServiceInterface::class);
    $config_pages_loader->method('getValue')
      ->will($this->returnCallback([$this, 'getConfigPageValue']));

    \Drupal::getContainer()->set('config_pages.loader', $config_pages_loader);

    Role::create(['id' => AccountInterface::ANONYMOUS_ROLE])->save();
    user_role_grant_permissions(RoleInterface::ANONYMOUS_ID, ['access content']);
    NodeType::create(['type' => 'stanford_policy'])->save();

    \Drupal::configFactory()->getEditable('book.settings')
      ->set('allowed_types', ['stanford_policy'])
      ->set('child_type', 'stanford_policy')
      ->save();

    $fields = [
      'su_policy_title' => 'string',
      'su_policy_chapter' => 'string',
      'su_policy_subchapter' => 'string',
      'su_policy_policy_num' => 'string',
      'su_policy_auto_prefix' => 'boolean',
    ];
    foreach ($fields as $field_name => $field_type) {
      FieldStorageConfig::create([
        'entity_type' => 'node',
        'field_name' => $field_name,
        'type' => $field_type,
        'cardinality' => 1,
      ])->save();
      FieldConfig::create([
        'field_name' => $field_name,
        'entity_type' => 'node',
        'bundle' => 'stanford_policy',
      ])->save();
    }

    $this->book = Node::create([
      'type' => 'stanford_policy',
      'title' => 'book foo_bar',
      'su_policy_title' => 'book FOO_BAR',
      'su_policy_auto_prefix' => 1,
    ]);
    $this->book->book = [
      'nid' => NULL,
      'bid' => 'new',
      'pid' => -1,
      'parent_depth_limit' => '9',
    ];
    $this->book->save();

    $this->node = Node::create([
      'type' => 'stanford_policy',
      'title' => 'chapter bar_foo',
      'su_policy_title' => 'chapter BAR_FOO',
      'su_policy_auto_prefix' => 1,
    ]);
    $this->node->book = [
      'nid' => NULL,
      'bid' => $this->book->id(),
      'pid' => $this->book->id(),
      'parent_depth_limit' => 9,
      'has_children' => 0,
      'weight' => 0,
    ];
    $this->node->save();

    $this->childNode = Node::create([
      'type' => 'stanford_policy',
      'title' => 'article foo_bar_baz',
      'su_policy_title' => 'article FOO_BAR_BAZ',
      'su_policy_auto_prefix' => 1,
    ]);
    $this->childNode->book = [
      'nid' => NULL,
      'bid' => $this->book->id(),
      'pid' => $this->node->id(),
      'parent_depth_limit' => 9,
      'has_children' => 0,
      'weight' => 0,
    ];
    $this->childNode->save();
  }

  public function testNumberPrefix() {
    /** @var \Drupal\book\BookManagerInterface $book_manager */
    $book_manager = \Drupal::service('book.manager');
    $book_tree_data = $book_manager->bookSubtreeData($book_manager->loadBookLink($this->book->id()));
    $this->assertNotEmpty(reset($book_tree_data)['below']);

    $book_tree_data = $book_manager->bookSubtreeData($book_manager->loadBookLink($this->node->id()));
    $this->assertNotEmpty(reset($book_tree_data)['below']);

    $reloaded_node = Node::load($this->node->id());
    $this->assertStringStartsWith('1. ', $reloaded_node->label());

    $child_node = Node::load($this->childNode->id());
    $this->assertStringStartsWith('1.1 ', $child_node->label());
  }

//  public function testUpperCaseAlpha() {
//    $node_id = $this->node->id();
//    $this->configPageValues['su_policy_prefix_first'] = 'alpha_uppercase';
//    $this->configPageValues['su_policy_prefix_sec'] = 'roman_numeral_lowercase';
//    $reloaded_node = Node::load($node_id);
//    $this->assertStringStartsWith('A. ', $reloaded_node->label());
//
//    $reloaded_node = Node::load($this->childNode->id());
//    $this->assertStringStartsWith('A.i ', $reloaded_node->label());
//  }
//
//  public function testLowerCaseAlpha() {
//    $node_id = $this->node->id();
//    $this->configPageValues['su_policy_prefix_first'] = 'alpha_lowercase';
//    $this->configPageValues['su_policy_prefix_sec'] = 'roman_numeral_uppercase';
//    $reloaded_node = Node::load($node_id);
//    $this->assertStringStartsWith('a. ', $reloaded_node->label());
//
//    $reloaded_node = Node::load($this->childNode->id());
//    $this->assertStringStartsWith('a.I ', $reloaded_node->label());
//  }
//
//  public function testUpperCaseRomanNumeral() {
//    $node_id = $this->node->id();
//    $this->configPageValues['su_policy_prefix_first'] = 'roman_numeral_uppercase';
//    $this->configPageValues['su_policy_prefix_sec'] = 'alpha_uppercase';
//    $reloaded_node = Node::load($node_id);
//    $this->assertStringStartsWith('I. ', $reloaded_node->label());
//
//    $reloaded_node = Node::load($this->childNode->id());
//    $this->assertStringStartsWith('I.A ', $reloaded_node->label());
//  }
//
//  public function testLowerCaseRomanNumeral() {
//    $node_id = $this->node->id();
//    $this->configPageValues['su_policy_prefix_first'] = 'roman_numeral_lowercase';
//    $this->configPageValues['su_policy_prefix_sec'] = 'alpha_lowercase';
//    $reloaded_node = Node::load($node_id);
//    $this->assertStringStartsWith('i. ', $reloaded_node->label());
//
//    $reloaded_node = Node::load($this->childNode->id());
//    $this->assertStringStartsWith('i.a', $reloaded_node->label());
//  }

  public function getConfigPageValue($page_type, $field_name, $delta = [], $column = NULL) {
    return $this->configPageValues[$field_name] ?? NULL;
  }

}
