<?php

/**
 * Spotlight content type tests.
 */
class OpportunityContentCest {

  /**
   * Create a new piece of Opportunity content.
   */
  public function testOpportunityContentCreation(\AcceptanceTester $I) {
    $this->createTaxonomyTerms($I, [
      'Stanford',
      'Canada',
    ], 'su_opportunity_location');
    $this->createTaxonomyTerms($I, [
      'Freshmen',
      'Senior',
    ], 'su_opportunity_open_to');

    $this->createTaxonomyTerms($I, [
      'Fall',
      'Winter',
    ], 'su_opportunity_time');

    $this->createTaxonomyTerms($I, [
      'Internship',
      'Public Service',
    ], 'su_opportunity_type');

    $I->logInWithRole('site_manager');
    $I->amOnPage('/node/add/su_opportunity');
    $I->fillField('Title', 'Test Opportunity');
    $I->fillField('Body', 'Lorem Ipsum');
    $I->click('Save');
    $I->canSee('Test Opportunity');
    $I->canSee('Lorem Ipsum');
  }

  /**
   * The importer should bring in some content.
   */
  public function testImporter(\AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/structure/migrate/manage/opportunities/migrations');
    $I->canSee('solo_opportunities');
    $total_items = $I->grabTextFrom('table tbody td:nth-child(4)');
    $I->assertGreaterOrEquals(1, (int) $total_items);

    // These steps require increased execution time. Figure that out and then
    // these can be uncommented.
    // $I->runDrush('mim solo_opportunities');
    // $I->amOnPage('/admin/content');
    // $I->canSee('Opportunity', '.vbo-table');
  }

  /**
   * Create taxonomy terms for testing.
   */
  protected function createTaxonomyTerms(\AcceptanceTester $I, array $terms, $vocab) {
    foreach ($terms as $term) {
      $I->createEntity([
        'name' => $term,
        'vid' => $vocab,
      ], 'taxonomy_term');
    }
  }

}
