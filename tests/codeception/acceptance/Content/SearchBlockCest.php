<?php

/**
 * Test for custom block types.
 *
 * @group block
 */
class SearchBlockCest {

  /**
   * Site managers should be able to disable the search block.
   */
  public function testHideSearchBlock(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $I->amOnPage('/');
    $I->seeElement('.su-site-search__input');
    $I->amOnPage('/admin/config/system/basic-site-settings');
    $I->see('Hide Site Search');
    $I->checkOption('Hide Site Search');
    $I->click('Save');
    // The settings might have been created or updated.
    $I->see('Site Settings has been');
    $I->amOnPage('/');
    $I->dontSeeElement('.su-site-search__input');

    $I->amOnPage('/admin/config/system/basic-site-settings');
    $I->uncheckOption('Hide Site Search');
    $I->click('Save');
    $I->amOnPage('/');
    $I->seeElement('.su-site-search__input');
  }

}
