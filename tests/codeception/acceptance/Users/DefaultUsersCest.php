<?php

use Codeception\Attribute as CodeceptionAttribute;

/**
 * Class DefaultUsersCest.
 */
#[CodeceptionAttribute\Group('users')]
class DefaultUsersCest {

  /**
   * Default users should be created.
   */
  public function testDefaultUsers(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/users');
    $I->canSee('Alex');
    $I->canSee('Morgan');
    $I->canSee('Kennedy');
  }

}
