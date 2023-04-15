<?php

use Faker\Factory;

/**
 * Class ListsCest.
 *
 * @group paragraphs
 */
class ListsCest {

  /**
   * Faker service.
   *
   * @var \Faker\Generator
   */
  protected $faker;

  /**
   * Test constructor.
   */
  public function __construct() {
    $this->faker = Factory::create();
  }

  /**
   * Shared tags on each content type are identical.
   */
  public function testSharedTags(AcceptanceTester $I) {
    $shared_tag = $I->createEntity([
      'name' => $this->faker->jobTitle,
      'vid' => 'su_shared_tags',
    ], 'taxonomy_term');
    $basic_page = $I->createEntity([
      'title' => $this->faker->text(20),
      'type' => 'stanford_page',
      'su_shared_tags' => $shared_tag->id(),
    ]);
    $news = $I->createEntity([
      'title' => $this->faker->text(20),
      'type' => 'stanford_news',
      'su_shared_tags' => $shared_tag->id(),
    ]);
    $event = $I->createEntity([
      'title' => $this->faker->text(20),
      'type' => 'stanford_event',
      'su_shared_tags' => $shared_tag->id(),
    ]);
    $person = $I->createEntity([
      'su_person_first_name' => $this->faker->firstName,
      'su_person_last_name' => $this->faker->lastName,
      'type' => 'stanford_person',
      'su_shared_tags' => $shared_tag->id(),
    ]);
    $publication = $I->createEntity([
      'title' => $this->faker->text(20),
      'type' => 'stanford_publication',
      'su_shared_tags' => $shared_tag->id(),
    ]);

    // List with all content types.
    $node_list = $this->getNodeWithList($I, [
      'target_id' => 'stanford_shared_tags',
      'display_id' => 'card_grid',
      'items_to_display' => 100,
      'arguments' => $shared_tag->label(),
    ]);
    $I->amOnPage($node_list->toUrl()->toString());
    $I->canSee($basic_page->label());
    $I->canSee($news->label());
    $I->canSee($event->label());
    $I->canSee($person->label());
    $I->canSee($publication->label());

    // List with only events and news.
    $node_list = $this->getNodeWithList($I, [
      'target_id' => 'stanford_shared_tags',
      'display_id' => 'card_grid',
      'items_to_display' => 100,
      'arguments' => $shared_tag->label() . '/stanford_event+stanford_news',
    ]);
    $I->amOnPage($node_list->toUrl()->toString());
    $I->cantSee($basic_page->label());
    $I->canSee($news->label());
    $I->canSee($event->label());
    $I->cantSee($person->label());
    $I->cantSee($publication->label());

    // List with only people.
    $node_list = $this->getNodeWithList($I, [
      'target_id' => 'stanford_shared_tags',
      'display_id' => 'card_grid',
      'items_to_display' => 100,
      'arguments' => $shared_tag->label() . '/stanford_person',
    ]);
    $I->amOnPage($node_list->toUrl()->toString());
    $I->cantSee($basic_page->label());
    $I->cantSee($news->label());
    $I->cantSee($event->label());
    $I->canSee($person->label());
    $I->cantSee($publication->label());

    $I->logInWithRole('contributor');
    $I->amOnPage($basic_page->toUrl('edit-form')->toString());
    $I->canSeeInField('Shared Tags', $shared_tag->id());
    $I->amOnPage($news->toUrl('edit-form')->toString());
    $I->canSeeInField('Shared Tags', $shared_tag->id());
    $I->amOnPage($event->toUrl('edit-form')->toString());
    $I->canSeeInField('Shared Tags', $shared_tag->id());
    $I->amOnPage($person->toUrl('edit-form')->toString());
    $I->canSeeInField('Shared Tags', $shared_tag->id());
    $I->amOnPage($publication->toUrl('edit-form')->toString());
    $I->canSeeInField('Shared Tags', $shared_tag->id());
  }

  /**
   * News items should display in the list paragraph.
   */
  public function testListParagraphNews(AcceptanceTester $I) {
    $I->logInWithRole('contributor');
    $I->amOnPage('/node/add/stanford_news');
    $title = $this->faker->words(3, TRUE);
    $I->fillField('Headline', $title);
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
    $I->canSee($title);
  }

