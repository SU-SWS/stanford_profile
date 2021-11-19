<?php

use Faker\Factory;

/**
 * Class ListsCest.
 */
class ListsCest {

  /**
   * Allow all paragraph types by using state.
   */
  public function _before() {
    \Drupal::state()->set('stanford_profile_allow_all_paragraphs', TRUE);
  }

  /**
   * News items should display in the list paragraph.
   */
  public function testListParagraphNews(AcceptanceTester $I) {
    $I->logInWithRole('contributor');
    $I->amOnPage('/node/add/stanford_news');
    $I->fillField('Headline', 'Foo Bar News');
    $I->click('Save');

    $node = $this->getNodeWithList($I, [
      'target_id' => 'stanford_news',
      'display_id' => 'vertical_teaser_term',
      'items_to_display' => 100,
    ]);

    $I->amOnPage($node->toUrl()->toString());
    $I->canSee('Headliner');
    $I->canSee('Lorem Ipsum');
    $I->canSeeLink('Google', 'http://google.com');
    $I->canSee('Foo Bar News');
  }

  /**
   * When using the list paragraph and view arguments, it should filter results.
   */
  public function testListParagraphNewsFiltersNoFilter(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $faker = Factory::create();

    $topic_term = $this->createTaxonomyTerm($I, 'stanford_news_topics');

    $news = $I->createEntity([
      'type' => 'stanford_news',
      'title' => $faker->text(15),
      'su_news_topics' => $topic_term->id(),
      'su_news_publishing_date' => date('Y-m-d', time()),
    ]);

    $I->amOnPage("/node/{$news->id()}/edit");
    $I->click('Save');

    $node = $this->getNodeWithList($I, [
      'target_id' => 'stanford_news',
      'display_id' => 'vertical_teaser_term',
      'items_to_display' => 100,
    ]);

    $I->amOnPage($node->toUrl()->toString());
    $I->canSee($news->label());
  }

  /**
   * When using the list paragraph and view arguments, it should filter results.
   */
  public function testListParagraphNewsFiltersRandomFilter(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $faker = Factory::create();

    $random_term = $this->createTaxonomyTerm($I, 'stanford_news_topics');
    $topic_term = $this->createTaxonomyTerm($I, 'stanford_news_topics');

    $news = $I->createEntity([
      'type' => 'stanford_news',
      'title' => $faker->text(15),
      'su_news_topics' => $topic_term->id(),
      'su_news_publishing_date' => date('Y-m-d', time()),
    ]);

    $I->amOnPage("/node/{$news->id()}/edit");
    $I->click('Save');

    $node = $this->getNodeWithList($I, [
      'target_id' => 'stanford_news',
      'display_id' => 'vertical_teaser_term',
      'items_to_display' => 100,
      'arguments' => $random_term->label(),
    ]);


    $I->amOnPage($node->toUrl()->toString());
    $I->cantSee($news->label());
  }

  /**
   * When using the list paragraph and view arguments, it should filter results.
   */
  public function testListParagraphNewsFiltersTopicFilter(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $faker = Factory::create();

    $topic_term = $this->createTaxonomyTerm($I, 'stanford_news_topics');
    // Use a child term but the argument is the parent term to verify children
    // are included in the results.
    $child_term = $this->createTaxonomyTerm($I, 'stanford_news_topics', NULL, $topic_term->id());

    $news = $I->createEntity([
      'type' => 'stanford_news',
      'title' => $faker->text(15),
      'su_news_topics' => $child_term->id(),
      'su_news_publishing_date' => date('Y-m-d', time()),
    ]);

    $I->amOnPage("/node/{$news->id()}/edit");
    $I->click('Save');

    $node = $this->getNodeWithList($I, [
      'target_id' => 'stanford_news',
      'display_id' => 'vertical_cards',
      'items_to_display' => 100,
      'arguments' => $topic_term->label(),
    ]);

    $I->amOnPage($node->toUrl()->toString());
    $I->canSee($news->label());
  }

