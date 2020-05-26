<?php

/**
 * Spotlight content type tests.
 */
class SpotlightContentCest {

  /**
   * Create a new piece of spotlight content.
   */
  public function testSpotlightContentCreation(\AcceptanceTester $I) {
    $I->createEntity(['type' => 'su_opportunity', 'title' => 'Foo Bar']);
    $I->logInWithRole('site_manager');
    $I->amOnPage('/node/add/su_spotlight');
    $I->canSee("Opportunity's Information", 'summary');
    $I->canSee("Opportunity's Open To?");
    $I->canSee("Opportunity's Service Theme");
    $I->canSee("Opportunity's Pathway");
    $I->canSee("Opportunity's Dimension");
    $I->fillField('Title', 'Foo Bar Spotlight');
    $I->fillField('Student Name', 'John Doe');
    $I->fillField('Major', 'Underwater Basket Weaving');
    $I->fillField('Story', 'Lorem Ipsum');
    $I->fillField('Related Opportunity', 'Foo Bar');
    $I->click('Save');
    $I->canSee("Foo Bar Spotlight");
    $I->canSee("John Doe");
    $I->canSee("Underwater Basket Weaving");
    $I->canSee("Lorem Ipsum");
    $I->amOnPage('/admin/content/');
    $I->click('Foo Bar');
    $I->canSee('John Doe');
    $I->canSee('Underwater Basket Weaving');
  }

}
