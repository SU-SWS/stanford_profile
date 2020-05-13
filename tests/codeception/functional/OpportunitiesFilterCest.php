<?php

/**
 * Class OpportunitiesFilterCest.
 */
class OpportunitiesFilterCest {

  /**
   * Test the PDB is available and displays nodes when filtering.
   */
  public function testFilters(FunctionalTester $I) {
    /** @var \Drupal\node\NodeInterface $node */
    $I->logInWithRole('site_manager');
    $node = $I->createEntity([
      'type' => 'stanford_page',
      'title' => 'Filters Page',
    ]);
    $filter_url = $node->toUrl()->toString();
    $this->createOpportunityNodes($I);

    $I->amOnPage($filter_url);
    $I->click('Layout');
    $I->click('Add block');
    $I->waitForAjaxToFinish();
    $I->click('Opportunities Filtering List');
    $I->waitForAjaxToFinish();
    $I->click('Add block');
    $I->waitForAjaxToFinish();
    $I->click('Add block');
    $I->waitForAjaxToFinish();
    $I->click('Opportunities: All Filtered');
    $I->waitForAjaxToFinish();
    $I->click('Add block');
    $I->waitForAjaxToFinish();
    
    // Scroll up because the admin toolbar sometimes overlays the task links.
    $I->scrollTo(['css' => '.su-brand-bar']);
    $I->click('Save layout');
    $I->canSeeNumberOfElements('.views-row', 10);

    $I->waitForElementVisible('.MuiFormControl-root', 5);
    $I->click('.su_opp_type-select');
    $I->click('.MuiAutocomplete-listbox li[aria-disabled="false"]');

    $I->click('.su_opp_open_to-select');
    $I->click('.MuiAutocomplete-listbox li[aria-disabled="false"]');

    $I->click('.su_opp_time_year-select');
    $I->click('.MuiAutocomplete-listbox li[aria-disabled="false"]');

    $I->click('Search', '#opportunities-filter-list');
    $I->canSeeNumberOfElements('.views-row', [1, 10]);
    $I->canSee('Showing Results For:');
  }

  /**
   * Test the exposed filters action works correctly.
   */
  public function testViewExposedFilter(FunctionalTester $I) {
    $I->logInWithRole('site_manager');
    $node = $I->createEntity([
      'type' => 'stanford_page',
      'title' => 'Filters Page',
    ]);
    $filter_url = $node->toUrl()->toString();
    $this->createOpportunityNodes($I);

    $node = $I->createEntity([
      'type' => 'stanford_page',
      'title' => 'Test Page',
    ]);
    $I->amOnPage($node->toUrl()->toString());
    $I->click('Layout');
    $I->click('Add block');
    $I->waitForAjaxToFinish();
    $I->click('Exposed form: su_opportunities-filtered_all');
    $I->waitForAjaxToFinish();
    $I->fillField('Form action URL', $filter_url);

    $I->click('Add block');
    $I->waitForAjaxToFinish();
    $I->click('Save layout');
    $I->click('Apply');
    $I->canSeeInCurrentUrl($filter_url);
    $I->wait(5);
  }

  /**
   * Create some opportunity nodes.
   */
  protected function createOpportunityNodes(FunctionalTester $I) {
    $terms = $this->createTerms($I);
    for ($j = 0; $j <= 10; $j++) {
      $values = [
        'type' => 'su_opportunity',
        'title' => "Opportunity $j",
        'su_opp_open_to' => $terms['su_opportunity_open_to'][$j % 3]->id(),
        'su_opp_location' => $terms['su_opportunity_location'][($j + 1) % 3]->id(),
        'su_opp_time_year' => $terms['su_opportunity_time'][($j + 2) % 3]->id(),
        'su_opp_type' => $terms['su_opportunity_type'][$j % 3]->id(),
      ];
      $I->createEntity($values);
    }
  }

  /**
   * Create some taxonomy terms.
   */
  protected function createTerms(FunctionalTester $I) {
    $vids = [
      'su_opportunity_open_to',
      'su_opportunity_location',
      'su_opportunity_time',
      'su_opportunity_type',
    ];
    $terms = [];
    foreach ($vids as $vid) {
      $terms[$vid][] = $I->createEntity([
        'vid' => $vid,
        'name' => 'foo',
      ], 'taxonomy_term');
      $terms[$vid][] = $I->createEntity([
        'vid' => $vid,
        'name' => 'bar',
      ], 'taxonomy_term');
      $terms[$vid][] = $I->createEntity([
        'vid' => $vid,
        'name' => 'baz',
      ], 'taxonomy_term');
    }
    return $terms;
  }

}