  /**
   * Event items should display in the list paragraph.
   */
  public function testListParagraphEvents(AcceptanceTester $I) {
    $I->logInWithRole('contributor');
    $event = $I->createEntity([
      'type' => 'stanford_event',
      'title' => 'Foo Bar Event',
      'su_event_date_time' => [
        'value' => time(),
        'end_value' => time() + 60,
      ],
    ]);
    $I->amOnPage("/node/{$event->id()}/edit");
    $I->click('Save');

    $node = $this->getNodeWithList($I, [
      'target_id' => 'stanford_events',
      'display_id' => 'list_page',
      'items_to_display' => 100,
    ]);

    $I->amOnPage($node->toUrl()->toString());
    $I->canSee('Headliner');
    $I->canSee('Lorem Ipsum');
    $I->canSeeLink('Google', 'http://google.com');
    $I->canSee('Foo Bar Event');
  }

  /**
   * When using the list paragraph and view arguments, it should filter results.
   */
  public function testListParagraphEventFiltersNoFilter(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $faker = Factory::create();

    $event_type = $this->createTaxonomyTerm($I, 'stanford_event_types');
    $event_audience = $this->createTaxonomyTerm($I, 'event_audience');

    $event = $I->createEntity([
      'type' => 'stanford_event',
      'title' => $faker->text(15),
      'su_event_audience' => $event_audience->id(),
      'su_event_type' => $event_type->id(),
      'su_event_date_time' => [
        'value' => time(),
        'end_value' => time() + 60,
      ],
    ]);
    $I->amOnPage("/node/{$event->id()}/edit");
    $I->click('Save');
    $I->canSee('has been updated');

    $node = $this->getNodeWithList($I, [
      'target_id' => 'stanford_events',
      'display_id' => 'list_page',
      'items_to_display' => 100,
    ]);


    $I->amOnPage($node->toUrl()->toString());
    $I->canSee($event->label());
  }

  /**
   * When using the list paragraph and view arguments, it should filter results.
   */
  public function testListParagraphEventFiltersRandomFilter(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $faker = Factory::create();

    $random_term = $this->createTaxonomyTerm($I, 'stanford_event_types');
    $event_type = $this->createTaxonomyTerm($I, 'stanford_event_types');
    $event_audience = $this->createTaxonomyTerm($I, 'event_audience');

    $event = $I->createEntity([
      'type' => 'stanford_event',
      'title' => $faker->text(15),
      'su_event_audience' => $event_audience->id(),
      'su_event_type' => $event_type->id(),
      'su_event_date_time' => [
        'value' => time(),
        'end_value' => time() + 60,
      ],
    ]);
    $I->amOnPage("/node/{$event->id()}/edit");
    $I->click('Save');
    $I->canSee('has been updated');

    $node = $this->getNodeWithList($I, [
      'target_id' => 'stanford_events',
      'display_id' => 'list_page',
      'items_to_display' => 100,
      'arguments' => $random_term->label(),
    ]);


    $I->amOnPage($node->toUrl()->toString());
    $I->cantSee($event->label());
  }

  /**
   * When using the list paragraph and view arguments, it should filter results.
   */
  public function testListParagraphEventFiltersTypeFilter(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $faker = Factory::create();

    $event_type = $this->createTaxonomyTerm($I, 'stanford_event_types');
    // Use a child term but the argument is the parent term to verify children
    // are included in the results.
    $child_type = $this->createTaxonomyTerm($I, 'stanford_event_types', null, $event_type->id());
    $event_audience = $this->createTaxonomyTerm($I, 'event_audience');

    $event = $I->createEntity([
      'type' => 'stanford_event',
      'title' => $faker->text(15),
      'su_event_audience' => $event_audience->id(),
      'su_event_type' => $child_type->id(),
      'su_event_date_time' => [
        'value' => time(),
        'end_value' => time() + 60,
      ],
    ]);
    $I->amOnPage("/node/{$event->id()}/edit");
    $I->click('Save');
    $I->canSee('has been updated');

    $node = $this->getNodeWithList($I, [
      'target_id' => 'stanford_events',
      'display_id' => 'list_page',
      'items_to_display' => 100,
      'arguments' => $event_type->label(),
    ]);

    $I->amOnPage($node->toUrl()->toString());
    $I->canSee($event->label());
  }

