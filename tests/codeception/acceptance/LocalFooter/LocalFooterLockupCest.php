<?php

use Drupal\config_pages\Entity\ConfigPages;

/**
 * Test for the local lockup settings.
 *
 * @group local_footer
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
   * Path to the logo image during the test.
   *
   * @var string
   */
  protected $logoPath;

  /**
   * Setup work before running tests.
   *
   * @param AcceptanceTester $I
   *  The working class.
   */
  function _before(AcceptanceTester $I) {
    $this->DATA_DIR = rtrim(codecept_data_dir(), '/\\');
    // Copy our assets into place first.
    if (!file_exists($this->DATA_DIR . DIRECTORY_SEPARATOR)) {
      mkdir($this->DATA_DIR, 0777, TRUE);
    }
    $this->logoPath = $this->DATA_DIR . DIRECTORY_SEPARATOR . str_replace('/', '-', self::class) . self::LOGO_FILENAME;
    copy(__DIR__ . DIRECTORY_SEPARATOR . self::LOGO_FILENAME, $this->logoPath);
  }

  /**
   * Always cleanup the config after testing.
   *
   * @param \AcceptanceTester $I
   *   Tester.
   */
  public function _after(AcceptanceTester $I) {
    if ($config_page = ConfigPages::load('stanford_local_footer')) {
      $config_page->delete();
    }

    $config_page = ConfigPages::create([
      'type' => 'stanford_local_footer',
      'su_local_foot_use_loc' => TRUE,
      'su_local_foot_use_logo' => TRUE,
      'su_local_foot_loc_op' => 'a',
      'context' => 'a:0:{}',
    ]);
    $config_page->save();

    // Clean up our assets.
    if (file_exists($this->logoPath)) {
      unlink($this->logoPath);
    }
  }

  /**
   * Test the lockup settings overrides.
   */
  public function testLockupSettingsA(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/config/system/local-footer');
    $I->canSeeResponseCodeIs(200);
    $I->uncheckOption('Use Default Lockup');
    $I->selectOption('Lockup Options', 'a');
    $I->checkOption('Use the logo supplied by the theme');
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
    $I->uncheckOption('Use Default Lockup');
    $I->selectOption('Lockup Options', "b");
    $I->checkOption('Use the logo supplied by the theme');
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
    $I->uncheckOption('Use Default Lockup');
    $I->selectOption('Lockup Options', "d");
    $I->checkOption('Use the logo supplied by the theme');
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
    $I->uncheckOption('Use Default Lockup');
    $I->selectOption('Lockup Options', "e");
    $I->checkOption('Use the logo supplied by the theme');
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
    $I->uncheckOption('Use Default Lockup');
    $I->selectOption('Lockup Options', "h");
    $I->checkOption('Use the logo supplied by the theme');
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
    $I->uncheckOption('Use Default Lockup');
    $I->selectOption('Lockup Options', "i");
    $I->checkOption('Use the logo supplied by the theme');
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
    $I->uncheckOption('Use Default Lockup');
    $I->selectOption('Lockup Options', "m");
    $I->checkOption('Use the logo supplied by the theme');
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
    $I->uncheckOption('Use Default Lockup');
    $I->selectOption('Lockup Options', "o");
    $I->checkOption('Use the logo supplied by the theme');
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
    $I->uncheckOption('Use Default Lockup');
    $I->selectOption('Lockup Options', "p");
    $I->checkOption('Use the logo supplied by the theme');
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
    $I->uncheckOption('Use Default Lockup');
    $I->selectOption('Lockup Options', "r");
    $I->checkOption('Use the logo supplied by the theme');
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
    $I->uncheckOption('Use Default Lockup');
    $I->selectOption('Lockup Options', "s");
    $I->checkOption('Use the logo supplied by the theme');
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
    $I->uncheckOption('Use Default Lockup');
    $I->selectOption('Lockup Options', "t");
    $I->checkOption('Use the logo supplied by the theme');
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
    $I->uncheckOption('Use Default Lockup');
    $I->selectOption('Lockup Options', 'a');
    $I->fillField('Line 1', 'Site title line');
    $I->fillField('Line 2', 'Secondary title line');
    $I->fillField('Line 3', 'Tertiary title line');
    $I->fillField('Line 4', 'Organization name');
    $I->fillField('Line 5', 'Last line full width option');

    // Add custom logo.
    $I->uncheckOption('Use the logo supplied by the theme');

    // In case there was an image already.
    if ($I->grabMultiple('input[value="Remove"]')) {
      $I->click("Remove");
    }

    $I->attachFile('input[name="files[su_local_foot_loc_img_0]"]', $this->logoPath);
    $I->click('Upload');

    $I->click('Save');
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
    $I->uncheckOption('Use Default Lockup');
    $I->selectOption('Lockup Options', 'none');
    $I->fillField('Line 1', 'Site title line');
    $I->fillField('Line 2', 'Secondary title line');
    $I->fillField('Line 3', 'Tertiary title line');
    $I->fillField('Line 4', 'Organization name');
    $I->fillField('Line 5', 'Last line full width option');

    // Add custom logo.
    $I->uncheckOption('Use the logo supplied by the theme');

    // In case there was an image already.
    if ($I->grabMultiple('input[value="Remove"]')) {
      $I->click("Remove");
    }

    // For CircleCI
    $I->attachFile('input[name="files[su_local_foot_loc_img_0]"]', $this->logoPath);
    $I->click('Upload');

    $I->click('Save');
    $I->amOnPage('/');
    $I->seeElement(".su-lockup__custom-logo");
    $I->cantSee("Site title line");
  }

}
