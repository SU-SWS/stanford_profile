<?php

use Codeception\Attribute as CodeceptionAttribute;
use Drupal\config_pages\Entity\ConfigPages;
use Faker\Factory;

/**
 * Test for custom block types.
 */
#[CodeceptionAttribute\Group('block')]
class SearchBlockCest {

  /**
   * Test Constructor.
   */
  public function __construct() {
    $this->faker = Factory::create();
  }

  public function _before() {
    if ($cp = ConfigPages::load('stanford_basic_site_settings')) {
      $cp->delete();
    }
  }

  /**
   * Site managers should be able to disable the search block.
   */
  public function testHideSearchBlock(AcceptanceTester $I) {
    $org_term = $I->createEntity([
      'vid' => 'site_owner_orgs',
      'name' => $this->faker->words(2, TRUE),
    ], 'taxonomy_term');

    $I->logInWithRole('site_manager');

    $I->amOnPage('/admin/config/system/basic-site-settings');
    $I->see('Hide Site Search');
    $I->checkOption('Hide Site Search');
    $I->fillField('Site Owner Contact Email (value 1)', $this->faker->email());
    $I->fillField('Primary Site Manager Email (value 1)', $this->faker->email());
    $I->fillField('Accessibility Contact Email (value 1)', $this->faker->email());
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

  /**
   * A page excluded from search indexes should not appear in search results.
   */
  #[CodeceptionAttribute\Group('search-results')]
  public function testExcludeFromSearchResults(AcceptanceTester $I) {
    $body_text = $this->faker->unique()->sentence(10);
    $node = $I->createEntity([
      'type' => 'stanford_page',
      'title' => $this->faker->words(3, TRUE),
      'body' => ['value' => $body_text, 'format' => 'stanford_html']
    ]);

    $search_keys = implode(' ', array_slice(explode(' ', $body_text), 0, 4));

    $I->logInWithRole('site_manager');
    $I->amOnPage($node->toUrl('edit-form')->toString());
    $I->click('Save');

    $I->runDrush('sapi-r full_site_content');
    $I->runDrush('sapi-i full_site_content');

    $I->amOnPage('/search');
    $I->fillField('Keyword Search', $search_keys);
    $I->click('Search', '#views-exposed-form-search-results');
    $I->canSee($node->label());

    $I->amOnPage($node->toUrl('edit-form')->toString());
    $I->checkOption('Yes, exclude this entity from the search indexes.');
    $I->click('Save');
    $I->canSee($node->label(), 'h1');

    $I->amOnPage('/search');
    $I->fillField('Keyword Search', $search_keys);
    $I->click('Search', '#views-exposed-form-search-results');
    $I->cantSee($node->label());
  }

}
