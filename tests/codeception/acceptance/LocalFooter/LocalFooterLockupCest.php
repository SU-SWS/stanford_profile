<?php

/**
 * Test for the local lockup settings
 */
class LocalFooterLockupCest {

  /**
   * The path to the data dir where codeception wants the logo file.
   *
   * @var string
   */
  protected $DATA_DIR;

  /**
   * The logo file name.
   *
   * @var string
   */
  const LOGO_FILENAME = "logo.jpg";

  /**
   * Setup work before running tests.
   *
   * @param AcceptanceTester $I
   *  The working class.
   */
  function _before(AcceptanceTester $I) {
    $this->DATA_DIR = rtrim(codecept_data_dir(), '/\\');
    // Copy our assets into place first.
    copy(__DIR__ . DIRECTORY_SEPARATOR . self::LOGO_FILENAME, $this->DATA_DIR . DIRECTORY_SEPARATOR . self::LOGO_FILENAME);
  }

  /**
   * Always cleanup the config after testing.
   *
   * @param \AcceptanceTester $I
   *   Tester.
   */
  public function _after(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/config/system/local-footer');
    $I->uncheckOption('#edit-su-local-foot-use-loc-value');
    $I->uncheckOption('#edit-su-local-foot-use-logo-value');
    $I->selectOption("#edit-su-local-foot-loc-op", "a");
    // In case there was an image already.
    try {
      $I->click("Remove");
    }
    catch(Exception $e) {
      // Do nothing and carry on.
    }
    $I->checkOption('#edit-su-local-foot-use-logo-value');
    $I->checkOption('#edit-su-local-foot-use-loc-value');
    $I->click('Save');

    // Clean up our assets.
    unlink($this->DATA_DIR . DIRECTORY_SEPARATOR . self::LOGO_FILENAME);
  }

  /**
   * Test the lockup exists.
   */
  public function testLockupSettings(AcceptanceTester $I) {
    $I->amOnPage('/');
    $I->seeElement('.su-local-footer .su-lockup');
  }

