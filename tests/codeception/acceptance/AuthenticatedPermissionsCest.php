<?php

/**
 * Test the restrictions on authenticated users.
 */
class AuthenticatedPermissionsCest {

  /**
   * Set up a file to test PHP injection.
   */
  public function _before(AcceptanceTester $I) {
    $dir = rtrim(codecept_data_dir(), '/');
    $file = "$dir/injection.php";
    if (!file_exists($dir)) {
      mkdir($dir, 0777, TRUE);
    }
    if (!file_exists($file)) {
      file_put_contents($file, '<?php echo("injection test"); die(); ?>');
    }
  }

  /**
   * Remove the php injection file.
   */
  public function _after(AcceptanceTester $I) {
    $file = rtrim(codecept_data_dir(), '/') . '/injection.php';
    if (file_exists($file)) {
      unlink($file);
    }
  }

  /**
   * Make sure authenticated users can't access things they should not.
   */
  public function testAuthenticatedUserRestrictions(AcceptanceTester $I) {
    $I->logInWithRole('authenticated');
    $I->amOnPage('/');
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
    $I->amOnPage('/admin/users');
    $I->canSeeResponseCodeIs(403);
    $I->amOnPage('/admin/reports');
    $I->canSeeResponseCodeIs(403);
    $I->amOnPage('/admin/reports/status');
    $I->canSeeResponseCodeIs(403);
  }

  /**
   * Make sure authenticated users can access things they should.
   */
  public function testAuthenticatedUserPermissions(AcceptanceTester $I) {
    $I->logInWithRole('authenticated');
    $I->amOnPage('/patterns');
    $I->canSeeResponseCodeIs(200);
  }

  /**
   * Site Manager cannot escalate their own role above Site Manager.
   */
  public function testSiteManagerEscalationSelf(AcceptanceTester $I) {
    $site_manager = $I->logInWithRole('site_manager');
    $site_manager_id = $site_manager->id();
    $I->amOnPage('/admin/users');
    $I->canSee($site_manager->getDisplayName());
    $I->click(['link' => $site_manager->getDisplayName()]);
    $I->click('.roles.tabs__tab a');
    $I->canSeeInCurrentUrl("/user/$site_manager_id/roles");
    $I->dontSee('Administrator');
    $I->dontSee('Site Builder');
    $I->dontSee('Site Developer');
  }

  /**
   * Site Manager cannot escalate others' role above Site Manager.
   */
  public function testSiteManagerEscalationOthers(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/users');
    $I->canSee('Morgan');
    $I->click('Morgan');
    $I->click('.roles.tabs__tab a');
    $I->dontSee('Administrator');
    $I->dontSee('Site Builder');
    $I->dontSee('Site Developer');
  }

  /**
   * PHP code is not allowed in redirects.
   */
  public function testPhpInRedirect(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/config/search/redirect/add');
    $I->fillField('#edit-redirect-source-0-path', 'home');
    $I->fillField('#edit-redirect-redirect-0-uri', '<?php echo("injection"); ?>');
    $I->click('Save');
    $I->dontSee('injection');
    $I->see('Manually entered paths should start with one of the following characters:');
  }

  /**
   * PHP code is escaped and not run when added to content.
   */
  public function testPhpInContent(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $I->amOnPage('/node/add/stanford_page');
    $I->fillField('#edit-title-0-value', '<?php echo("injection test"); die(); ?>');
    $I->click('Save');
    $I->seeInCurrentUrl('node');
    $I->seeElement('.su-global-footer__copyright');
  }

  /**
   * PHP files are not allowed as uploads for favicons.
   */
  public function testPhpUploadInFavicon(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/appearance/settings');
    $I->seeCheckboxIsChecked('#edit-default-favicon');
    $I->uncheckOption('#edit-default-favicon');
    $I->see('Upload favicon image');
    $I->attachFile('Upload favicon image', 'injection.php');
    $I->click('#edit-submit');
    $I->see('Only files with the following extensions are allowed');
    $I->checkOption('#edit-default-favicon');
    $I->click('#edit-submit');
  }

  /**
   * PHP files are not allowed as uploads for the logo.
   */
  public function testPhpUploadInLogo(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/appearance/settings');
    $I->seeCheckboxIsChecked('#edit-default-logo');
    $I->uncheckOption('#edit-default-logo');
    $I->see('Upload logo image');
    $I->attachFile('Upload logo image', 'injection.php');
    $I->click('#edit-submit');
    $I->see('Only files with the following extensions are allowed');
    $I->see('The image file is invalid or the image type is not allowed.');
    $I->checkOption('#edit-default-logo');
    $I->click('#edit-submit');
  }

}
