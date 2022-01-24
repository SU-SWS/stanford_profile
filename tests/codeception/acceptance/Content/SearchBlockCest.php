<?php

/**
 * Test for custom block types.
 *
 * @group block
 */
class SearchBlockCest {

  /**
   * Site managers should be able to disable the search block
   */
  public function testHideSearchBlock(AcceptanceTester $I) {
    $I->runDrush('config:pages-set-field-value stanford_basic_site_settings su_hide_site_search 0');
    $I->logInWithRole('site_manager');
    $I->amOnPage('/');
    $I->seeElement('.su-site-search__input');
    $I->amOnPage('/admin/config/system/basic-site-settings');
    $I->see('Hide Site Search');
    $I->checkOption('Hide Site Search');
    $I->click('Save');
    $I->see('Site Settings has been updated');
    $I->amOnPage('/');
    $I->dontSeeElement('.su-site-search__input');

    $I->amOnPage('/admin/config/system/basic-site-settings');
    $I->uncheckOption('Hide Site Search');
    $I->click('Save');
    $I->amOnPage('/');
    $I->seeElement('.su-site-search__input');
  }

}
