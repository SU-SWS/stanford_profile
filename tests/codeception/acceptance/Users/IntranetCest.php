<?php

/**
 * Class IntranetCest.
 *
 * @group users
 * @group mikes
 */
class IntranetCest {

  protected $intranetWasEnabled = FALSE;

  public function _before(AcceptanceTester $I) {
    $this->intranetWasEnabled = (bool) $I->runDrush('sget stanford_intranet');
  }

  public function _after(AcceptanceTester $I) {
    $I->runDrush('sset stanford_intranet ' . (int) $this->intranetWasEnabled);
  }

  /**
   * Simple full site access check.
   */
  public function testIntranet(AcceptanceTester $I) {
    if (!$this->intranetWasEnabled) {
      $I->runDrush('sset stanford_intranet 1');
      $I->runDrush('cache-rebuild');
    }

    $I->amOnPage('/');
    $I->canSee('Access denied');
    $I->canSeeNumberOfElements('.su-multi-menu__menu a', 0);

    $I->logInWithRole('authenticated');
    $I->amOnPage('/');
    $I->canSeeNumberOfElements('.su-multi-menu__menu a', [0, 99]);
  }

  public function testAccessField(AcceptanceTester $I) {
    $I->runDrush('sset stanford_intranet 0');
    $I->logInWithRole('contributor');
    $I->amOnPage('/node/add/stanford_page');
    $I->cantSee('Allow users with the following roles to view this content');

    $I->runDrush('sset stanford_intranet 1');
    $I->amOnPage('/node/add/stanford_page');
    $I->cantSee('Allow users with the following roles to view this content');

    $I->amOnPage('/user/logout');
    $I->logInWithRole('site_manager');
    $I->amOnPage('/node/add/stanford_page');
    $I->canSee('Allow users with the following roles to view this content');
    $I->canSee('Site Manager');
    $I->cantSeeCheckboxIsChecked('Contributor');
    $I->cantSeeCheckboxIsChecked('Site Manager');

    $I->fillField('Title', 'Test Private Access');
    $I->checkOption('Stanford Student');
    $I->click('Save');
    $I->canSee('Test Private Access', 'h1');
//    $page_url = $I->grabFromCurrentUrl();
//
//    $I->amOnPage('/user/logout');
//    $I->amOnPage($page_url);
//    $I->canSee('Access denied');
//    $I->logInWithRole('stanford_staff');
//    $I->amOnPage($page_url);
//    $I->canSee('Access denied');
  }

}
