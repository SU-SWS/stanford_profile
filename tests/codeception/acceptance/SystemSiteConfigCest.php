<?php

use Drupal\config_pages\Entity\ConfigPages;
use Faker\Factory;

/**
 * Class SystemSiteConfigCest.
 *
 * @group system-site-config
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
  public function _after(AcceptanceTester $I) {
    if ($config_page = ConfigPages::load('stanford_basic_site_settings')) {
      $config_page->delete();
    }
  }

  /**
   * The site manager should be able to change the site name.
   *
   * @group site-settings
   */
  public function testBasicSiteSettings(AcceptanceTester $I) {
    $org_term = $I->createEntity([
      'vid' => 'site_owner_orgs',
      'name' => $this->faker->words(2, TRUE),
    ], 'taxonomy_term');

    $I->logInWithRole('site_manager');
    $I->amOnPage('/');
    $I->cantSee('Foo Bar Site');
    $I->amOnPage('/admin/config/system/basic-site-settings');
    $I->canSeeElement('#contact');
    $I->cantSee('Site URL');
    $I->fillField('Site Name', 'Foo Bar Site');
    $I->fillField('Site Owner Contact Email (value 1)', $this->faker->email);
    $I->fillField('Primary Site Manager Email (value 1)', $this->faker->email);
    $I->fillField('Accessibility Contact Email (value 1)', $this->faker->email);
    $I->selectOption('[name="su_site_org[0][target_id]"]', $org_term->id());
    $I->click('Save');
    $I->canSee('Site Settings has been', '.messages-list');

    $I->amOnPage('/user/logout');
    $I->click('Log out', 'form');
    $I->amOnPage('/');
    $I->canSee('Foo Bar Site');

    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/config/system/basic-site-settings');
    $I->fillField('Site Name', '');
    $I->click('Save');
    $I->canSee('Site Settings has been', '.messages-list');
    $I->amOnPage('/');
    $I->cantSee('Foo Bar Site');
  }

  /**
   * @group header-links
   */
  public function testHeaderLinks(AcceptanceTester $I) {
    $org_term = $I->createEntity([
      'vid' => 'site_owner_orgs',
      'name' => $this->faker->words(2, TRUE),
    ], 'taxonomy_term');

    $button_uri = $this->faker->url();
    $button_text = $this->faker->words(3, TRUE);

    $link_1_uri = $this->faker->url();
    $link_1_text = $this->faker->words(3, TRUE);

    $link_2_uri = $this->faker->url();
    $link_2_text = $this->faker->words(3, TRUE);

    $link_3_uri = $this->faker->url();
    $link_3_text = $this->faker->words(3, TRUE);

    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/config/system/basic-site-settings');
    $I->click('#edit-group-site-header-options summary');
    $I->click('#edit-group-main-menu-options summary');
    $I->fillField('su_site_header_button[0][uri]', $button_uri);
    $I->fillField('su_site_header_button[0][title]', $button_text);

    $I->fillField('su_site_header_links[0][uri]', $link_1_uri);
    $I->fillField('su_site_header_links[0][title]', $link_1_text);

    $I->fillField('su_site_header_links[1][uri]', $link_2_uri);
    $I->fillField('su_site_header_links[1][title]', $link_2_text);

    $I->fillField('su_site_header_links[2][uri]', $link_3_uri);
    $I->fillField('su_site_header_links[2][title]', $link_3_text);

    $I->fillField('Site Owner Contact Email (value 1)', $this->faker->email);
    $I->fillField('Primary Site Manager Email (value 1)', $this->faker->email);
    $I->fillField('Accessibility Contact Email (value 1)', $this->faker->email);
    $I->selectOption('[name="su_site_org[0][target_id]"]', $org_term->id());

    $I->click('Save');
    $I->canSee('has been');

    $I->amOnPage('/');
    $I->canSeeLink($button_text, $button_uri);
    $I->canSee($button_text, '.su-masthead--inner');
    $I->canSeeLink($link_1_text, $link_1_uri);
    $I->canSee($link_1_text, '.su-masthead--inner');
    $I->canSeeLink($link_2_text, $link_2_uri);
    $I->canSee($link_2_text, '.su-masthead--inner');
    $I->canSeeLink($link_3_text, $link_3_uri);
    $I->canSee($link_3_text, '.su-masthead--inner');
  }

  /**
   * Site settings config should change the home, 404, and 403 pages.
   */
  public function testSitePages(AcceptanceTester $I) {
    $org_term = $I->createEntity([
      'vid' => 'site_owner_orgs',
      'name' => $this->faker->words(2, TRUE),
    ], 'taxonomy_term');

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
    $I->fillField('Site Owner Contact Email (value 1)', $this->faker->email);
    $I->fillField('Primary Site Manager Email (value 1)', $this->faker->email);
    $I->fillField('Accessibility Contact Email (value 1)', $this->faker->email);
    $I->selectOption('[name="su_site_org[0][target_id]"]', $org_term->id());
    $I->click('Save');
    $I->canSee('Site Settings has been', '.messages-list');

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
    $I->canSee('Site Settings has been', '.messages-list');

    $I->amOnPage('/');
    $I->canSeeResponseCodeIs(200);
    $I->cantSee($text);
  }

  /**
   * Google Analytics account should be added for anonymous users.
   */
  protected function experimentalTestGoogleAnalytics(AcceptanceTester $I) {
    $org_term = $I->createEntity([
      'vid' => 'site_owner_orgs',
      'name' => $this->faker->words(2, TRUE),
    ], 'taxonomy_term');

    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/config/system/basic-site-settings');
    $I->fillField('Google Analytics Account', 'abcdefg');

    $I->fillField('Site Owner Contact Email (value 1)', $this->faker->email);
    $I->fillField('Primary Site Manager Email (value 1)', $this->faker->email);
    $I->fillField('Accessibility Contact Email (value 1)', $this->faker->email);
    $I->selectOption('[name="su_site_org[0][target_id]"]', $org_term->id());

    $I->click('Save');
    $I->canSee('1 error has been found: Google Analytics Account');
    $I->fillField('Google Analytics Account', 'UA-123456-12');
    $I->click('Save');
    $I->canSee('Site Settings has been', '.messages-list');

    $I->amOnPage('/user/logout');
    $I->click('Log out', 'form');
    $I->amOnPage('/');
    $I->canSee('UA-123456-12');
    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/config/system/basic-site-settings');
    $I->fillField('Google Analytics Account', '');
    $I->click('Save');
    $I->canSee('Site Settings has been', '.messages-list');
    $I->amOnPage('/user/logout');
    $I->click('Log out', 'form');
    $I->amOnPage('/');
    $I->cantSee('UA-12456-12');
  }

}
