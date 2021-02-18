<?php

/**
 * Test the event series functionality.
 */
class EventsSeriesCest {

  /**
   * Ensure events are in the sitemap.
   */
  public function testXMLSiteMap(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/config/search/xmlsitemap/settings/node/stanford_event_series');
    $I->seeOptionIsSelected("#edit-xmlsitemap-status", "Included");
    $I->seeOptionIsSelected("#edit-xmlsitemap-priority", "0.5 (normal)");
  }

  /**
   * Test metadata settings.
   */
  public function testMetaDataSettings(AcceptanceTester $I) {
    // TODO: Create and export this config.
  }

  /**
   * Test Page Title Conditions.
   */
  public function testPageTitleIgnoreCondition(AcceptanceTester $I) {
    $I->logInWithRole("administrator");
    // Todo: make theme name dynamic.
    $I->amOnPage("/admin/structure/block/manage/stanford_basic_pagetitle");
    $values = $I->grabTextFrom("#edit-visibility-request-path-pages");
    if (is_string($values)) {
      $values = explode("\n", $values);
    }
    $I->assertContains("/event-series*", $values);
  }

  /**
   * Test the the event content type exists and has at least a couple of fields.
   */
  public function testContentTypeExists(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/structure/types/manage/stanford_event_series/fields');
    $I->canSee('su_event_series_subheadline');
    $I->canSee('su_event_series_event');
  }

  /**
   * Test Access to stuff for contrib role.
   */
  public function testContributorPerms(AcceptanceTester $I) {
    $I->logInWithRole('contributor');

    // Can create a node.
    $I->amOnPage("/node/add/stanford_event_series");
    $I->canSeeResponseCodeIs(200);

    // Can not delete a node that is not theirs but can edit.
    $node = $this->createEventSeriesNode($I);
    $id = $node->id();
    $I->amOnPage("/node/$id/edit");
    $I->dontSee("#edit-delete");
    $I->fillField("#edit-title-0-value", "My new title");
    $I->click('Save');
    $I->canSee("My new title");

    // Can see revisions.
    $I->amOnPage("/node/$id/revisions");
    $I->canSee("Current revision");
  }

  /**
   * Test thing.
   */
  public function testEditorPerms(AcceptanceTester $I) {
    $I->logInWithRole('site_editor');

    // Can create a node.
    $I->amOnPage("/node/add/stanford_event_series");
    $I->canSeeResponseCodeIs(200);

    // Can delete a node that is not theirs and can edit.
    $node = $this->createEventSeriesNode($I);
    $id = $node->id();

    $I->amOnPage("/node/$id/delete");
    $I->canSeeResponseCodeIs(200);
    $I->canSee("This action cannot be undone");

    $I->amOnPage("/node/$id/edit");
    $I->fillField("#edit-title-0-value", "My new title");
    $I->click('Save');

    $I->canSee("My new title");

    // Can see revisions.
    $I->amOnPage("/node/$id/revisions");
    $I->canSee("Current revision");
  }

  /**
   * Test thing.
   */
  public function testSiteManagerPerms(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');

    // Can create a node.
    $I->amOnPage("/node/add/stanford_event_series");
    $I->canSeeResponseCodeIs(200);

    // Can delete a node that is not theirs and can edit.
    $node = $this->createEventNode($I);
    $id = $node->id();

    $I->amOnPage("/node/$id/delete");
    $I->canSeeResponseCodeIs(200);
    $I->canSee("This action cannot be undone");

    $I->amOnPage("/node/$id/edit");
    $I->fillField("#edit-title-0-value", "My new title");
    $I->click('Save');
    $I->canSee("My new title");

    // Can see revisions.
    $I->amOnPage("/node/$id/revisions");
    $I->canSee("Current revision");
  }

  /**
   * Creates an event series node.
   *
   * @depends EnableModule
   */
  protected function createEventSeriesNode(AcceptanceTester $I, $node_title = NULL) {
    $event_nodes = [];
    for($i = 0; $i <= 5; $i++) {
      $node = $this->createEventNode($I, "Series Event Node: $i", $i);
      $event_nodes[] = ['target_id' => $node->id()];
    }

    return $I->createEntity([
      'type' => 'stanford_event_series',
      'title' => $node_title ?: 'This is a test event series node',
      'su_event_series_dek' => "This is a dek",
      'su_event_series_event' => $event_nodes,
      'su_event_series_subheadline' => "This is a subheadline",
    ]);
  }

  /**
   * [protected description]
   * @var [type]
   */
  protected function createEventNode(AcceptanceTester $I, $node_title = null, $time_multiplier = 1) {
    $start = time() - (60 * 60 * 24 * $time_multiplier);
    $end = time() + (60 * 60 * 24 * $time_multiplier);

    return $I->createEntity([
      'type' => 'stanford_event',
      'title' => $node_title ?: 'This is a test event node',
      'body' => [
        "value" => "<p>More updates to come.</p>",
        "summary" => "",
      ],
      'su_event_date_time' => [
        'value' => $start,
        'end_value' => $end,
        'duration' => floor(($start - $end) / 60),
        'timezone' => "America/Los_Angeles",
      ],
      'su_event_dek' => 'This is a dek field',
      'su_event_sponsor' => [
        'This is a sponsor',
      ],
      'su_event_subheadline' => 'This is a sub-headline',
    ]);
  }

}
