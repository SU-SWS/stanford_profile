<?php

/**
 * Class SystemSiteConfigCest.
 *
 * @group system_site_config
 */
class SystemSiteConfigCest {

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
    $I->runDrush('cache-rebuild');
    $I->amOnPage('/user/logout');
    $I->amOnPage('/');
    $I->canSee('Foo Bar Site');

    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/config/system/basic-site-settings');
    $I->fillField('Site Name', '');
    $I->click('Save');
    $I->runDrush('cache-rebuild');
    $I->amOnPage('/');
    $I->cantSee('Foo Bar Site');
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
    $I->runDrush('cache-rebuild');
    $I->amOnPage('/user/logout');
    $I->amOnPage('/');
    $I->canSee('UA-123456-12');
    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/config/system/basic-site-settings');
    $I->fillField('Google Analytics Account', '');
    $I->click('Save');
    $I->amOnPage('/user/logout');
    $I->runDrush('cache-rebuild');
    $I->amOnPage('/');
    $I->cantSee('UA-12456-12');
  }

  /**
   * @group testme
   */
  public function testBreadcrumbs(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/config/system/basic-site-settings');
    $I->uncheckOption('Display Breadcrumbs');
    $I->click('Save');

    $I->amOnPage('/node/add/stanford_page');
    $I->fillField('Title', 'Foo');
    $I->checkOption('Provide a menu link');
    $I->fillField('Menu link title', 'Foo');
    $I->click('Save');


    $I->amOnPage('/node/add/stanford_page');
    $I->fillField('Title', 'Bar');
    $I->checkOption('Provide a menu link');
    $I->fillField('Menu link title', 'Bar');
    $I->selectOption('Parent item', '-- Foo');
    $I->click('Change parent (update list of weights)');
    $I->click('Save');
    $I->canSee('Bar', 'h1');

    $url = $I->grabFromCurrentUrl();
    $I->cantSee('Foo', '.breadcrumb');
    $I->cantSee('Bar', '.breadcrumb');

    $I->amOnPage('/admin/config/system/basic-site-settings');
    $I->checkOption('Display Breadcrumbs');
    $I->click('Save');

    $I->amOnPage($url);
    $I->canSee('Foo', '.breadcrumb');
    $I->canSee('Bar', '.breadcrumb');

    $I->amOnPage('/admin/config/system/basic-site-settings');
    $I->uncheckOption('Display Breadcrumbs');
    $I->click('Save');

    $I->amOnPage($url);
    $I->cantSee('Foo', '.breadcrumb');
    $I->cantSee('Bar', '.breadcrumb');
  }

}
