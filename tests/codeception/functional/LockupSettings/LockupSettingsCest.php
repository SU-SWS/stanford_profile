<?php

/**
 * Test for the lockup settings
 * @group lockup_settings
 */
class LockupSettingsCest {

  /**
   * Test the lockup exists.
   */
  public function testLockupSettings(FunctionalTester $I) {
    $I->logInWithRole('site_manager');
    $I->amOnPage('/');
    $I->seeElement('.su-lockup');

  }

  /**
   * Test the lockup settings overrides.
   */
  public function testLockupSettingsA(FunctionalTester $I) {
    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/config/system/lockup-settings');
    $I->selectOption('#edit-su-lockup-options', 'a');
    $I->checkOption('#edit-su-use-theme-logo-value');
    $I->fillField('Line 1', 'Site title line');
    $I->fillField('Line 2', 'Secondary title line');
    $I->fillField('Line 3', 'Tertiary title line');
    $I->fillField('Line 4', 'Organization name');
    $I->fillField('Line 5', 'Last line full width option');
    $I->click('Save');
    $I->amOnPage('/');
    $I->waitForElement('.su-lockup', 5);
    $I->canSee("Site title line");
    $I->canSee("Last line full width option");
  }

  /**
   * Test the lockup settings overrides.
   */
  public function testLockupSettingsB(FunctionalTester $I) {
    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/config/system/lockup-settings');
    $I->selectOption("#edit-su-lockup-options", "b");
    $I->fillField('Line 1', 'Site title line');
    $I->fillField('Line 2', 'Secondary title line');
    $I->fillField('Line 3', 'Tertiary title line');
    $I->fillField('Line 4', 'Organization name');
    $I->fillField('Line 5', 'Last line full width option');
    $I->click('Save');
    $I->amOnPage('/');
    $I->canSee("Site title line");
    $I->canSee("Secondary title line");
  }

  /**
   * Test the lockup settings overrides.
   */
  public function testLockupSettingsD(FunctionalTester $I) {
    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/config/system/lockup-settings');
    $I->selectOption("#edit-su-lockup-options", "d");
    $I->fillField('Line 1', 'Site title line');
    $I->fillField('Line 2', 'Secondary title line');
    $I->fillField('Line 3', 'Tertiary title line');
    $I->fillField('Line 4', 'Organization name');
    $I->fillField('Line 5', 'Last line full width option');
    $I->click('Save');
    $I->amOnPage('/');
    $I->canSee("Site title line");
    $I->canSee("Tertiary title line");
  }

  /**
   * Test the lockup settings overrides.
   */
  public function testLockupSettingsE(FunctionalTester $I) {
    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/config/system/lockup-settings');
    $I->selectOption("#edit-su-lockup-options", "e");
    $I->fillField('Line 1', 'Site title line');
    $I->fillField('Line 2', 'Secondary title line');
    $I->fillField('Line 3', 'Tertiary title line');
    $I->fillField('Line 4', 'Organization name');
    $I->fillField('Line 5', 'Last line full width option');
    $I->click('Save');
    $I->amOnPage('/');
    $I->canSee("Site title line");
    $I->canSee("Secondary title line");
    $I->canSee("Tertiary title line");
  }

  /**
   * Test the lockup settings overrides.
   */
  public function testLockupSettingsH(FunctionalTester $I) {
    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/config/system/lockup-settings');
    $I->selectOption("#edit-su-lockup-options", "h");
    $I->fillField('Line 1', 'Site title line');
    $I->fillField('Line 2', 'Secondary title line');
    $I->fillField('Line 3', 'Tertiary title line');
    $I->fillField('Line 4', 'Organization name');
    $I->fillField('Line 5', 'Last line full width option');
    $I->click('Save');
    $I->amOnPage('/');
    $I->canSee("Site title line");
    $I->canSee("Organization name");
    $I->canSee("Tertiary title line");
  }

  /**
   * Test the lockup settings overrides.
   */
  public function testLockupSettingsI(FunctionalTester $I) {
    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/config/system/lockup-settings');
    $I->selectOption("#edit-su-lockup-options", "i");
    $I->fillField('Line 1', 'Site title line');
    $I->fillField('Line 2', 'Secondary title line');
    $I->fillField('Line 3', 'Tertiary title line');
    $I->fillField('Line 4', 'Organization name');
    $I->fillField('Line 5', 'Last line full width option');
    $I->click('Save');
    $I->amOnPage('/');
    $I->canSee("Site title line");
    $I->canSee("Organization name");
    $I->canSee("Tertiary title line");
  }

