<?php

use Drupal\config_pages\Entity\ConfigPages;
use Faker\Factory;

/**
 * Class SystemSiteConfigCest.
 *
 * @group system_site_config
 */
class SystemSiteConfigCest {

  /**
   * Faker service.
   *
   * @var \Faker\Generator
   */
  protected $faker;

  /**
   * Test Constructor.
   */
  public function __construct() {
    $this->faker = Factory::create();
  }

  /**
   * Delete the config page after the test finishes.
   */
  public function __after(AcceptanceTester $I) {
    if ($config_page = ConfigPages::load('stanford_basic_site_settings')) {
      $config_page->delete();
    }
  }

  /**
   * The site manager should be able to change the site name.
   */
  public function testBasicSiteSettings(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $I->amOnPage('/');
    $I->cantSee('Foo Bar Site');
    $I->amOnPage('/admin/config/system/basic-site-settings');
    $I->cantSee('Site URL');
    $I->fillField('Site Name', 'Foo Bar Site');
    $I->click('Save');

    $I->amOnPage('/user/logout');
    $I->amOnPage('/');
    $I->canSee('Foo Bar Site');

    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/config/system/basic-site-settings');
    $I->fillField('Site Name', '');
    $I->click('Save');
    $I->amOnPage('/');
    $I->cantSee('Foo Bar Site');
  }

  /**
   * Site settings config should change the home, 404, and 403 pages.
   */
  public function testSitePages(AcceptanceTester $I) {
    $text = $this->faker->paragraph;
    $paragraph = $I->createEntity([
      'type' => 'stanford_wysiwyg',
      'su_wysiwyg_text' => [
        'format' => 'stanford_html',
        'value' => $text,
      ],
    ], 'paragraph');

    $node = $I->createEntity([
      'type' => 'stanford_page',
      'title' => $this->faker->words(4, TRUE),
      'su_page_components' => [
        'target_id' => $paragraph->id(),
        'entity' => $paragraph,
      ],
    ]);
    $I->amOnPage($node->toUrl()->toString());
    $I->canSee($node->label(), 'h1');
    $I->canSee($text);

    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/config/system/basic-site-settings');
    $I->selectOption('Home Page', $node->label());
    $I->click('Save');
    $I->canSee('Site Settings has been');

    drupal_flush_all_caches();
    $setting = \Drupal::config('system.site')->get('page.front');
    $path = '/node/' . $node->id();
    $I->assertEquals($path, $setting);
    $I->amOnPage('/');
    $I->canSeeResponseCodeIs(200);
    $I->canSee($text);
    $I->cantSee($node->label(), 'h1');

    $I->amOnPage('/admin/config/system/basic-site-settings');
    $I->selectOption('Home Page', '- None -');
    $I->selectOption('404 Page', '- None -');
    $I->selectOption('403 Page', '- None -');
    $I->click('Save');
    $I->canSee('Site Settings has been');

    $I->amOnPage('/');
    $I->canSeeResponseCodeIs(200);
    $I->cantSee($text);
  }

  /**
   * Google Analytics account should be added for anonymous users.
   */
  protected function experimentalTestGoogleAnalytics(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/config/system/basic-site-settings');
    $I->fillField('Google Analytics Account', 'abcdefg');
    $I->click('Save');
    $I->canSee('1 error has been found: Google Analytics Account');
    $I->fillField('Google Analytics Account', 'UA-123456-12');
    $I->click('Save');
    $I->amOnPage('/user/logout');
    $I->amOnPage('/');
    $I->canSee('UA-123456-12');
    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/config/system/basic-site-settings');
    $I->fillField('Google Analytics Account', '');
    $I->click('Save');
    $I->amOnPage('/user/logout');
    $I->amOnPage('/');
    $I->cantSee('UA-12456-12');
  }

}
