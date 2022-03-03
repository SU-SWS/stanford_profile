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
   *
   * @group pathauto
   */
  public function testCreatingPage(AcceptanceTester $I) {
    $faker = Factory::create();
    $node_title = $faker->text(20);

    $I->logInWithRole('site_manager');
    $I->amOnPage('/node/add/stanford_page');
    $I->fillField('Title', $node_title);
    $I->checkOption('Provide a menu link');
    $I->fillField('Menu link title', "$node_title Item");
    // The label on the menu parent changes in D9 vs D8
    $I->selectOption('Parent link', ' <Main navigation>');
    $I->uncheckOption('Generate automatic URL alias');
    $I->fillField('URL alias', '/foo-bar');
    $I->click('Save');
    $I->canSeeLink("$node_title Item");
    $I->assertStringContainsString('/foo-bar', $I->grabFromCurrentUrl());

    $child_title = $faker->text('15');

    $I->amOnPage('/node/add/stanford_page');
    $I->fillField('Title', $child_title);
    $I->checkOption('Provide a menu link');
    $I->fillField('Menu link title', "$child_title Item");
    $I->selectOption('Parent link', "-- $node_title Item");
    $I->click('Change parent (update list of weights)');
    $I->click('Save');
    $I->canSeeLink("$child_title Item");
    $I->assertStringContainsString('/foo-bar', $I->grabFromCurrentUrl());
  }

  /**
   * Number of h1 tags should always be 1.
   */
  public function testH1Tags(AcceptanceTester $I) {
    $faker = Factory::create();

    $I->amOnPage('/' . $faker->text);
    $I->canSeeResponseCodeIs(404);
    $I->canSeeNumberOfElements('h1', 1);

    $I->amOnPage('/search?keys=stuff&search=');
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
    $I->selectOption('Basic Page Type (experimental)', 'Research Project');
    $I->click('Save');
    $I->seeInSource('<meta name="description" content="' . $description . '" />');
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

  /**
   * A site manager should be able to place a page under an unpublished page.
   */
  public function testUnpublishedMenuItems(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $I->amOnPage('/node/add/stanford_page');
    $I->fillField('Title', 'Unpublished Parent');
    $I->checkOption('Provide a menu link');
    $I->fillField('Menu link title', 'Unpublished Parent');
    $I->uncheckOption('Published');
    $I->click('Save');
    $I->canSee('Unpublished Parent', 'h1');

    $I->amOnPage('/node/add/stanford_page');
    $I->fillField('Title', 'Child Page');
    $I->checkOption('Provide a menu link');
    $I->fillField('Menu link title', 'Child Page');
    $I->selectOption('Parent link', '-- Unpublished Parent');
    $I->click('Change parent (update list of weights)');
    $I->uncheckOption('Published');
    $I->click('Save');
    $I->canSee('Child Page', 'h1');

    $I->click('Edit', '.tabs__tab');
    $I->click('Save');
    $I->assertEquals('/unpublished-parent/child-page', $I->grabFromCurrentUrl());
  }

  /**
   * Clone a basic page.
   */
  public function testClone(AcceptanceTester $I) {
    $I->logInWithRole('contributor');
    $I->amOnPage('/node/add/stanford_page');
    $I->fillField('Title', 'Original Node');
    $I->click('Save');
    $I->amOnPage('/admin/content');
    $I->canSee('Original Node');
    $I->checkOption('[name="views_bulk_operations_bulk_form[0]"]');
    $I->selectOption('Action', 'Clone selected content');
    $I->click('Apply to selected items');
    $I->selectOption('Clone how many times', 2);
    $I->click('Apply');
    $links = $I->grabMultiple('a:contains("Original Node")');
    $I->assertCount(3, $links);
  }

  /**
   * Test the basic page scheduled publishing.
   *
   * @group scheduler
   */
  public function testScheduler(AcceptanceTester $I) {
    $time = \Drupal::time();

    /** @var \Drupal\system\TimeZoneResolver $timezone_resolver */
    $timezone_resolver = \Drupal::service('system.timezone_resolver');
    $timezone_resolver->setDefaultTimeZone();

    $I->logInWithRole('site_manager');
    $node = $I->createEntity(['title' => 'Foo Bar', 'type' => 'stanford_page']);
    $I->amOnPage($node->toUrl('edit-form')->toString());
    $I->fillField('publish_on[0][value][date]', date('Y-m-d'));
    $I->fillField('publish_on[0][value][time]', date('H:i:s', $time->getCurrentTime() + 10));
    $I->click('Save');
    $I->canSee('This page is currently unpublished');
    echo 'sleep 15 seconds' . PHP_EOL;
    sleep(15);
    $I->runDrush('sch-cron');
    $I->amOnPage($node->toUrl()->toString());
    $I->cantSee('This page is currently unpublished');
    $I->amOnPage($node->toUrl('edit-form')->toString());
    $I->canSeeCheckboxIsChecked('Published');
  }

}
