<?php

use Codeception\Attribute as CodeceptionAttribute;

/**
 * Test the home page exists.
 */
#[CodeceptionAttribute\Group('home-page')]
class HomePageCest {

  /**
   * Validate the homepage loads.
   */
  public function testHomepage(AcceptanceTester $I) {
    $I->amOnPage('/');
    $I->canSee('Stanford');
    $I->seeCurrentUrlEquals('/user/login?destination=/home');
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
    $I->canSee('Stanford');
    $I->click('Edit', '.tabs');
    $I->cantSee('Published', '.field--name-status label');
  }

}
