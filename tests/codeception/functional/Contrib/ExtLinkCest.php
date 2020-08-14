<?php

/**
 * Test the external link module functionality.
 */
class ExtLinkCest {

  /**
   * Test that revisions are trimmed after cron runs.
   */
  public function testExtLink(FunctionalTester $I) {
    $I->amOnPage('/');
    $I->waitForAjaxToFinish();

    // Validate email links.
    $I->canSeeNumberOfElements('a.mailto svg.mailto', 3);

    // External Links in the page-content region.
    $I->canSeeNumberOfElements('#page-content a.su-link--external svg.su-link--external', 1);

    // External links in the local footer.
    $I->canSeeNumberOfElements('.su-local-footer__cell2 a.su-link--external svg.su-link--external', 4);
  }

}
