<?php

/**
 * Test for the lockup settings
 */
class LockupSettingsCest {

  /**
   * Test the lockup exists.
   */
  public function testLockupSettings(AcceptanceTester $I) {
    $I->amOnPage('/');
    $I->seeElement('.su-lockup');
  }

  /**
   * Test the lockup settings overrides.
   */
  public function testLockupSettingsA(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/config/system/lockup-settings');
    $I->canSeeResponseCodeIs(200);
    $I->selectOption('#edit-su-lockup-options', 'a');
    $I->checkOption('#edit-su-use-theme-logo-value');
    $I->fillField('Line 1', 'Site title line');
    $I->fillField('Line 2', 'Secondary title line');
    $I->fillField('Line 3', 'Tertiary title line');
    $I->fillField('Line 4', 'Organization name');
    $I->fillField('Line 5', 'Last line full width option');
    $I->click('Save');
    $I->amOnPage('/');
    $I->canSee("Site title line");
    $I->canSee("Last line full width option");
  }

  /**
   * Test the lockup settings overrides.
   */
  public function testLockupSettingsB(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/config/system/lockup-settings');
    $I->canSeeResponseCodeIs(200);
    $I->selectOption("#edit-su-lockup-options", "b");
    $I->checkOption('#edit-su-use-theme-logo-value');
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
  public function testLockupSettingsD(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/config/system/lockup-settings');
    $I->canSeeResponseCodeIs(200);
    $I->selectOption("#edit-su-lockup-options", "d");
    $I->checkOption('#edit-su-use-theme-logo-value');
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
  public function testLockupSettingsE(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/config/system/lockup-settings');
    $I->canSeeResponseCodeIs(200);
    $I->selectOption("#edit-su-lockup-options", "e");
    $I->checkOption('#edit-su-use-theme-logo-value');
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
  public function testLockupSettingsH(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/config/system/lockup-settings');
    $I->canSeeResponseCodeIs(200);
    $I->selectOption("#edit-su-lockup-options", "h");
    $I->checkOption('#edit-su-use-theme-logo-value');
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
  public function testLockupSettingsI(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/config/system/lockup-settings');
    $I->canSeeResponseCodeIs(200);
    $I->selectOption("#edit-su-lockup-options", "i");
    $I->checkOption('#edit-su-use-theme-logo-value');
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
  public function testLockupSettingsO(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/config/system/lockup-settings');
    $I->canSeeResponseCodeIs(200);
    $I->selectOption("#edit-su-lockup-options", "o");
    $I->checkOption('#edit-su-use-theme-logo-value');
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
  public function testLockupSettingsP(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/config/system/lockup-settings');
    $I->canSeeResponseCodeIs(200);
    $I->selectOption("#edit-su-lockup-options", "p");
    $I->checkOption('#edit-su-use-theme-logo-value');
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
  public function testLockupSettingsR(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/config/system/lockup-settings');
    $I->canSeeResponseCodeIs(200);
    $I->selectOption("#edit-su-lockup-options", "r");
    $I->checkOption('#edit-su-use-theme-logo-value');
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
  public function testLockupSettingsS(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/config/system/lockup-settings');
    $I->canSeeResponseCodeIs(200);
    $I->selectOption("#edit-su-lockup-options", "s");
    $I->checkOption('#edit-su-use-theme-logo-value');
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
  public function testLockupSettingsT(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/config/system/lockup-settings');
    $I->canSeeResponseCodeIs(200);
    $I->selectOption("#edit-su-lockup-options", "t");
    $I->checkOption('#edit-su-use-theme-logo-value');
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
  public function testLogoWithLockup(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/config/system/lockup-settings');
    $I->canSeeResponseCodeIs(200);
    $I->selectOption('#edit-su-lockup-options', 'a');
    $I->fillField('Line 1', 'Site title line');
    $I->fillField('Line 2', 'Secondary title line');
    $I->fillField('Line 3', 'Tertiary title line');
    $I->fillField('Line 4', 'Organization name');
    $I->fillField('Line 5', 'Last line full width option');

    // Add custom logo.
    $I->uncheckOption('#edit-su-use-theme-logo-value');

    // In case there was an image already.
    try {
      $I->click("Remove");
    }
    catch(Exception $e) {
      // Do nothing and carry on.
    }

    try {
      // For CircleCI
      $I->attachFile('input[name="files[su_upload_logo_image_0]"]', '../acceptance/LockupSettings/logo.jpg');
    }
    catch(Exception $e) {
      // For Local.
      $uglyHack = "../../../../../../..";
      $I->attachFile('input[name="files[su_upload_logo_image_0]"]', $uglyHack . __DIR__ . '/logo.jpg');
    }

    $I->click('Upload');
    $I->fillField("input[name='su_upload_logo_image[0][alt]']", "Alternative Text");

    $I->click('Save');
    $I->runDrush('cr');
    $I->amOnPage('/');
    $I->assertNotEmpty($I->grabAttributeFrom('.su-masthead img', 'alt'));
    $I->canSee("Site title line");
    $I->seeElement(".su-lockup__custom-logo");
  }

  /**
   * Test for the logo without the lockup text.
   *
   * @depends testLogoWithLockup
   */
  public function testLogoWithOutLockup(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/config/system/lockup-settings');
    $I->canSeeResponseCodeIs(200);
    $I->selectOption('#edit-su-lockup-options', 'none');
    $I->click('Save');
    $I->runDrush('cache-clear router');
    $I->amOnPage('/');
    $I->assertNotEmpty($I->grabAttributeFrom('.su-masthead img', 'alt'));
    $I->seeElement(".su-lockup__custom-logo");
    $I->cantSeeElement(".su-lockup__cell2");
    $I->cantSee("Site title line");
  }

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
