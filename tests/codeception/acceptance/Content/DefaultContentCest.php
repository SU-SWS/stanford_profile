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
    $I->runDrush('cron');
    $I->amOnPage('/sitemap.xml');
    $I->canSeeResponseCodeIs(200);
  }

  /**
   * Test the default menu items exist with proper destinations.
   */
  public function testMenuItems(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/structure/menu/manage/main');

    $pages = [
      '/about' => 'About',
      '/news' => 'News',
      '/people' => 'People',
      '/research' => 'Research',
      '/resources' => 'Resources',
    ];
    foreach ($pages as $path => $title) {
      $I->canSeeLink($title, $path);

      $I->click('Edit', '#menu-overview tr:contains("' . $title . '")');
      $link_url = $I->grabValueFrom('[name="link[0][uri]"]');
      preg_match('/(\w+) \((\d+)\)/', $link_url, $matches);
      $I->assertCount(3, $matches, 'Link URL should be in the format "page_name (page_id)"');

      $I->amOnPage('/admin/structure/menu/manage/main');
    }
  }

}
