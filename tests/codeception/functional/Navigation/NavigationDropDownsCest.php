<?php

use Faker\Factory;

/**
 * Test for the lockup settings
 */
class NavigationDropDownsCest {

  /**
   * Create some content and test the dropdown menu.
   */
  public function testDropdownMenus(FunctionalTester $I) {
    $I->logInWithRole('Administrator');
    $I->amOnPage('/');
    $I->cantSeeElement('button', ['class' => 'su-nav-toggle']);
    $I->amOnPage('/admin/config/system/basic-site-settings');
    $I->checkOption('Use drop down menus');
    $I->click('Save');

    $node_title = Factory::create()->text(20);

    $I->amOnPage('/node/add/stanford_page');
    $I->fillField('Title', $node_title);
    $I->click('Menu settings');
    $I->checkOption('Provide a menu link');
    $I->fillField('Menu link title', $node_title);
    // The label on the menu parent changes in D9 vs D8
    $I->selectOption('Parent link', '-- Resources');
    $I->waitForText('Change the weight of the links within the Resources menu');
    $I->click('Save');
    $I->canSeeLink($node_title);

    $I->amOnPage('/');
    $I->resizeWindow(1000, 800);
    $I->seeElement('button', ['class' => 'su-nav-toggle']);
  }

}
