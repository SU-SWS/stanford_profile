<?php

/**
 * Class DefaultContentCest.
 *
 * @group content
 */
class DefaultContentCest {

  /**
   * Default content pages and meta data exist.
   */
  public function testExistingContent(AcceptanceTester $I) {
    $pages = [
      '/',
      '/resources',
      'research',
      'about',
      'page-not-found',
      'access-denied',
    ];

    foreach ($pages as $page) {
      $I->amOnPage($page);
      $I->canSeeResponseCodeIs(200);
    }
  }

  /**
   * There should be at least 15 media items.
   */
  public function testMedia(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/content/media');
    $I->canSeeNumberOfElements('table img, form img', [15, 999]);
  }

  /**
   * XML Sitemap should exist after cron.
   */
  public function testXmlSitemap(AcceptanceTester $I) {
    $I->runDrush('xmlsitemap:regenerate');
    $I->amOnPage('/sitemap.xml');
    $I->canSeeResponseCodeIs(200);
  }

}
