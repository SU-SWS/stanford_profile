<?php

/**
 * Test the home page exists.
 */
class HomePageCest {

  /**
   * Validate the homepage loads.
   */
  public function testHomepage(AcceptanceTester $I) {
    $I->amOnPage('/');
    $I->canSee('Stanford');
    $I->seeCurrentUrlEquals('/');
    $I->canSeeResponseCodeIs(200);
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/structure');
    $I->canSeeResponseCodeIs(200);
  }

  /**
   * Users can't unpublish the homepage.
   */
  public function testUnpublishingHomepage(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $I->amOnPage('/');
    $I->click('Edit', '.tabs');
    $I->cantSee('Published', 'label');
  }

}
