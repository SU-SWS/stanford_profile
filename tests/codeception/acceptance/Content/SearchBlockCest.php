<?php

use Drupal\config_pages\Entity\ConfigPages;
use Faker\Factory;

/**
 * Test for custom block types.
 *
 * @group block
 */
class SearchBlockCest {

  /**
   * Test Constructor.
   */
  public function __construct() {
    $this->faker = Factory::create();
  }

  public function __before() {
    if ($cp = ConfigPages::load('stanford_basic_site_settings')) {
      $cp->delete();
    }
  }

  /**
   * Site managers should be able to disable the search block.
   */
  protected function footestHideSearchBlock(AcceptanceTester $I) {
    $org_term = $I->createEntity([
      'vid' => 'site_owner_orgs',
      'name' => $this->faker->words(2, TRUE),
    ], 'taxonomy_term');

    $I->logInWithRole('site_manager');

    $I->amOnPage('/admin/config/system/basic-site-settings');
    $I->see('Hide Site Search');
    $I->checkOption('Hide Site Search');
    $I->fillField('Site Owner Contact (value 1)', $this->faker->email);
    $I->fillField('Technical Contact (value 1)', $this->faker->email);
    $I->fillField('Accessibility Contact (value 1)', $this->faker->email);
    $I->selectOption('[name="su_site_org[0][target_id]"]', $org_term->id());
    $I->click('Save');
    // The settings might have been created or updated.
    $I->see('Site Settings has been', '.messages-list');
    $I->amOnPage('/');
    $I->dontSeeElement('.su-site-search__input');

    $I->amOnPage('/admin/config/system/basic-site-settings');
    $I->uncheckOption('Hide Site Search');
    $I->click('Save');
    $I->canSee('Site Settings has been', '.messages-list');
    $I->amOnPage('/');
    $I->seeElement('.su-site-search__input');
  }

}
