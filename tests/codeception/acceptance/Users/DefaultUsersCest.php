<?php

use Codeception\Attribute as CodeceptionAttribute;

/**
 * Tests that the default users are created.
 */
#[CodeceptionAttribute\Group('users')]
// phpcs:ignore DrupalPractice.General.ClassName.ClassPrefix
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
