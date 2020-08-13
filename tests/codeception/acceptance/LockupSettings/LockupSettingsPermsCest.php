<?php

/**
 * Test for the lockup settings permissions.
 */
class LockupSettingsPermsCest {

  /**
   * Test access to lockup settings overrides.
   */
  public function testSiteManagerRole(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/config/system/lockup-settings');
    $I->canSee('Edit config page Lockup Settings');
    $I->canSeeResponseCodeIs(200);
  }

  /**
   * Test access to lockup settings overrides.
   */
  public function testContributorRole(AcceptanceTester $I) {
    $I->logInWithRole('contributor');
    $I->amOnPage('/admin/config/system/lockup-settings');
    $I->canSeeResponseCodeIs(403);
  }

  /**
   * Test access to lockup settings overrides.
   */
  public function testSiteEditorRole(AcceptanceTester $I) {
    $I->logInWithRole('site_editor');
    $I->amOnPage('/admin/config/system/lockup-settings');
    $I->canSeeResponseCodeIs(403);
  }

}
