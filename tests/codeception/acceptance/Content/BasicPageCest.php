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

  /**
   * There should be Page Metadata fields
   */
  public function testPageDescription(AcceptanceTester $I) {
    $faker = Factory::create();
    $title = $faker->text(20);
    $description = $faker->text(100);
    $I->logInWithRole('site_manager');
    $I->amOnPage('/node/add/stanford_page');
    $I->see('Page Metadata');
    $I->see('Page Image');
    $I->see('Basic Page Type');
    $I->fillField('Title', $title);
    $I->fillField('Page Description', $description);
    $I->selectOption('Basic Page Type', 'Research Project');
    $I->click('Save');
    $I->canSee($description);
    $I->seeInSource('<meta name="description" content="'.$description.'" />');
  }

  /**
   * Test that the vocabulary and default terms exist.
   */
  public function testBasicPageVocabularyTermsExists(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $I->amOnPage("/admin/structure/taxonomy/manage/basic_page_types/overview");
    $I->canSeeResponseCodeIs(200);
    $I->canSee('Research Project');
    $I->amOnPage("/admin/structure/taxonomy/manage/basic_page_types/add");
    $I->canSeeResponseCodeIs(200);
    $I->fillField('Name', 'Test Basic Page Term');
    $I->click('Save');
    $I->canSee('Created new term');
  }


}
