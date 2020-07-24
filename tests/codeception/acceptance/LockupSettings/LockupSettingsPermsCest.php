<?php

/**
 * Test for the lockup settings
 */
class LockupSettingsCest {

  /**
   * Test access to lockup settings overrides.
   */
  public function testSiteManagerRole(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/config/system/lockup-settings');
    $I->canSee('Lockup Options');
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
