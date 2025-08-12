<?php

use Codeception\Attribute as CodeceptionAttribute;

/**
 * System tests.
 */
#[CodeceptionAttribute\Group('system')]
class SystemCest {

  /**
   * Test the site status report.
   */
  public function testSiteStatus(AcceptanceTester $I) {
    $I->runDrush('xmlsitemap:rebuild');
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/reports/status');
    $I->canSee('11.2', '.system-status-general-info');
    if ($I->grabMultiple('.system-status-counter--error')) {
      $error_count = \Drupal::moduleHandler()
        ->moduleExists('config_inspector') ? '2 Errors' : '1 Error';

      $I->canSee($error_count, '.system-status-counter--error');
      $I->canSee('Access to update.php ', '.system-status-report__status-icon--error');
    }

    if (\Drupal::moduleHandler()->moduleExists('chosen')) {
      $I->canSee('Chosen Javascript file');
      $I->cantSee('Chosen JavaScript file', '.system-status-report__status-icon--error');
    }
  }

  /**
   * Test the login page.
   */
  #[CodeceptionAttribute\Group('403-redirect')]
  public function testLoginPage(AcceptanceTester $I) {
    $I->amOnPage('/admin/config');
    $I->canSeeInCurrentUrl('/user/login');
    $I->canSeeNumberOfElements('h1', 1);
  }

  /**
   * User json api should not exist.
   */
  #[CodeceptionAttribute\Group('jsonapi')]
  public function testJsonApiUser(AcceptanceTester $I){
    $I->amOnPage('/jsonapi/user/user');
    $I->canSeeResponseCodeIs(404);
  }

}
