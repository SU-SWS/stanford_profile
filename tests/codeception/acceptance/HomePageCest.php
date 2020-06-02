<?php

class HomePageCest {

  /**
   * Validate the homepage loads.
   */
  public function testHomepage(AcceptanceTester $I) {
    $I->amOnPage('/');
    $I->canSee('Stanford');
    $I->seeCurrentUrlEquals('/');
    $I->canSee('Welcome to your site!', 'h2');
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/structure');
    $I->canSeeResponseCodeIs(200);
  }

}
