<?php

use Faker\Factory;

/**
 * Test the events + importer functionality.
 */
class EventsCest {

  /**
   * Events list intro block is at the top of the page.
   */
  public function testListIntro(AcceptanceTester $I) {
    $intro_text = Factory::create()->text();
    $I->logInWithRole('site_manager');
    $I->amOnPage('/events');
    $I->click('Edit Block Content Above');
    $I->click('Add Text Area');
    $I->fillField('Body', $intro_text);
    $I->click('Save');
    $I->canSee($intro_text);
  }

  /**
   * Ensure events are in the sitemap.
   */
  public function testXMLSiteMap(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/config/search/xmlsitemap/settings/node/stanford_event');
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
    $I->assertContains("/events*", $values);
  }

  /**
   * Test the the event content type exists and has at least a couple of fields.
   */
  public function testContentTypeExists(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/structure/types/manage/stanford_event/fields');
    $I->canSee('body');
    $I->canSee('su_event_date_time');
  }

  /**
   * Test Access to stuff for contrib role.
   */
  public function testContributorPerms(AcceptanceTester $I) {
    $I->logInWithRole('contributor');

    // Can create a node.
    $I->amOnPage("/node/add/stanford_event");
    $I->canSeeResponseCodeIs(200);

    // Can not delete a node that is not theirs but can edit.
    $node = $this->createEventNode($I);
    $id = $node->id();
    $I->amOnPage("/node/$id/edit");
    $I->dontSee("#edit-delete");
    $I->fillField("#edit-title-0-value", "My new title");
    $I->click('Save');
    $I->canSee("My new title");

    // Can see revisions.
    $I->amOnPage("/node/$id/revisions");
    $I->canSee("Current revision");

    // Can't adjust taxonomy terms.
    $I->amOnPage("/admin/structure/taxonomy/manage/event_audience/overview");
    $I->dontSeeResponseCodeIs(200);

    $I->amOnPage("/admin/structure/taxonomy/manage/stanford_event_types/overview");
    $I->dontSeeResponseCodeIs(200);

    // Can't adjust menu items.
    $I->amOnPage("/admin/structure/menu/manage/stanford-event-types");
    $I->dontSeeResponseCodeIs(200);

    // Can't adjust the importer form.
    $I->amOnPage("/admin/config/importers/events-importer");
    $I->dontSeeResponseCodeIs(200);
  }

  /**
   * Test thing.
   */
  public function testEditorPerms(AcceptanceTester $I) {
    $I->logInWithRole('site_editor');

    // Can create a node.
    $I->amOnPage("/node/add/stanford_event");
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

    // Can adjust taxonomy terms.
    $I->amOnPage("/admin/structure/taxonomy/manage/event_audience/overview");
    $I->seeResponseCodeIs(200);

    $I->amOnPage("/admin/structure/taxonomy/manage/stanford_event_types/overview");
    $I->seeResponseCodeIs(200);

    // Can't adjust menu items.
    $I->amOnPage("/admin/structure/menu/manage/stanford-event-types");
    $I->seeResponseCodeIs(200);

    // Can't adjust the importer form.
    $I->amOnPage("/admin/config/importers/events-importer");
    $I->dontSeeResponseCodeIs(200);
  }

  /**
   * Test thing.
   */
  public function testSiteManagerPerms(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');

    // Can create a node.
    $I->amOnPage("/node/add/stanford_event");
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

    // Can adjust taxonomy terms.
    $I->amOnPage("/admin/structure/taxonomy/manage/event_audience/overview");
    $I->seeResponseCodeIs(200);

    $I->amOnPage("/admin/structure/taxonomy/manage/stanford_event_types/overview");
    $I->seeResponseCodeIs(200);

    // Can adjust menu items.
    $I->amOnPage("/admin/structure/menu/manage/stanford-event-types");
    $I->seeResponseCodeIs(200);

    // Can adjust the importer form.
    $I->amOnPage("/admin/config/importers/events-importer");
    $I->seeResponseCodeIs(200);
  }

  /**
   * Test to make sure the main menu link is there.
   */
  public function testDefaultContentExists(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    // Events Main Menu Link.
    $I->amOnPage("/admin/structure/menu/manage/main");
    $I->canSee("Events");
  }

  /**
   * Published checkbox should be hidden on term edit pages.
   */
  public function testTermPublishing(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $term = $I->createEntity([
      'vid' => 'event_audience',
      'name' => 'Foo',
    ], 'taxonomy_term');
    $I->amOnPage($term->toUrl('edit-form')->toString());
    $I->cantSee('Published');

    $term = $I->createEntity([
      'vid' => 'stanford_event_types',
      'name' => 'Foo',
    ], 'taxonomy_term');
    $I->amOnPage($term->toUrl('edit-form')->toString());
    $I->cantSee('Published');
  }

