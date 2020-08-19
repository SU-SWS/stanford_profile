<?php

/**
 * Spotlight content type tests.
 */
class SpotlightContentCest {

  /**
   * Create a new piece of spotlight content.
   *
   * @group testthis
   */
  public function testSpotlightContentCreation(\AcceptanceTester $I) {
    $opportunity = $I->createEntity([
      'type' => 'su_opportunity',
      'title' => 'Foo Bar',
    ]);
    $I->logInWithRole('site_manager');
    $I->amOnPage('/node/add/su_spotlight');
    $I->canSee("Opportunity's Information", 'summary');
    $I->canSee("Opportunity's Open To?");
    $I->canSee("Opportunity's Service Theme");
    $I->canSee("Opportunity's Pathway");
    $I->canSee("Opportunity Program");
    $I->fillField('Title', 'Foo Bar Spotlight');
    $I->fillField('Author Name', 'John Doe');
    $I->fillField('Author Subtitle', '2020 Underwater Basket Weaving');
    $I->fillField('Quote', 'Basket Weaving is fun.');
    $I->fillField('Story', 'Lorem Ipsum');
    $I->fillField('Related Opportunity', 'Foo Bar');
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