  /**
   * Test the lockup settings overrides.
   */
  public function testLockupSettingsA(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/config/system/local-footer');
    $I->canSeeResponseCodeIs(200);
    $I->uncheckOption('#edit-su-local-foot-use-loc-value');
    $I->selectOption('#edit-su-local-foot-loc-op', 'a');
    $I->checkOption('#edit-su-local-foot-use-logo-value');
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
    $I->amOnPage('/admin/config/system/local-footer');
    $I->canSeeResponseCodeIs(200);
    $I->uncheckOption('#edit-su-local-foot-use-loc-value');
    $I->selectOption('#edit-su-local-foot-loc-op', "b");
    $I->checkOption('#edit-su-local-foot-use-logo-value');
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
    $I->amOnPage('/admin/config/system/local-footer');
    $I->canSeeResponseCodeIs(200);
    $I->uncheckOption('#edit-su-local-foot-use-loc-value');
    $I->selectOption('#edit-su-local-foot-loc-op', "d");
    $I->checkOption('#edit-su-local-foot-use-logo-value');
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
    $I->amOnPage('/admin/config/system/local-footer');
    $I->canSeeResponseCodeIs(200);
    $I->uncheckOption('#edit-su-local-foot-use-loc-value');
    $I->selectOption('#edit-su-local-foot-loc-op', "e");
    $I->checkOption('#edit-su-local-foot-use-logo-value');
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
    $I->amOnPage('/admin/config/system/local-footer');
    $I->canSeeResponseCodeIs(200);
    $I->uncheckOption('#edit-su-local-foot-use-loc-value');
    $I->selectOption('#edit-su-local-foot-loc-op', "h");
    $I->checkOption('#edit-su-local-foot-use-logo-value');
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
    $I->amOnPage('/admin/config/system/local-footer');
    $I->canSeeResponseCodeIs(200);
    $I->uncheckOption('#edit-su-local-foot-use-loc-value');
    $I->selectOption('#edit-su-local-foot-loc-op', "i");
    $I->checkOption('#edit-su-local-foot-use-logo-value');
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
  public function testLockupSettingsM(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/config/system/local-footer');
    $I->canSeeResponseCodeIs(200);
    $I->uncheckOption('#edit-su-local-foot-use-loc-value');
    $I->selectOption('#edit-su-local-foot-loc-op', "m");
    $I->checkOption('#edit-su-local-foot-use-logo-value');
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
  public function testLockupSettingsO(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/config/system/local-footer');
    $I->canSeeResponseCodeIs(200);
    $I->uncheckOption('#edit-su-local-foot-use-loc-value');
    $I->selectOption('#edit-su-local-foot-loc-op', "o");
    $I->checkOption('#edit-su-local-foot-use-logo-value');
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
    $I->amOnPage('/admin/config/system/local-footer');
    $I->canSeeResponseCodeIs(200);
    $I->uncheckOption('#edit-su-local-foot-use-loc-value');
    $I->selectOption('#edit-su-local-foot-loc-op', "p");
    $I->checkOption('#edit-su-local-foot-use-logo-value');
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
    $I->amOnPage('/admin/config/system/local-footer');
    $I->canSeeResponseCodeIs(200);
    $I->uncheckOption('#edit-su-local-foot-use-loc-value');
    $I->selectOption('#edit-su-local-foot-loc-op', "r");
    $I->checkOption('#edit-su-local-foot-use-logo-value');
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
    $I->amOnPage('/admin/config/system/local-footer');
    $I->canSeeResponseCodeIs(200);
    $I->uncheckOption('#edit-su-local-foot-use-loc-value');
    $I->selectOption('#edit-su-local-foot-loc-op', "s");
    $I->checkOption('#edit-su-local-foot-use-logo-value');
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
    $I->amOnPage('/admin/config/system/local-footer');
    $I->canSeeResponseCodeIs(200);
    $I->uncheckOption('#edit-su-local-foot-use-loc-value');
    $I->selectOption('#edit-su-local-foot-loc-op', "t");
    $I->checkOption('#edit-su-local-foot-use-logo-value');
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
    $I->amOnPage('/admin/config/system/local-footer');
    $I->canSeeResponseCodeIs(200);
    $I->uncheckOption('#edit-su-local-foot-use-loc-value');
    $I->selectOption('#edit-su-local-foot-loc-op', 'a');
    $I->fillField('Line 1', 'Site title line');
    $I->fillField('Line 2', 'Secondary title line');
    $I->fillField('Line 3', 'Tertiary title line');
    $I->fillField('Line 4', 'Organization name');
    $I->fillField('Line 5', 'Last line full width option');

    // Add custom logo.
    $I->uncheckOption('#edit-su-local-foot-use-logo-value');

    // In case there was an image already.
    try {
      $I->click("Remove");
    }
    catch(Exception $e) {
      // Do nothing and carry on.
    }

    $I->attachFile('input[name="files[su_local_foot_loc_img_0]"]', self::LOGO_FILENAME);
    $I->click('Upload');

    $I->click('Save');
    $I->runDrush('cr');
    $I->amOnPage('/');
    $I->seeElement(".su-lockup__custom-logo");
    $I->assertNotEmpty($I->grabAttributeFrom('.su-lockup__custom-logo', 'alt'));
    $I->canSee("Site title line");
  }

  /**
   * Test for the logo without the lockup text.
   */
  public function testLogoWithOutLockup(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/config/system/local-footer');
    $I->canSeeResponseCodeIs(200);
    $I->uncheckOption('#edit-su-local-foot-use-loc-value');
    $I->selectOption('#edit-su-local-foot-loc-op', 'none');
    $I->fillField('Line 1', 'Site title line');
    $I->fillField('Line 2', 'Secondary title line');
    $I->fillField('Line 3', 'Tertiary title line');
    $I->fillField('Line 4', 'Organization name');
    $I->fillField('Line 5', 'Last line full width option');

    // Add custom logo.
    $I->uncheckOption('#edit-su-local-foot-use-logo-value');

    // In case there was an image already.
    try {
      $I->click("Remove");
    }
    catch(Exception $e) {
      // Do nothing and carry on.
    }

    // For CircleCI
    $I->attachFile('input[name="files[su_local_foot_loc_img_0]"]', self::LOGO_FILENAME);
    $I->click('Upload');

    $I->click('Save');
    $I->runDrush('cr');
    $I->amOnPage('/');
    $I->seeElement(".su-lockup__custom-logo");
    $I->cantSee("Site title line");
  }

}
