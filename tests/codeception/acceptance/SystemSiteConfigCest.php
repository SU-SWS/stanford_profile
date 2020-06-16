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

}