  /**
   * When using the list paragraph and view arguments, it should filter results.
   */
  public function testListParagraphNewsFiltersNoFilter(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');

    $topic_term = $this->createTaxonomyTerm($I, 'stanford_news_topics');

    $news = $I->createEntity([
      'type' => 'stanford_news',
      'title' => $this->faker->text(15),
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

    $random_term = $this->createTaxonomyTerm($I, 'stanford_news_topics');
    $topic_term = $this->createTaxonomyTerm($I, 'stanford_news_topics');

    $news = $I->createEntity([
      'type' => 'stanford_news',
      'title' => $this->faker->text(15),
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

    $topic_term = $this->createTaxonomyTerm($I, 'stanford_news_topics');
    // Use a child term but the argument is the parent term to verify children
    // are included in the results.
    $child_term = $this->createTaxonomyTerm($I, 'stanford_news_topics', NULL, $topic_term->id());

    $news = $I->createEntity([
      'type' => 'stanford_news',
      'title' => $this->faker->text(15),
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
   * No results message and hiding should work.
   *
   * @group D8CORE-4858
   */
  public function testEmptyResultsListEvents(AcceptanceTester $I) {
    // Start with no events.
    $nodes = \Drupal::entityTypeManager()
      ->getStorage('node')
      ->loadByProperties(['type' => 'stanford_event']);
    foreach ($nodes as $node) {
      $node->delete();
    }
    $message = $this->faker->sentence;
    $headline_text = $this->faker->words(3, TRUE);
    /** @var \Drupal\paragraphs\ParagraphInterface $paragraph */
    $paragraph = $I->createEntity([
      'type' => 'stanford_lists',
      'su_list_view' => [
        'target_id' => 'stanford_events',
        'display_id' => 'list_page',
        'items_to_display' => 100,
      ],
      'su_list_headline' => $headline_text,
      'su_list_description' => [
        'format' => 'stanford_html',
        'value' => '<p>Lorem Ipsum</p>',
      ],
      'su_list_button' => ['uri' => 'http://google.com', 'title' => 'Google'],
    ], 'paragraph');
    //    $row = $I->createEntity([
    //      'type' => 'node_stanford_page_row',
    //      'su_page_components' => [
    //        'target_id' => $paragraph->id(),
    //        'entity' => $paragraph,
    //      ],
    //    ], 'paragraph_row');

    $node = $I->createEntity([
      'type' => 'stanford_page',
      'title' => $this->faker->text(30),
      'su_page_components' => [
        'target_id' => $paragraph->id(),
        'entity' => $paragraph,
      ],
    ]);

    $I->amOnPage($node->toUrl()->toString());
    $I->canSee($node->label(), 'h1');
    $I->canSee($headline_text);
    $I->cantSee($message);


    /** @var \Drupal\paragraphs\ParagraphInterface $paragraph */
    $paragraph = $I->createEntity([
      'type' => 'stanford_lists',
      'su_list_view' => [
        'target_id' => 'stanford_events',
        'display_id' => 'list_page',
        'items_to_display' => 100,
      ],
      'su_list_headline' => $headline_text,
      'su_list_description' => [
        'format' => 'stanford_html',
        'value' => '<p>Lorem Ipsum</p>',
      ],
      'su_list_button' => ['uri' => 'http://google.com', 'title' => 'Google'],
    ], 'paragraph');
    $paragraph->setBehaviorSettings('list_paragraph', ['empty_message' => $message]);
    $paragraph->save();

    $node = $I->createEntity([
      'type' => 'stanford_page',
      'title' => $this->faker->text(30),
      'su_page_components' => [
        'target_id' => $paragraph->id(),
        'entity' => $paragraph,
      ],
    ]);

    $I->amOnPage('/');
    $I->amOnPage($node->toUrl()->toString());
    $I->canSee($headline_text);
    $I->canSee($message);

    /** @var \Drupal\paragraphs\ParagraphInterface $paragraph */
    $paragraph = $I->createEntity([
      'type' => 'stanford_lists',
      'su_list_view' => [
        'target_id' => 'stanford_events',
        'display_id' => 'list_page',
        'items_to_display' => 100,
      ],
      'su_list_headline' => $headline_text,
      'su_list_description' => [
        'format' => 'stanford_html',
        'value' => '<p>Lorem Ipsum</p>',
      ],
      'su_list_button' => ['uri' => 'http://google.com', 'title' => 'Google'],
    ], 'paragraph');
    $paragraph->setBehaviorSettings('list_paragraph', [
      'empty_message' => $message,
      'hide_empty' => TRUE,
    ]);
    $paragraph->save();

    $node = $I->createEntity([
      'type' => 'stanford_page',
      'title' => $this->faker->text(30),
      'su_page_components' => [
        'target_id' => $paragraph->id(),
        'entity' => $paragraph,
      ],
    ]);

    $I->amOnPage('/');
    $I->amOnPage($node->toUrl()->toString());
    $I->cantSee($headline_text);
    $I->cantSee($message);
  }

  /**
   * Event items should display in the list paragraph.
   */
  public function testListParagraphEvents(AcceptanceTester $I) {
    $I->logInWithRole('contributor');

    $type = $I->createEntity([
      'name' => $this->faker->words(3, TRUE),
      'vid' => 'stanford_event_types',
    ], 'taxonomy_term');
    $audience = $I->createEntity([
      'name' => $this->faker->words(3, TRUE),
      'vid' => 'event_audience',
    ], 'taxonomy_term');
    $group = $I->createEntity([
      'name' => $this->faker->words(3, TRUE),
      'vid' => 'stanford_event_groups',
    ], 'taxonomy_term');
    $subject = $I->createEntity([
      'name' => $this->faker->words(3, TRUE),
      'vid' => 'stanford_event_subject',
    ], 'taxonomy_term');
    $keyword = $I->createEntity([
      'name' => $this->faker->words(3, TRUE),
      'vid' => 'stanford_event_keywords',
    ], 'taxonomy_term');

    $event = $I->createEntity([
      'type' => 'stanford_event',
      'title' => $this->faker->words(3, TRUE),
      'su_event_date_time' => [
        'value' => time(),
        'end_value' => time() + 60,
      ],
      'su_event_type' => $type->id(),
      'su_event_audience' => $audience->id(),
      'su_event_groups' => $group->id(),
      'su_event_subject' => $subject->id(),
      'su_event_keywords' => $keyword->id(),
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
    $I->canSee($event->label());

    $node = $this->getNodeWithList($I, [
      'target_id' => 'stanford_events',
      'display_id' => 'list_page',
      'items_to_display' => 100,
      'arguments' => str_replace(' ', '-', $audience->label()),
    ]);
    $I->amOnPage($node->toUrl()->toString());
    $I->canSee($event->label());

    $node = $this->getNodeWithList($I, [
      'target_id' => 'stanford_events',
      'display_id' => 'list_page',
      'items_to_display' => 100,
      'arguments' => $type->label(),
    ]);
    $I->amOnPage($node->toUrl()->toString());
    $I->canSee($event->label());

    $node = $this->getNodeWithList($I, [
      'target_id' => 'stanford_events',
      'display_id' => 'list_page',
      'items_to_display' => 100,
      'arguments' => "''/" . str_replace(' ', '-', $group->label()),
    ]);
    $I->amOnPage($node->toUrl()->toString());
    $I->canSee($event->label());

    $node = $this->getNodeWithList($I, [
      'target_id' => 'stanford_events',
      'display_id' => 'list_page',
      'items_to_display' => 100,
      'arguments' => "''/''/" . str_replace(' ', '-', $subject->label()),
    ]);
    $I->amOnPage($node->toUrl()->toString());
    $I->canSee($event->label());

    $node = $this->getNodeWithList($I, [
      'target_id' => 'stanford_events',
      'display_id' => 'list_page',
      'items_to_display' => 100,
      'arguments' => "''/''/''/" . str_replace(' ', '-', $keyword->label()),
    ]);
    $I->amOnPage($node->toUrl()->toString());
    $I->canSee($event->label());

    $type = $I->createEntity([
      'name' => $this->faker->words(3, TRUE),
      'vid' => 'stanford_event_types',
    ], 'taxonomy_term');
    $node = $this->getNodeWithList($I, [
      'target_id' => 'stanford_events',
      'display_id' => 'list_page',
      'items_to_display' => 100,
      'arguments' => str_replace(' ', '-', $type->label()),
    ]);
    $I->amOnPage($node->toUrl()->toString());
    $I->cantSee($event->label());
  }

  /**
   * When using the list paragraph and view arguments, it should filter results.
   */
  public function testListParagraphEventFiltersNoFilter(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');

    $event_type = $this->createTaxonomyTerm($I, 'stanford_event_types');
    $event_audience = $this->createTaxonomyTerm($I, 'event_audience');

    $event = $I->createEntity([
      'type' => 'stanford_event',
      'title' => $this->faker->text(15),
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

    $random_term = $this->createTaxonomyTerm($I, 'stanford_event_types');
    $event_type = $this->createTaxonomyTerm($I, 'stanford_event_types');
    $event_audience = $this->createTaxonomyTerm($I, 'event_audience');

    $event = $I->createEntity([
      'type' => 'stanford_event',
      'title' => $this->faker->text(15),
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

    $event_type = $this->createTaxonomyTerm($I, 'stanford_event_types');
    // Use a child term but the argument is the parent term to verify children
    // are included in the results.
    $child_type = $this->createTaxonomyTerm($I, 'stanford_event_types', NULL, $event_type->id());
    $event_audience = $this->createTaxonomyTerm($I, 'event_audience');

    $event = $I->createEntity([
      'type' => 'stanford_event',
      'title' => $this->faker->text(15),
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

    $event_type = $this->createTaxonomyTerm($I, 'stanford_event_types');
    $event_audience = $this->createTaxonomyTerm($I, 'event_audience');
    // Use a child term but the argument is the parent term to verify children
    // are included in the results.
    $child_audience = $this->createTaxonomyTerm($I, 'event_audience', NULL, $event_audience->id());

    $event = $I->createEntity([
      'type' => 'stanford_event',
      'title' => $this->faker->text(15),
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

    $type_term = $this->createTaxonomyTerm($I, 'stanford_person_types');

    $news = $I->createEntity([
      'type' => 'stanford_person',
      'su_person_first_name' => $this->faker->text(15),
      'su_person_last_name' => $this->faker->text(15),
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

    $random_term = $this->createTaxonomyTerm($I, 'stanford_person_types');
    $type_term = $this->createTaxonomyTerm($I, 'stanford_person_types');

    $news = $I->createEntity([
      'type' => 'stanford_person',
      'su_person_first_name' => $this->faker->text(15),
      'su_person_last_name' => $this->faker->text(15),
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

    $type_term = $this->createTaxonomyTerm($I, 'stanford_person_types');
    // Use a child term but the argument is the parent term to verify children
    // are included in the results.
    $child_type = $this->createTaxonomyTerm($I, 'stanford_person_types', NULL, $type_term->id());

    $news = $I->createEntity([
      'type' => 'stanford_person',
      'su_person_first_name' => $this->faker->text(15),
      'su_person_last_name' => $this->faker->text(15),
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
   * Test basic page types list view.
   */
  public function testListParagraphBasicPageTypesFilter(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');

    $type_term = $this->createTaxonomyTerm($I, 'basic_page_types', 'Basic Page Test Term');

    $basic_page_entity = $I->createEntity([
      'type' => 'stanford_page',
      'title' => $this->faker->text(15),
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

    $layout_changed_page = $I->createEntity([
      'type' => 'stanford_page',
      'title' => $this->faker->text(15),
      'su_basic_page_type' => $type_term->id(),
      'su_page_description' => $this->faker->text,
      'layout_selection' => 'stanford_basic_page_full',
    ]);
    $I->amOnPage($layout_changed_page->toUrl('edit-form')->toString());
    $I->click('Save');
    $I->amOnPage($node->toUrl()->toString());
    $I->canSee($layout_changed_page->label());
    $I->canSee($layout_changed_page->get('su_page_description')->getString());
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

    $paragraph = $I->createEntity([
      'type' => 'stanford_lists',
      'su_list_view' => $view,
      'su_list_headline' => 'Headliner',
      'su_list_description' => [
        'format' => 'stanford_html',
        'value' => '<p>Lorem Ipsum</p>',
      ],
      'su_list_button' => ['uri' => 'http://google.com', 'title' => 'Google'],
    ], 'paragraph');

    $node = $I->createEntity([
      'type' => 'stanford_page',
      'title' => $this->faker->text(30),
      'su_page_components' => [
        'target_id' => $paragraph->id(),
        'entity' => $paragraph,
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
  protected function createTaxonomyTerm(AcceptanceTester $I, string $vid, ?string $name = NULL, ?int $parent_id = NULL) {
    if (!$name) {
      $name = $this->faker->text(15);
    }

    $name = trim(preg_replace('/[\W]/', '-', $name), '-');
    return $I->createEntity([
      'vid' => $vid,
      'name' => $name,
      'parent' => $parent_id,
    ], 'taxonomy_term');
  }

}