  /**
   * Test the lockup settings overrides.
   */
  public function testLockupSettingsO(FunctionalTester $I) {
    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/config/system/lockup-settings');
    $I->selectOption("#edit-su-lockup-options", "o");
    $I->fillField('Line 1', 'Site title line');
    $I->fillField('Line 2', 'Secondary title line');
    $I->fillField('Line 3', 'Tertiary title line');
    $I->fillField('Line 4', 'Organization name');
    $I->fillField('Line 5', 'Last line full width option');
    $I->click('Save');
    $I->amOnPage('/');
    $I->canSee("Organization name");
  }

  /**
   * Test the lockup settings overrides.
   */
  public function testLockupSettingsP(FunctionalTester $I) {
    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/config/system/lockup-settings');
    $I->selectOption("#edit-su-lockup-options", "p");
    $I->fillField('Line 1', 'Site title line');
    $I->fillField('Line 2', 'Secondary title line');
    $I->fillField('Line 3', 'Tertiary title line');
    $I->fillField('Line 4', 'Organization name');
    $I->fillField('Line 5', 'Last line full width option');
    $I->click('Save');
    $I->amOnPage('/');
    $I->canSee("Site title line");
    $I->canSee("Organization name");
  }

  /**
   * Test the lockup settings overrides.
   */
  public function testLockupSettingsR(FunctionalTester $I) {
    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/config/system/lockup-settings');
    $I->selectOption("#edit-su-lockup-options", "r");
    $I->fillField('Line 1', 'Site title line');
    $I->fillField('Line 2', 'Secondary title line');
    $I->fillField('Line 3', 'Tertiary title line');
    $I->fillField('Line 4', 'Organization name');
    $I->fillField('Line 5', 'Last line full width option');
    $I->click('Save');
    $I->amOnPage('/');
    $I->canSee("Last line full width option");
  }

  /**
   * Test the lockup settings overrides.
   */
  public function testLockupSettingsS(FunctionalTester $I) {
    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/config/system/lockup-settings');
    $I->selectOption("#edit-su-lockup-options", "s");
    $I->fillField('Line 1', 'Site title line');
    $I->fillField('Line 2', 'Secondary title line');
    $I->fillField('Line 3', 'Tertiary title line');
    $I->fillField('Line 4', 'Organization name');
    $I->fillField('Line 5', 'Last line full width option');
    $I->click('Save');
    $I->amOnPage('/');
    $I->canSee("Site title line");
    $I->canSee("Secondary title line");
    $I->canSee("Organization name");
  }

  /**
   * Test the lockup settings overrides.
   */
  public function testLockupSettingsT(FunctionalTester $I) {
    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/config/system/lockup-settings');
    $I->selectOption("#edit-su-lockup-options", "t");
    $I->fillField('Line 1', 'Site title line');
    $I->fillField('Line 2', 'Secondary title line');
    $I->fillField('Line 3', 'Tertiary title line');
    $I->fillField('Line 4', 'Organization name');
    $I->fillField('Line 5', 'Last line full width option');
    $I->click('Save');
    $I->amOnPage('/');
    $I->canSee("Site title line");
    $I->canSee("Secondary title line");
    $I->canSee("Tertiary title line");
    $I->canSee("Organization name");
  }

  /**
   * Test the logo image settings overrides.
   */
  public function testLogoImage(FunctionalTester $I) {
    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/config/system/lockup-settings');
    $I->uncheckOption('#edit-su-use-theme-logo-value');
    $I->fillField('Path to custom logo', 'https://placecorgi.com/1080/600');
    $I->click('Save');
    $I->runDrush('cache-clear router');
    $I->amOnPage('/');
    $I->canSeeElement('//img[@alt="site logo"]');
  }

  /**
   * Test access to lockup settings overrides.
   */
  public function testSiteManagerRole(FunctionalTester $I) {
    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/config/system');
    $I->canSeeLink('Lockup Settings');
  }

  /**
   * Test access to lockup settings overrides.
   */
  public function testContributorRole(FunctionalTester $I) {
    $I->logInWithRole('contributor');
    $I->amOnPage('/admin/config/system');
    $I->cantSeeLink('Lockup Settings');
  }

  /**
   * Test access to lockup settings overrides.
   */
  public function testSiteEditorRole(FunctionalTester $I) {
    $I->logInWithRole('site_editor');
    $I->amOnPage('/admin/config/system');
    $I->cantSeeLink('Lockup Settings');
  }

}