  /**
   * Clone events get incremented date.
   */
  public function testClone(AcceptanceTester $I) {
    $user = $I->createUserWithRoles(['contributor']);
    /** @var \Drupal\node\NodeInterface $node */
    $node = $this->createEventNode($I);
    $node->set('uid', $user->id())->save();
    $original_date_time = (int) $node->get('su_event_date_time')[0]->get('value')
      ->getString();
    $I->logInAs($user->getAccountName());
    $I->amOnPage('/admin/content');

    $I->checkOption('[name="views_bulk_operations_bulk_form[0]"]');
    $I->selectOption('Action', 'Clone selected content');
    $I->click('Apply to selected items');
    $I->selectOption('Clone how many times', 2);
    $I->selectOption('Increment Amount', '3');
    $I->selectOption('Units', 'Month');
    $I->click('Apply');

    $node_storage = \Drupal::entityTypeManager()->getStorage('node');
    $nids = $node_storage->getQuery()
      ->condition('type', 'stanford_event')
      ->sort('nid', 'DESC')
      ->range(0, 1)
      ->accessCheck(FALSE)
      ->execute();
    $cloned_node = $node_storage->load(reset($nids));
    $cloned_date_time = $cloned_node->get('su_event_date_time')[0]->get('value')
      ->getString();

    $I->assertNotEquals($cloned_date_time, $original_date_time);
    $diff = $cloned_date_time - $original_date_time;
    $I->assertEquals(6, round($diff / (60 * 60 * 24 * 30.5)));
  }

  /**
   * Create an Event Node.
   *
   * @param AcceptanceTester $I
   *   Codeception AcceptanceTester
   * @param bool $external
   *   Wether or not this node should be external.
   * @param string $node_title
   *   A title string to call the new node.
   *
   * @return object
   *   Node Object
   */
  protected function createEventNode(AcceptanceTester $I, $external = FALSE, $node_title = NULL) {
    return $I->createEntity([
      'type' => 'stanford_event',
      'title' => $node_title ?: 'This is a headline',
      'body' => [
        "value" => "<p>More updates to come.</p>",
        "summary" => "",
      ],
      'su_event_cta' => [
        "uri" => "https://google.com/",
        "title" => "This is cta link text",
      ],
      'su_event_email' => 'noreply@stanford.edu',
      'su_event_telephone' => '555-555-5645',
      'su_event_date_time' => [
        'value' => time(),
        'end_value' => time() + (60 * 60 * 24),
        'duration' => (60 * 24),
        'timezone' => "America/Los_Angeles",
      ],
      'su_event_dek' => 'This is a dek field',
      'su_event_alt_loc' => $external ? "https://events.stanford.edu/" : "",
      'su_event_source' => $external ? [
        "uri" => "http://events.stanford.edu/events/880/88074",
        "title" => "",
      ] : "",
      'su_event_location' => $external ?: [
        "langcode" => "",
        "country_code" => "US",
        "administrative_area" => "CA",
        "locality" => "San Francisco",
        "postal_code" => "94123-2806",
        "address_line1" => "1901 Lombard St",
        "address_line2" => "",
        "organization" => "Asfdasdfa sdfasd fasf",
      ],
      'su_event_map_link' => [
        'uri' => 'https://stanford.edu/',
        'title' => 'map link',
      ],
      'su_event_sponsor' => [
        'This is a sponsor',
        'This is two sponsor',
        'This is 3 sponsor',
      ],
      'su_event_subheadline' => 'This is a sub-headline',
    ]);
  }

  /**
   * Creates a new event type term.
   *
   * @param AcceptanceTester $I
   *   Codeception AcceptanceTester
   * @param string $name
   *   The name of the term.
   *
   * @return object
   *   A taxonomy term.
   */
  protected function createEventTypeTerm(AcceptanceTester $I, $name = NULL) {
    return $I->createEntity([
      'name' => $name ?: 'Foo',
      'vid' => 'stanford_event_types',
    ], 'taxonomy_term');
  }

  /**
   * Creates a new event audience term.
   *
   * @param AcceptanceTester $I
   *   Codeception AcceptanceTester
   * @param string $name
   *   The name of the term.
   *
   * @return object
   *   A taxonomy term.
   */
  protected function createEventAudienceTerm(AcceptanceTester $I, $name = NULL) {
    return $I->createEntity([
      'name' => $name ?: 'Foo',
      'vid' => 'stanford_event_audience',
    ], 'taxonomy_term');
  }

}
