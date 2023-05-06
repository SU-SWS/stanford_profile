<?php

/**
 * Class DefaultContentCest.
 *
 * @group content
 */
class DefaultContentCest {

  /**
   * Test default images.
   */
  public function testDefaultImages(AcceptanceTester $I) {
    $files = \Drupal::entityTypeManager()->getStorage('file')->loadMultiple();

    /** @var \Drupal\file\FileInterface $file */
    foreach ($files as $file) {
      $real_path = \Drupal::service('file_system')
        ->realpath($file->getFileUri());
      $I->assertTrue(file_exists($real_path), 'File exists: ' . $real_path);
    }
    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/content/media');
    $I->click('default-homepage_card-image.jpg');
    $I->click('default-homepage_card-image.jpg');
    $I->canSeeInCurrentUrl('files/media/image/default-homepage_card-image.jpg');
    $I->canSeeResponseCodeIs(200);
  }

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
   *
   * @group menu_link_weight
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
      $I->canSee('The path cannot be edited');
      $I->amOnPage('/admin/structure/menu/manage/main');
    }
  }

}
