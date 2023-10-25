<?php

use Faker\Factory;

class MenuBlockCest {

  /**
   * Faker generator.
   *
   * @var \Faker\Generator $faker
   */
  protected $faker;

  /**
   * MenuBlockCest constructor.
   */
  public function __construct() {
    $this->faker = Factory::create();
  }

  /**
   * Test the menu block.
   *
   * @group menu_block
   */
  public function testMenuBlockVisibility(AcceptanceTester $I) {
    $parent = $I->createEntity([
      'type' => 'stanford_page',
      'title' => $this->faker->words(3, TRUE),
    ]);
    $parent_menu = $I->createEntity([
      'title' => $parent->label(),
      'link' => ['uri' => 'entity:node/' . $parent->id()],
      'menu_name' => 'main',
    ], 'menu_link_content');

    $I->amOnPage($parent->toUrl()->toString());
    $I->canSee($parent->label(), 'h1');
    $I->cantSeeElement('.left-region');
    $I->canSeeLink($parent->label());

    $child = $I->createEntity([
      'type' => 'stanford_page',
      'title' => $this->faker->words(3, TRUE),
    ]);
    $child_menu = $I->createEntity([
      'title' => $child->label(),
      'link' => ['uri' => 'entity:node/' . $child->id()],
      'menu_name' => 'main',
      'parent' => 'menu_link_content:' . $parent_menu->uuid(),
    ], 'menu_link_content');

    $I->amOnPage($child->toUrl()->toString());
    $I->canSee($child->label(), 'h1');
    $I->canSeeElement('.left-region');
    $I->canSee($child->label(), '.left-region nav');

    $grandchild = $I->createEntity([
      'type' => 'stanford_page',
      'title' => $this->faker->words(3, TRUE),
    ]);
    $I->createEntity([
      'title' => $grandchild->label(),
      'link' => ['uri' => 'entity:node/' . $grandchild->id()],
      'menu_name' => 'main',
      'parent' => 'menu_link_content:' . $child_menu->uuid(),
    ], 'menu_link_content');

    $I->amOnPage($grandchild->toUrl()->toString());
    $I->canSee($grandchild->label(), 'h1');
    $I->canSeeElement('.left-region');
    $I->canSee($grandchild->label(), '.left-region nav li li');
  }

}
