<?php


/**
 * Test the restrictions on authenticated users.
 */
class AuthenticatedPermissionsCest {

    /**
     * Make sure we can't see admin pages.
     */
    public function testAuthenticatedUserRestrictions(AcceptanceTester $I) {
      $I->logInWithRole('authenticated');
      $I->amOnPage('/');
      $I->canSee('Stanford');
      $I->seeCurrentUrlEquals('/');
      $I->canSeeResponseCodeIs(200);

      $I->amOnPage('/admin');
      $I->canSeeResponseCodeIs(403);
      $I->amOnPage('/admin/content');
      $I->canSeeResponseCodeIs(403);
      $I->amOnPage('/admin/structure');
      $I->canSeeResponseCodeIs(403);
      $I->amOnPage('/admin/appearance');
      $I->canSeeResponseCodeIs(403);
      $I->amOnPage('/admin/modules');
      $I->canSeeResponseCodeIs(403);
      $I->amOnPage('/admin/config');
      $I->canSeeResponseCodeIs(403);
      $I->amOnPage('/admin/people');
      $I->canSeeResponseCodeIs(403);
      $I->amOnPage('/admin/reports');
      $I->canSeeResponseCodeIs(403);

    }

  }
