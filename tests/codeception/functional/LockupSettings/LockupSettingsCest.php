<?php

/**
 * Test for the lockup settings
 */
class LockupSettingsCest {

  /**
   * Test the lockup settings overrides.
   */
  public function testLockupSettings(FunctionalTester $I) {
    $I->logInWithRole('site_manager');
    $I->amOnPage('/');
    $I->seeElement('.su-lockup');

  }

  /**
   * Test the lockup settings overrides.
   */
  public function testLockupSettingsA(FunctionalTester $I) {
    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/appearance/lockup-settings');
    $I->selectOption('#edit-su-lockup-options', 'a');
    $I->fillField('Line 1', 'Site title line');
    $I->fillField('Line 2', 'Secondary title line');
    $I->fillField('Line 3', 'Tertiary title line');
    $I->fillField('Line 4', 'Organization name');
    $I->fillField('Line 5', 'Last line full width option');
  }

  /**
   * Test the lockup settings overrides.
   */
  public function testLockupSettingsB(FunctionalTester $I) {
    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/appearance/lockup-settings');
    $I->selectOption("#edit-su-lockup-options", "b");
    $I->fillField('Line 1', 'Site title line');
    $I->fillField('Line 2', 'Secondary title line');
    $I->fillField('Line 3', 'Tertiary title line');
    $I->fillField('Line 4', 'Organization name');
    $I->fillField('Line 5', 'Last line full width option');
  }

}
