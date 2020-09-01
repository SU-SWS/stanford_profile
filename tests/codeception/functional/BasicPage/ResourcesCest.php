<?php

/**
 * Class ResourcesCest.
 *
 * @group basic_page
 */
class ResourcesCest {

  /**
   * Create some resource pages and make sure they display on the list.
   */
  public function testResources(FunctionalTester $I) {
    $dimension = $I->createEntity([
      'vid' => 'su_opportunity_dimension',
      'name' => 'Foo',
    ], 'taxonomy_term');

    $type = $I->createEntity([
      'vid' => 'cs_resource_type',
      'name' => 'Foo',
    ], 'taxonomy_term');
    $foo_aud = $I->createEntity([
      'vid' => 'cs_resource_audience',
      'name' => 'Foo',
    ], 'taxonomy_term');
    $bar_aud = $I->createEntity([
      'vid' => 'cs_resource_audience',
      'name' => 'Bar',
    ], 'taxonomy_term');

    $I->createEntity([
      'type' => 'stanford_page',
      'title' => 'This Foo Page',
      'su_page_resource_audience' => $foo_aud->id(),
      'su_page_resource_type' => $type->id(),
      'su_page_resource_dimension' => $dimension->id(),
    ]);
    $I->createEntity([
      'type' => 'stanford_page',
      'title' => 'This Bar Page',
      'su_page_resource_audience' => $bar_aud->id(),
      'su_page_resource_type' => $type->id(),
      'su_page_resource_dimension' => $dimension->id(),
    ]);

    $paragraph = $I->createEntity([
      'type' => 'stanford_resource_list',
      'su_resource_list' => [
        'target_id' => 'cs_resources',
        'display_id' => 'audience',
        'arguments' => 'Foo',
      ],
    ], 'paragraph');

    $row = $I->createEntity([
      'type' => 'node_stanford_page_row',
      'su_page_components' => [
        'target_id' => $paragraph->id(),
        'entity' => $paragraph,
      ],
    ], 'paragraph_row');

    $resources = $I->createEntity([
      'type' => 'stanford_page',
      'title' => 'Resources',
      'su_page_components' => [
        'target_id' => $row->id(),
        'entity' => $row,
      ],
    ]);

    $I->amOnPage($resources->toUrl()->toString());
    $I->canSeeLink('This Foo Page');
    $I->cantSeeLink('This Bar Page');

    $paragraph->set('su_resource_list', [
      'target_id' => 'cs_resources',
      'display_id' => 'all_list',
    ])->save();

    $I->logInWithRole('site_manager');
    $I->amOnPage($resources->toUrl('edit-form')->toString());
    $I->click('Save');
    $I->canSeeLink('This Foo Page');
    $I->canSeeLink('This Bar Page');
  }

}