  /**
   * When using the list paragraph and view arguments, it should filter results.
   */
  public function testListParagraphEventFiltersAudienceFilter(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $faker = Factory::create();

    $event_type = $this->createTaxonomyTerm($I, 'stanford_event_types');
    $event_audience = $this->createTaxonomyTerm($I, 'event_audience');
    // Use a child term but the argument is the parent term to verify children
    // are included in the results.
    $child_audience = $this->createTaxonomyTerm($I, 'event_audience', NULL, $event_audience->id());

    $event = $I->createEntity([
      'type' => 'stanford_event',
      'title' => $faker->text(15),
      'su_event_audience' => $child_audience->id(),
      'su_event_type' => $event_type->id(),
      'su_event_date_time' => [
        'value' => time(),
        'end_value' => time() + 60,
      ],
    ]);
    $I->amOnPage("/node/{$event->id()}/edit");
    $I->click('Save');
    $I->canSee('has been updated');

    $node = $this->getNodeWithList($I, [
      'target_id' => 'stanford_events',
      'display_id' => 'list_page',
      'items_to_display' => 100,
      'arguments' => $event_audience->label(),
    ]);

    $I->amOnPage($node->toUrl()->toString());
    $I->canSee($event->label());
  }

  /**
   * People items should display in the list paragraph.
   */
  public function testListParagraphPeople(AcceptanceTester $I) {
    $I->logInWithRole('contributor');
    $I->amOnPage('/node/add/stanford_person');
    $I->fillField('First Name', 'Foo');
    $I->fillField('Last Name', 'Bar Person');
    $I->fillField('Short Title', 'Short title field');
    $I->click('Save');

    $node = $this->getNodeWithList($I, [
      'target_id' => 'stanford_person',
      'display_id' => 'grid_list_all',
      'items_to_display' => 100,
    ]);

    $I->amOnPage($node->toUrl()->toString());
    $I->canSee('Headliner');
    $I->canSee('Lorem Ipsum');
    $I->canSeeLink('Google', 'http://google.com');
    $I->canSee('Foo Bar Person');
  }

  /**
   * When using the list paragraph and view arguments, it should filter results.
   */
  public function testListParagraphPeopleFilters(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $faker = Factory::create();

    $type_term = $this->createTaxonomyTerm($I, 'stanford_person_types');

    $news = $I->createEntity([
      'type' => 'stanford_person',
      'su_person_first_name' => $faker->text(15),
      'su_person_last_name' => $faker->text(15),
      'su_person_type_group' => $type_term->id(),
    ]);

    $I->amOnPage("/node/{$news->id()}/edit");
    $I->click('Save');

    $node = $this->getNodeWithList($I, [
      'target_id' => 'stanford_person',
      'display_id' => 'grid_list_all',
      'items_to_display' => 100,
    ]);


    $I->amOnPage($node->toUrl()->toString());
    $I->canSee($news->label());
  }

  /**
   * When using the list paragraph and view arguments, it should filter results.
   */
  public function testListParagraphPeopleFiltersRandomFilter(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $faker = Factory::create();

    $random_term = $this->createTaxonomyTerm($I, 'stanford_person_types');
    $type_term = $this->createTaxonomyTerm($I, 'stanford_person_types');

    $news = $I->createEntity([
      'type' => 'stanford_person',
      'su_person_first_name' => $faker->text(15),
      'su_person_last_name' => $faker->text(15),
      'su_person_type_group' => $type_term->id(),
    ]);

    $I->amOnPage("/node/{$news->id()}/edit");
    $I->click('Save');

    $node = $this->getNodeWithList($I, [
      'target_id' => 'stanford_person',
      'display_id' => 'grid_list_all',
      'items_to_display' => 100,
      'arguments' => $random_term->label(),
    ]);


    $I->amOnPage($node->toUrl()->toString());
    $I->cantSee($news->label());
  }

