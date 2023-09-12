<?php

use Faker\Factory;

/**
 * Test the event series functionality.
 *
 * @group content
 */
class EventsSeriesCest {

  /**
   * Faker.
   *
   * @var \Faker\Generator
   */
  protected $faker;

  /**
   * Test Constructor.
   */
  public function __construct() {
    $this->faker = Factory::create();
  }

  /**
   * Ensure events are in the sitemap.
   */
  public function testXMLSiteMap(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/config/search/xmlsitemap/settings/node/stanford_event_series');
    $I->seeOptionIsSelected('#edit-xmlsitemap-status', 'Included');
    $I->seeOptionIsSelected('#edit-xmlsitemap-priority', '0.5 (normal)');
  }

  /**
   * Test Page Title Conditions.
   */
  public function testPageTitleIgnoreCondition(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    // Todo: make theme name dynamic.
    $I->amOnPage('/admin/structure/block/manage/stanford_basic_pagetitle');
    $values = $I->grabTextFrom('#edit-visibility-request-path-pages');
    if (is_string($values)) {
      $values = explode("\n", $values);
    }
    $I->assertContains('/event-series*', $values);
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

    // D8CORE-4551: Can NOT create a node.
    $I->amOnPage('/node/add/stanford_event_series');
    $I->canSeeResponseCodeIs(403);

    // Can not delete a node that is not theirs but can edit.
    $node = $this->createEventSeriesNode($I);
    $id = $node->id();
    $I->amOnPage("/node/$id/edit");
    $I->dontSeeLink('delete');
    $new_title = $this->faker->words(3, TRUE);
    $I->fillField('Title', $new_title);
    $I->click('Save');
    $I->canSee($new_title, 'h1');

    // Can see revisions.
    $I->amOnPage("/node/$id/revisions");
    $I->canSee('Current revision');
  }

  /**
   * Test thing.
   */
  public function testEditorPerms(AcceptanceTester $I) {
    $I->logInWithRole('site_editor');

    // Can create a node.
    $I->amOnPage('/node/add/stanford_event_series');
    $I->canSeeResponseCodeIs(200);

    // Can delete a node that is not theirs and can edit.
    $node = $this->createEventSeriesNode($I);
    $id = $node->id();

    $I->amOnPage("/node/$id/delete");
    $I->canSeeResponseCodeIs(200);
    $I->canSee('This action cannot be undone');

    $I->amOnPage("/node/$id/edit");
    $new_title = $this->faker->words(3, TRUE);
    $I->fillField('Title', $new_title);
    $I->click('Save');

    $I->canSee($new_title, 'h1');

    // Can see revisions.
    $I->amOnPage("/node/$id/revisions");
    $I->canSee('Current revision');
  }

  /**
   * Test thing.
   */
  public function testSiteManagerPerms(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');

    // Can create a node.
    $I->amOnPage('/node/add/stanford_event_series');
    $I->canSeeResponseCodeIs(200);

    // Can delete a node that is not theirs and can edit.
    $node = $this->createEventSeriesNode($I);
    $id = $node->id();

    $I->amOnPage("/node/$id/delete");
    $I->canSeeResponseCodeIs(200);
    $I->canSee('This action cannot be undone');

    $I->amOnPage("/node/$id/edit");
    $new_title = $this->faker->words(3, TRUE);
    $I->fillField('Title', $new_title);
    $I->click('Save');
    $I->canSee($new_title, 'h1');

    // Can see revisions.
    $I->amOnPage("/node/$id/revisions");
    $I->canSee('Current revision');
  }

  /**
   * Creates an event series node.
   *
   * @depends EnableModule
   */
  protected function createEventSeriesNode(AcceptanceTester $I) {
    $event_nodes = [];
    for ($i = 0; $i <= 5; $i++) {
      $node = $this->createEventNode($I);
      $event_nodes[] = ['target_id' => $node->id()];
    }

    return $I->createEntity([
      'type' => 'stanford_event_series',
      'title' => $this->faker->words(4, TRUE),
      'su_event_series_dek' => 'This is a dek',
      'su_event_series_event' => $event_nodes,
      'su_event_series_subheadline' => 'This is a subheadline',
    ]);
  }

  /**
   * [protected description]
   *
   * @var [type]
   */
  protected function createEventNode(AcceptanceTester $I) {
    $start = time() - (60 * 60 * 24);
    $end = time() + (60 * 60 * 24);

    return $I->createEntity([
      'type' => 'stanford_event',
      'title' => $this->faker->word(4, TRUE),
      'body' => [
        'value' => '<p>More updates to come.</p>',
        'summary' => '',
      ],
      'su_event_date_time' => [
        'value' => $start,
        'end_value' => $end,
        'duration' => floor(($start - $end) / 60),
        'timezone' => 'America/Los_Angeles',
      ],
      'su_event_dek' => 'This is a dek field',
      'su_event_sponsor' => [
        'This is a sponsor',
      ],
      'su_event_subheadline' => 'This is a sub-headline',
    ]);
  }

}
