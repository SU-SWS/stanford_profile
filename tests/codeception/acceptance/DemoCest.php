<?php

class DemoCest {

  /**
   * Validate the homepage loads.
   */
  public function testHomepage(AcceptanceTester $I) {
    $I->amOnPage('/');
    $I->canSee('Stanford');
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/structure');
    $I->canSeeResponseCodeIs(200);
  }

}
