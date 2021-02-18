<?php

use Faker\Factory;

/**
 * Class BasicPageCest.
 *
 * @group content
 * @group basic_page
 */
class BasicPageCest {

  /**
   * Test placing a basic page in the menu with a child menu item.
   */
  public function testCreatingPage(AcceptanceTester $I) {
    $faker = Factory::create();
    $node_title = $faker->text(20);

    $I->logInWithRole('contributor');
    $I->amOnPage('/node/add/stanford_page');
    $I->fillField('Title', $node_title);
    $I->checkOption('Provide a menu link');
    $I->fillField('Menu link title', "$node_title Item");
    // The label on the menu parent changes in D9 vs D8
    $I->selectOption('Parent link', ' <Main navigation>');
    $I->click('Save');
    $I->canSeeLink("$node_title Item");

    $child_title = $faker->text('15');

    $I->amOnPage('/node/add/stanford_page');
    $I->fillField('Title', $child_title);
    $I->checkOption('Provide a menu link');
    $I->fillField('Menu link title', "$child_title Item");
    $I->selectOption('Parent link', "-- $node_title Item");
    $I->click('Change parent (update list of weights)');
    $I->click('Save');
    $I->canSeeLink("$child_title Item");
  }

  /**
   * Number of h1 tags should always be 1.
   */
  public function testH1Tags(AcceptanceTester $I) {
    $faker = Factory::create();

    $I->amOnPage('/' . $faker->text);
    $I->canSeeResponseCodeIs(404);
    $I->canSeeNumberOfElements('h1', 1);

    $I->amOnPage('/search/content?keys=stuff&search=');
    $I->canSeeResponseCodeIs(200);
    $I->canSeeNumberOfElements('h1', 1);
  }

  /**
   * The revision history tab should be functional.
   *
   * Regression test for D8CORE-1547.
   */
  public function testRevisionPage(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $node = $I->createEntity(['title' => 'Foo Bar', 'type' => 'stanford_page']);
    $I->amOnPage($node->toUrl()->toString());
    $I->click('Version History');
    $I->canSeeResponseCodeIs(200);
  }

}
