<?php

/**
 * Test for the Global Messages.
 */
class GlobalMessageCest {

  /**
   * Test the block exists.
   */
  public function testBlockExists(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/structure/block');
    $I->canSee('Global Messages');
  }

  /**
   * Test the Form exists.
   */
  public function testFormExists(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/config/system/global-message');
    $I->canSee('Edit config page Global Message');
  }

  /**
   * Test the Form Settings.
   */
  public function testFormSettings(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/config/system/global-message');
    $I->checkOption('#edit-su-global-msg-enabled-value');
    $I->selectOption("#edit-su-global-msg-type", "success");
    $I->fillField('Label', 'MESSAGE LABEL');
    $I->fillField('Headline', 'MESSAGE HEADER');
    $I->fillField('#edit-su-global-msg-message-0-value', '<p>This is the message body.</p>');
    $I->fillField('URL', '<front>');
    $I->fillField('Link text', 'Action link');
    $I->click('Save');
    $I->amOnPage("/");
    $I->seeElement(".su-alert--success");
    $I->canSee("MESSAGE LABEL");
    $I->canSee("MESSAGE HEADER");
    $I->canSee("This is the message body");
    $I->canSee("Action link");
    $I->amOnPage('/admin/config/system/global-message');
    $I->selectOption("#edit-su-global-msg-type", "error");
    $I->click('Save');
    $I->amOnPage("/");
    $I->seeElement(".su-alert--error");
  }

  /**
   * Test user role permissions.
   */
  public function testAdminUserRole(AcceptanceTester $I) {
    // Admin.
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/config/system/global-message');
    $I->canSeeResponseCodeIs(200);
    $I->canSee('Edit config page Global Message');
  }

  /**
   * Test user role permissions.
   */
  public function testSiteManagerUserRole(AcceptanceTester $I) {
    // Site Manager.
    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/config/system/global-message');
    $I->canSeeResponseCodeIs(200);
    $I->canSee('Edit config page Global Message');
  }

  /**
   * Test user role permissions.
   */
  public function testSiteEditorUserRole(AcceptanceTester $I) {
    // Editor.
    $I->logInWithRole('site_editor');
    $I->amOnPage('/admin/config/system/global-message');
    $I->canSeeResponseCodeIs(403);
    $I->cantSee('Edit config page Global Message');
  }

  /**
   * Test user role permissions.
   */
  public function testContributorUserRole(AcceptanceTester $I) {
    // Contributor.
    $I->logInWithRole('contributor');
    $I->amOnPage('/admin/config/system/global-message');
    $I->canSeeResponseCodeIs(403);
    $I->cantSee('Edit config page Global Message');
  }

}
