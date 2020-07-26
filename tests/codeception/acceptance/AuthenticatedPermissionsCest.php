<?php

/**
 * Test the restrictions on authenticated users.
 */
use StanfordCaravan\Codeception\Drupal\DrupalUser;

class AuthenticatedPermissionsCest {


  public function _before(AcceptanceTester $I) {
      file_put_contents(codecept_data_dir() . '/injection.php', '<?php echo("injection test"); die(); ?>');
  }

  public function _after(AcceptanceTester $I) {
      unlink(codecept_data_dir() . '/injection.php');
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
    $I->amOnPage('/admin/people');
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
   * Site Manager cannot escalate own role above Site Manager
   * Site Manager cannot escalate the role of others above Site Manager
   * PHP entered in a redirect gets sanitized
   * PHP code gets sanitized on content creation
   * Try to upload a PHP file in Media and it fails form validation
   * Try to upload a PHP file as a favicon in theme settings and have it fail (error message for me was "For security reasons, your upload has been renamed to foo.php.txt.")
   * Try to upload a PHP file as a logo in theme settings and have it fail (error message for me was "The image file is invalid or the image type is not allowed. Allowed types: gif, jpe, jpeg, jpg, png")
   */

   public function testSiteManagerEscalationSelf(AcceptanceTester $I) {
     $site_manager = $I->logInWithRole('site_manager');
     $site_manager_id = $site_manager->id();
     $I->amOnPage('/admin/people');
     $I->canSee($site_manager->getUsername());
     $I->click(['link' => $site_manager->getUsername()]);
     $I->click('.roles.tabs__tab a');
     $I->canSeeInCurrentUrl("/user/$site_manager_id/roles");
     $I->dontSee('Administrator');
     $I->dontSee('Site Builder');
     $I->dontSee('Site Developer');
   }

   public function testSiteManagerEscalationOthers(AcceptanceTester $I) {
     $I->logInWithRole('site_manager');
     $I->amOnPage('/admin/people');
     $I->canSee('Morgan');
     $I->click(['link' => 'Morgan']);
     $I->click('.roles.tabs__tab a');
     $I->dontSee('Administrator');
     $I->dontSee('Site Builder');
     $I->dontSee('Site Developer');
   }

   public function testPhpInRedirect(AcceptanceTester $I) {
     $I->logInWithRole('site_manager');
     $I->amOnPage('/admin/config/search/redirect/add');
     $I->fillField('#edit-redirect-source-0-path', 'home');
     $I->fillField('#edit-redirect-redirect-0-uri', '<?php echo("injection"); ?>');
     $I->click('Save');
     $I->dontSee('injection');
     $I->see('Manually entered paths should start with one of the following characters:');
   }

   public function testPhpInContent(AcceptanceTester $I) {
      $I->logInWithRole('site_manager');
      $I->amOnPage('/node/add/stanford_page');
      $I->fillField('#edit-title-0-value', '<?php echo("injection test"); die(); ?>');
      $I->click('Save');
      $I->seeInCurrentUrl('node');
      $I->seeElement('.su-global-footer__copyright');
   }

   public function testPhpUploadInMedia(AcceptanceTester $I) {
      return;
   }

   public function testPhpUploadInFavicon(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/appearance/settings');
    $I->seeCheckboxIsChecked('#edit-default-favicon');
    $I->uncheckOption('#edit-default-favicon');
    $I->see('Upload favicon image');
    $I->attachFile('Upload favicon image', 'injection.php');
    $I->click('#edit-submit');
    $I->see('For security reasons, your upload has been renamed');
   }

   public function testPhpUploadInLogo(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/appearance/settings');
    $I->seeCheckboxIsChecked('#edit-default-logo');
    $I->uncheckOption('#edit-default-logo');
    $I->see('Upload logo image');
    $I->attachFile('Upload logo image', 'injection.php');
    $I->click('#edit-submit');
    $I->see('For security reasons, your upload has been renamed');
    $I->see('The specified file injection.php.txt could not be uploaded.');
    $I->see('The image file is invalid or the image type is not allowed. Allowed types: gif, jpe, jpeg, jpg, png');
   }

}
