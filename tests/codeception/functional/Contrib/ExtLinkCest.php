<?php

/**
 * Test the external link module functionality.
 */
class ExtLinkCest {

  /**
   * Test external links get the added class and svg.
   */
  public function testExtLink(FunctionalTester $I) {

    // Local footer block isnt showing up on circle for some reason.
    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/config/system/local-footer');
    $I->checkOption('Enabled');
    $fields = [
      'su_local_foot_primary[0][uri]' => 'https://google.com',
      'su_local_foot_primary[0][title]' => 'Primary Link',
      'su_local_foot_second[0][uri]' => 'https://stanford.edu',
      'su_local_foot_second[0][title]' => 'Secondary Link',
    ];
    foreach ($fields as $selector => $value) {
      $I->fillField($selector, $value);
    }

    $I->click('Save');
    $I->runDrush('cache-rebuild');

    // Validate email links.
    $I->amOnPage('/');
    $mails = $I->grabMultiple('a.mailto svg.mailto');
    $I->assertEquals(count($mails), 3);

    // External Links in the page-content region.
    $pageExternals = $I->grabMultiple('#page-content a.su-link--external svg.su-link--external');
    $I->assertEquals(count($pageExternals), 1);

    // External links in the local footer.
    $footerExternals = $I->grabMultiple('.su-local-footer__cell2 a.su-link--external svg.su-link--external');
    $I->assertEquals(count($footerExternals), 2);
  }

}
