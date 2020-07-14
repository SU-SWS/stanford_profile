<?php

/**
 * Class LocalFooterCest.
 *
 * @group local_footer
 */
class LocalFooterCest {

  /**
   * Only site manager and higher should have access.
   */
  public function testAccess(AcceptanceTester $I) {
    $I->logInWithRole('contributor');
    $I->amOnPage('/admin/config/system/local-footer');
    $I->canSeeResponseCodeIs(403);
    $I->amOnPage('/user/logout');
    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/config/system/local-footer');
    $I->canSeeResponseCodeIs(200);
  }

  /**
   * Changes to the local footer should display correctly.
   */
  public function testCustomLocalFooter(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/config/system/local-footer');
    $I->checkOption('Enabled');
    $I->selectOption('State', 'New York');
    $fields = [
      'Company' => 'Drupal',
      'Street address' => '123 Drupal Dr',
      'City' => 'New York',
      'Zip code' => 12345,
      'su_local_foot_action[0][uri]' => 'http://google.com',
      'su_local_foot_action[0][title]' => 'Action Link',
      'su_local_foot_social[0][uri]' => 'http://facebook.com',
      'su_local_foot_social[0][title]' => 'Facebook Social Link',
      'Primary Links Header' => 'Primary links header',
      'su_local_foot_primary[0][uri]' => 'http://google.com',
      'su_local_foot_primary[0][title]' => 'Primary Link',
      'Secondary Links Header' => 'Secondary Links Header',
      'su_local_foot_second[0][uri]' => 'http://google.com',
      'su_local_foot_second[0][title]' => 'Secondary Link',
      'su_local_foot_f_intro[0][value]' => '<p>Lorem Ipsum</p>',
      'Form Action URL' => 'http://google.com',
      'Signup Button Text' => 'Sign Me Up',
    ];

    foreach ($fields as $selector => $value) {
      $I->fillField($selector, $value);
    }

    $I->click('Save');
    $I->runDrush('cache-rebuild');
    $I->amOnPage('/');
    $I->canSee('123 Drupal Dr');
    $I->canSee('New York, NY 12345');
    $I->canSeeLink('Action Link', 'http://google.com');
    $I->canSeeLink('Facebook Social Link', 'http://facebook.com');
    $I->canSee('Primary links header', 'h2');
    $I->canSeeLink('Primary Link', 'http://google.com');
    $I->canSee('Secondary Links Header', 'h2');
    $I->canSeeLink('Secondary Link', 'http://google.com');
    $I->canSee('Lorem Ipsum', 'p');
    $I->canSeeElement('input[value="Sign Me Up"]');

    $I->amOnPage('/admin/config/system/local-footer');
    $I->uncheckOption('Enabled');
    $I->click('Save');
    $I->runDrush('cache-rebuild');
    $I->amOnPage('/');
    $I->cantSee('123 Drupal Dr');
  }

}
