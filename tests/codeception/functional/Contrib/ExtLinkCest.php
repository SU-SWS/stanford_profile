<?php

/**
 * Test the external link module functionality.
 */
class ExtLinkCest {

  /**
   * Test external links get the added class and svg.
   */
  public function testExtLink(FunctionalTester $I) {
    $I->amOnPage('/');
    $I->waitForAjaxToFinish();

    // Validate email links.
    $mails = $I->grabMultiple('a.mailto svg.mailto');
    $I->assertEquals(count($mails), 3);

    // External Links in the page-content region.
    $pageExternals = $I->grabMultiple('#page-content a.su-link--external svg.su-link--external');
    $I->assertEquals(count($pageExternals), 1);

    // External links in the local footer.
    $footerExternals = $I->grabMultiple('.su-local-footer__cell2 a.su-link--external svg.su-link--external');
    $I->assertEquals(count($footerExternals), 4);
  }

}
