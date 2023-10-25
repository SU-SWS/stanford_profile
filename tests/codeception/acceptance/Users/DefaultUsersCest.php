<?php

/**
 * Class DefaultUsersCest.
 *
 * @group users
 */
class DefaultUsersCest {

  /**
   * Default users should be created.
   */
  public function testDefaultUsers(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/users');
    $I->canSee('Alex');
    $I->canSee('Jamie');
    $I->canSee('Sam');
    $I->canSee('Morgan');
    $I->canSee('Kennedy');
  }

}