  /**
   * When using the list paragraph and view arguments, it should filter results.
   */
  public function testListParagraphPeopleFiltersTypeFilter(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $faker = Factory::create();

    $type_term = $this->createTaxonomyTerm($I, 'stanford_person_types');
    // Use a child term but the argument is the parent term to verify children
    // are included in the results.
    $child_type = $this->createTaxonomyTerm($I, 'stanford_person_types', NULL, $type_term->id());

    $news = $I->createEntity([
      'type' => 'stanford_person',
      'su_person_first_name' => $faker->text(15),
      'su_person_last_name' => $faker->text(15),
      'su_person_type_group' => $child_type->id(),
    ]);

    $I->amOnPage("/node/{$news->id()}/edit");
    $I->click('Save');

    $node = $this->getNodeWithList($I, [
      'target_id' => 'stanford_person',
      'display_id' => 'grid_list_all',
      'items_to_display' => 100,
      'arguments' => $type_term->label(),
    ]);


    $I->amOnPage($node->toUrl()->toString());
    $I->canSee($news->label());
  }

  /**
   * Test basic page types list view
   */
  public function testListParagraphBasicPageTypesFilter(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $faker = Factory::create();

    $type_term = $this->createTaxonomyTerm($I, 'basic_page_types', 'Basic Page Test Term');

    $basic_page_entity = $I->createEntity([
      'type' => 'stanford_page',
      'title' => $faker->text(15),
      'su_basic_page_type' => $type_term->id(),
    ]);

    $I->amOnPage("/node/{$basic_page_entity->id()}/edit");
    $I->click('Save');

    $node = $this->getNodeWithList($I, [
      'target_id' => 'stanford_basic_pages',
      'display_id' => 'basic_page_type_list',
      'items_to_display' => 100,
      'arguments' => 'Basic-Page-Test-Term',
    ]);


    $I->amOnPage($node->toUrl()->toString());
    $I->canSee($basic_page_entity->label());
    $I->cantSee($type_term->label());
  }

  /**
   * Get a node with a list paragraph in a row.
   *
   * @param \AcceptanceTester $I
   *   Tester.
   * @param array $view
   *   Keyed field value.
   *
   * @return bool|\Drupal\node\NodeInterface
   */
  protected function getNodeWithList(AcceptanceTester $I, array $view) {
    $faker = Factory::create();

    $paragraph = $I->createEntity([
      'type' => 'stanford_lists',
      'su_list_view' => $view,
      'su_list_headline' => 'Headliner',
      'su_list_description' => [
        'format' => 'stanford_basic_html',
        'value' => '<p>Lorem Ipsum</p>',
      ],
      'su_list_button' => ['uri' => 'http://google.com', 'title' => 'Google'],
    ], 'paragraph');
    $row = $I->createEntity([
      'type' => 'node_stanford_page_row',
      'su_page_components' => [
        'target_id' => $paragraph->id(),
        'entity' => $paragraph,
      ],
    ], 'paragraph_row');

    $node = $I->createEntity([
      'type' => 'stanford_page',
      'title' => $faker->text(30),
      'su_page_components' => [
        'target_id' => $row->id(),
        'entity' => $row,
      ],
    ]);

    return $node;
  }

  /**
   * @param \AcceptanceTester $I
   *   Tester.
   * @param string $vid
   *   Term vocabulary ID.
   * @param string|null $name
   *   Taxonomy name.
   * @param int|null $parent_id
   *
   * @return \Drupal\taxonomy\TermInterface
   *   Generated taxonomy term.
   */
  protected function createTaxonomyTerm(AcceptanceTester $I, string $vid, ?string $name = NULL, ?int $parent_id = null) {
    if (!$name) {
      $name = Factory::create()->text(15);
    }

    $name = trim(preg_replace('/[\W]/', '-', $name), '-');
    return $I->createEntity(['vid' => $vid, 'name' => $name, 'parent' => $parent_id], 'taxonomy_term');
  }

}
