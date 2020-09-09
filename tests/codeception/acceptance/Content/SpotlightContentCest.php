<?php

/**
 * Spotlight content type tests.
 */
class SpotlightContentCest {

  /**
   * Create a new piece of spotlight content.
   */
  public function testSpotlightContentCreation(\AcceptanceTester $I) {
    $I->createEntity([
      'name' => 'foo bar',
      'vid' => 'su_opportunity_dimension',
    ], 'taxonomy_term');
    $opportunity = $I->createEntity([
      'type' => 'su_opportunity',
      'title' => 'Foo Bar',
    ]);
    $I->logInWithRole('site_manager');
    $I->amOnPage('/node/add/su_spotlight');
    $I->canSee("Spotlight Information", 'summary');
    $I->canSee("Related Service Theme");
    $I->canSee("Related Pathway");
    $I->canSee("Related Program");
    $I->fillField('Title', 'Foo Bar Spotlight');
    $I->fillField('Author Name', 'John Doe');
    $I->fillField('Author Subtitle', '2020 Underwater Basket Weaving');
    $I->fillField('Quote', 'Basket Weaving is fun.');
    $I->fillField('Story', 'Lorem Ipsum');
    $I->fillField('su_spotlight_opportunity[0][target_id]', 'Foo Bar');
    $I->selectOption('Related Program', 'foo bar');
    $I->click('Save');
    $I->canSee("Foo Bar Spotlight", 'h1');
    $I->canSee("John Doe");
    $I->canSee("2020 Underwater Basket Weaving");
    $I->cantSee("Basket Weaving is fun.");
    $I->canSee('Lorem Ipsum');
    $I->amOnPage($opportunity->toUrl()->toString());
    $I->canSee('John Doe');
    $I->canSee('Basket Weaving is fun.');
  }

}
