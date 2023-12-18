<?php

/**
 * System tests.
 *
 * @group system
 */
class SystemCest {

  /**
   * Test the site status report.
   */
  public function testSiteStatus(AcceptanceTester $I) {
    $I->runDrush('xmlsitemap:rebuild');
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/reports/status');
    $I->canSee('10.2', '.system-status-general-info');
    if ($I->grabMultiple('.system-status-counter--error')) {
      $I->canSee('1 Error', '.system-status-counter--error');
      $I->canSee('Access to update.php ', '.system-status-report__status-icon--error');
    }

    if (\Drupal::moduleHandler()->moduleExists('chosen')) {
      $I->canSee('Chosen Javascript file');
      $I->cantSee('Chosen JavaScript file', '.system-status-report__status-icon--error');
    }
  }

}
