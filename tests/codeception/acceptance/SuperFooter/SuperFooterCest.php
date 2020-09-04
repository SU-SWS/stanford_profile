<?php

/**
 * Test for the Super Footer.
 */
class SuperFooterCest {

  /**
   * Test the block exists.
   */
  public function testBlockExists(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/structure/block');
    $I->canSee('Super Footer');
  }

  /**
   * Test the Form exists.
   */
  public function testFormExists(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/config/system/super-footer');
    $I->canSee('Edit config page Super Footer');
  }

  /**
   * Test the Form Settings.
   */
  public function testFormSettings(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/config/system/super-footer');
    $I->checkOption('#edit-su-super-foot-enabled-value');

    $I->fillField('Super Footer Title', 'Super Footer Title');
    $I->fillField('#edit-su-super-foot-text-0-value', '<p>Super footers are super.</p>');
    $I->fillField('#edit-su-super-foot-link-0-uri', '<front>');
    $I->fillField('#edit-su-super-foot-link-0-title', 'Action link');
    $I->fillField('#edit-su-super-foot-intranet-0-uri', 'https://stanford.edu/');
    $I->fillField('#edit-su-super-foot-intranet-0-title', 'Intranet Link');
    $I->click('Save');
    $I->amOnPage("/");
    $I->seeElement(".block-config-pages-super-footer");
    $I->canSee("Super Footer Title");
    $I->canSee("Super footers are super.");
    $I->canSee("Intranet Link");
    $I->canSee("Action link");
  }

  /**
   * Test user role permissions.
   */
  public function testAdminUserRole(AcceptanceTester $I) {
    // Admin.
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/config/system/super-footer');
    $I->canSeeResponseCodeIs(200);
    $I->canSee('Edit config page Super Footer');
  }

  /**
   * Test user role permissions.
   */
  public function testSiteManagerUserRole(AcceptanceTester $I) {
    // Site Manager.
    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/config/system/super-footer');
    $I->canSeeResponseCodeIs(200);
    $I->canSee('Edit config page Super Footer');
  }

  /**
   * Test user role permissions.
   */
  public function testSiteEditorUserRole(AcceptanceTester $I) {
    // Editor.
    $I->logInWithRole('site_editor');
    $I->amOnPage('/admin/config/system/super-footer');
    $I->canSeeResponseCodeIs(403);
    $I->cantSee('Edit config page Super Footer');
  }

  /**
   * Test user role permissions.
   */
  public function testContributorUserRole(AcceptanceTester $I) {
    // Contributor.
    $I->logInWithRole('contributor');
    $I->amOnPage('/admin/config/system/super-footer');
    $I->canSeeResponseCodeIs(403);
    $I->cantSee('Edit config page Super Footer');
  }

}
