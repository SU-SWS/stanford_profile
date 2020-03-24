<?php

/**
 * Spotlight content type tests.
 */
class SpotlightContentCest {

  /**
   * Create a new piece of spotlight content.
   */
  public function testSpotlightContentCreation(\AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $I->amOnPage('/node/add/cs_spotlight');
    $I->fillField('Title', 'Foo Bar Spotlight');
    $I->fillField('Student Name', 'John Doe');
    $I->fillField('Major', 'Underwater Basket Weaving');
    $I->fillField('Story', 'Lorem Ipsum');
    $I->click('Save');
    $I->canSee("Foo Bar Spotlight");
    $I->canSee("John Doe");
    $I->canSee("Underwater Basket Weaving");
    $I->canSee("Lorem Ipsum");
  }

}
