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
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/reports/status');
    $I->canSee('9.5', '.system-status-general-info');
    $I->canSee('1 Error', '.system-status-counter--error');
  }

}
