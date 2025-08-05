<?php

use Codeception\Attribute as CodeceptionAttribute;
use Faker\Factory;

/**
 * Test for the decoupled menu.
 */
#[CodeceptionAttribute\Group('navigation')]
#[CodeceptionAttribute\Group('decoupled-menu')]
class DecoupledMenuCest {

  /**
   * Faker service.
   *
   * @var \Faker\Generator
   */
  protected $faker;

  /**
   * Test constructor.
   */
  public function __construct() {
    $this->faker = Factory::create();
  }

  /**
   * Cleanup after test.
   */
  public function _after(FunctionalTester $I) {
    \Drupal::entityTypeManager()
      ->getStorage('config_pages')
      ->load('stanford_basic_site_settings')
      ->set('su_site_new_menu', NULL)
      ->save();
  }

  public function testDecoupledMenu(FunctionalTester $I) {
    $this->enableDecoupledMenu();
    $parent_1_title = $this->faker->uuid();
    $child_1_title = $this->faker->uuid();
    $child_2_title = $this->faker->uuid();
    $parent_2_title = $this->faker->uuid();

    $parent_1 = $this->createPageInMenu($I, $parent_1_title, 10);
    $child_1 = $this->createPageInMenu($I, $child_1_title, 20, "menu_link_field:node_field_menulink_{$parent_1->uuid()}_und");
    $child_2 = $this->createPageInMenu($I, $child_2_title, 10, "menu_link_field:node_field_menulink_{$parent_1->uuid()}_und");
    $parent_2 = $this->createPageInMenu($I, $parent_2_title, 20);

    $I->logInWithRole('site_manager');
    $I->amOnPage('/');
    $I->resizeWindow(1500, 600);
    $I->waitForElementVisible('nav.preact-main-menu');

    $I->canSeeLink($parent_1_title, $parent_1->toUrl()->toString());
    $I->canSeeLink($parent_2_title, $parent_2->toUrl()->toString());
    $I->cantSeeLink($child_1_title);
    $I->cantSeeLink($child_2_title);

    $menu_links = explode("\n", $I->grabTextFrom('nav.preact-main-menu'));
    $I->assertTrue(array_search($parent_1_title, $menu_links) < array_search($parent_2_title, $menu_links), 'Parent link 1 displays before parent link 2');

    $I->clickWithLeftButton("button[aria-labelledby='menu_link_field:node_field_menulink_{$parent_1->uuid()}_und']");

    $I->waitForText($child_1_title);
    $I->canSeeLink($child_1_title, $child_1->toUrl()->toString());
    $I->canSeeLink($child_2_title, $child_2->toUrl()->toString());

    $menu_links = explode("\n", $I->grabTextFrom('nav.preact-main-menu'));
    $I->assertTrue(array_search($child_2_title, $menu_links) < array_search($child_1_title, $menu_links), 'Child link 2 displays before child link 1');
    $I->assertTrue(array_search($parent_1_title, $menu_links) < array_search($child_1_title, $menu_links), 'Parent link 1 displays before child link 1');
    $I->assertTrue(array_search($child_1_title, $menu_links) < array_search($parent_2_title, $menu_links), 'Child link 1 displays before parent link 2');
  }

  protected function createPageInMenu(FunctionalTester $I, string $title, int $weight = 0, string $parent = '') {
    return $I->createEntity([
      'type' => 'stanford_page',
      'title' => $title,
      'field_menulink' => [
        'menu_name' => 'main',
        'title' => $title,
        'expanded' => 1,
        'weight' => $weight,
        'parent' => $parent,
      ],
    ]);
  }

  protected function enableDecoupledMenu() {
    $cp_storage = \Drupal::entityTypeManager()
      ->getStorage('config_pages');
    $config_page = $cp_storage->load('stanford_basic_site_settings');
    if (!$config_page) {
      $config_page = $cp_storage->create([
        'type' => 'stanford_basic_site_settings',
        'context' => 'a:0:{}',
      ]);
    }
    $config_page->set('su_site_new_menu', TRUE)->save();
  }

}
