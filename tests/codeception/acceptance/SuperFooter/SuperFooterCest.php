<?php

/**
 * Test for the Super Footer.
 */
class SuperFooterCest {

  public function _after(AcceptanceTester $I) {
    $config_page = \Drupal::entityTypeManager()
      ->getStorage('config_pages')
      ->load('stanford_super_footer');
    if ($config_page) {
      $config_page->delete();
    }
  }

  /**
   * Test the block exists.
   */
  protected function footestBlockExists(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/structure/block');
    $I->canSee('Super Footer');
  }

  /**
   * Test the Form exists.
   */
  protected function footestFormExists(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/config/system/super-footer');
    $I->canSee('Edit config page Super Footer');
  }

  /**
   * Test the Form Settings.
   */
  protected function footestFormSettings(AcceptanceTester $I) {
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
    $I->see('Super Footer has been', '.messages-list');

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
  protected function footestAdminUserRole(AcceptanceTester $I) {
    // Admin.
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/config/system/super-footer');
    $I->canSeeResponseCodeIs(200);
    $I->canSee('Edit config page Super Footer');
  }

  /**
   * Test user role permissions.
   */
  protected function footestSiteManagerUserRole(AcceptanceTester $I) {
    // Site Manager.
    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/config/system/super-footer');
    $I->canSeeResponseCodeIs(200);
    $I->canSee('Edit config page Super Footer');
  }

  /**
   * Test user role permissions.
   */
  protected function footestSiteEditorUserRole(AcceptanceTester $I) {
    // Editor.
    $I->logInWithRole('site_editor');
    $I->amOnPage('/admin/config/system/super-footer');
    $I->canSeeResponseCodeIs(403);
    $I->cantSee('Edit config page Super Footer');
  }

  /**
   * Test user role permissions.
   */
  protected function footestContributorUserRole(AcceptanceTester $I) {
    // Contributor.
    $I->logInWithRole('contributor');
    $I->amOnPage('/admin/config/system/super-footer');
    $I->canSeeResponseCodeIs(403);
    $I->cantSee('Edit config page Super Footer');
  }

}
