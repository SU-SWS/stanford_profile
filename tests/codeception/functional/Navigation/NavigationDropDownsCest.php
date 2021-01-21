<?php
use Faker\Factory;
/**
 * Test for the lockup settings
 */
class NavigationDropDownsCest {

  /**
   * Create some content and test the dropdown menu
   */
  public function testDropdownMenus(FunctionalTester $I) {

    $I->logInWithRole('Administrator');
    $I->amOnPage('/admin/appearance/settings/stanford_basic');
    $I->see('Enable dropdowns for navigation menu');
    $I->checkOption('Enable dropdowns for navigation menu');
    $I->click('Save configuration');

    $faker = Factory::create();
    $node_title = $faker->text(20);

    $I->amOnPage('/node/add/stanford_page');
    $I->wait(3);
    $I->fillField('Title', $node_title);
    $I->click('Menu settings');
    $I->checkOption('Provide a menu link');
    $I->fillField('Menu link title', "$node_title");
    // The label on the menu parent changes in D9 vs D8
    $I->selectOption('Parent link', '-- Resources');
    $I->wait(3);
    $I->click('Save');
    $I->canSeeLink("$node_title");

    $I->amOnPage('/');
    $I->resizeWindow(1000,800);
    $I->seeElement('button', ['class' => 'su-nav-toggle']);
  }

}
