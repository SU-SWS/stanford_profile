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
    $I->cantSee("Secondary title line");
    $I->cantSee("Tertiary title line");
    $I->cantSee("Organization name");
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
    $I->cantSee("Last line full width option");
    $I->cantSee("Tertiary title line");
    $I->cantSee("Organization name");
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
    $I->cantSee("Last line full width option");
    $I->cantSee("Secondary title line");
    $I->cantSee("Organization name");
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
    $I->cantSee("Last line full width option");
    $I->cantSee("Organization name");
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
    $I->cantSee("Last line full width option");
    $I->cantSee("Secondary title line");
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
    $I->cantSee("Last line full width option");
    $I->cantSee("Secondary title line");
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
    $I->cantSee("Last line full width option");
    $I->cantSee("Secondary title line");
    $I->cantSee("Site title line");
    $I->cantSee("Tertiary title line");
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
    $I->cantSee("Last line full width option");
    $I->cantSee("Secondary title line");
    $I->cantSee("Secondary title line");
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
    $I->cantSee("Site title line");
    $I->cantSee("Secondary title line");
    $I->cantSee("Tertiary title line");
    $I->cantSee("Organization name");
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
    $I->cantSee("Tertiary title line");
    $I->cantSee("Last line full width option");
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
    $I->cantSee("Last line full width option");
  }

  /**
   * Test the logo image settings overrides.
   */
  public function testLogoWithLockup(FunctionalTester $I) {
    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/config/system/lockup-settings');
    $I->selectOption('#edit-su-lockup-options', 'a');
    $I->checkOption('#edit-su-use-theme-logo-value');
    $I->fillField('Line 1', 'Site title line');
    $I->fillField('Line 2', 'Secondary title line');
    $I->fillField('Line 3', 'Tertiary title line');
    $I->fillField('Line 4', 'Organization name');
    $I->fillField('Line 5', 'Last line full width option');

    // Add custom logo.
    $I->uncheckOption('#edit-su-use-theme-logo-value');
    $I->attachFile('input[name="files[su_upload_logo_image_0]"]', 'logo.jpg');
    $I->waitForElement("input[name='su_upload_logo_image[0][alt]']");
    $I->fillField("input[name='su_upload_logo_image[0][alt]']", "Alternative Text");

    $I->click('Save');
    $I->runDrush('cache-clear router');
    $I->amOnPage('/');
    $I->assertNotEmpty($I->grabAttributeFrom('.su-masthead img', 'alt'));
    $I->canSee("Site title line");
  }

  /**
   * Test for the logo without the lockup text.
   */
  public function testLogoWithOutLockup(FunctionalTester $I) {
    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/config/system/lockup-settings');
    $I->selectOption('#edit-su-lockup-options', 'a');
    $I->checkOption('#edit-su-use-theme-logo-value');
    $I->fillField('Line 1', 'Site title line');
    $I->fillField('Line 2', 'Secondary title line');
    $I->fillField('Line 3', 'Tertiary title line');
    $I->fillField('Line 4', 'Organization name');
    $I->fillField('Line 5', 'Last line full width option');

    // Add custom logo.
    $I->uncheckOption('#edit-su-use-theme-logo-value');
    $I->attachFile('input[name="files[su_upload_logo_image_0]"]', 'logo.jpg');
    $I->waitForElement("input[name='su_upload_logo_image[0][alt]']");
    $I->fillField("input[name='su_upload_logo_image[0][alt]']", "Alternative Text");

    $I->click('Save');
    $I->runDrush('cache-clear router');
    $I->amOnPage('/');
    $I->assertNotEmpty($I->grabAttributeFrom('.su-masthead img', 'alt'));
    $I->canSee("Site title line");
  }

  /**
   * Test access to lockup settings overrides.
   */
  public function testSiteManagerRole(FunctionalTester $I) {
    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/config/system/lockup-settings');
    $I->canSee('Lockup Options');
    $I->canSeeResponseCodeIs(200);
  }

  /**
   * Test access to lockup settings overrides.
   */
  public function testContributorRole(FunctionalTester $I) {
    $I->logInWithRole('contributor');
    $I->amOnPage('/admin/config/system/lockup-settings');
    $I->canSeeResponseCodeIs(403);
  }

  /**
   * Test access to lockup settings overrides.
   */
  public function testSiteEditorRole(FunctionalTester $I) {
    $I->logInWithRole('site_editor');
    $I->amOnPage('/admin/config/system/lockup-settings');
    $I->canSeeResponseCodeIs(403);
  }

}
