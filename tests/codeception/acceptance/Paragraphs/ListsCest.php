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
  public function testListParagraphNewsFilters(AcceptanceTester $I) {
    $I->logInWithRole('contributor');
    $faker = Factory::create();

    $random_term = $I->createEntity([
      'name' => $faker->text(10),
      'vid' => 'stanford_news_topics',
    ], 'taxonomy_term');

    $topic_term = $I->createEntity([
      'name' => $faker->text(10),
      'vid' => 'stanford_news_topics',
    ], 'taxonomy_term');

    $news = $I->createEntity([
      'type' => 'stanford_news',
      'su_news_headline' => $faker->text(15),
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

    $node = $this->getNodeWithList($I, [
      'target_id' => 'stanford_news',
      'display_id' => 'vertical_teaser_term',
      'items_to_display' => 100,
      'arguments' => $random_term->label(),
    ]);

    $I->amOnPage($node->toUrl()->toString());
    $I->cantSee($news->label());

    $node = $this->getNodeWithList($I, [
      'target_id' => 'stanford_news',
      'display_id' => 'vertical_teaser_term',
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
  public function testListParagraphEventFilters(AcceptanceTester $I) {
    $I->logInWithRole('contributor');
    $faker = Factory::create();

    $random_term = $I->createEntity([
      'name' => $faker->text(10),
      'vid' => 'stanford_event_types',
    ], 'taxonomy_term');

    $event_type = $I->createEntity([
      'name' => $faker->text(10),
      'vid' => 'stanford_event_types',
    ], 'taxonomy_term');

    $event_audience = $I->createEntity([
      'name' => $faker->text(10),
      'vid' => 'event_audience',
    ], 'taxonomy_term');

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

    $node = $this->getNodeWithList($I, [
      'target_id' => 'stanford_events',
      'display_id' => 'list_page',
      'items_to_display' => 100,
    ]);

    $I->amOnPage($node->toUrl()->toString());
    $I->canSee($event->label());

    $node = $this->getNodeWithList($I, [
      'target_id' => 'stanford_events',
      'display_id' => 'list_page',
      'items_to_display' => 100,
      'arguments' => $random_term->label(),
    ]);

    $I->amOnPage($node->toUrl()->toString());
    $I->cantSee($event->label());

    $node = $this->getNodeWithList($I, [
      'target_id' => 'stanford_events',
      'display_id' => 'list_page',
      'items_to_display' => 100,
      'arguments' => $event_type->label(),
    ]);

    $I->amOnPage($node->toUrl()->toString());
    $I->canSee($event->label());

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
    $I->logInWithRole('contributor');
    $faker = Factory::create();

    $random_term = $I->createEntity([
      'name' => $faker->text(10),
      'vid' => 'stanford_person_types',
    ], 'taxonomy_term');

    $type_term = $I->createEntity([
      'name' => $faker->text(10),
      'vid' => 'stanford_person_types',
    ], 'taxonomy_term');

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

    $node = $this->getNodeWithList($I, [
      'target_id' => 'stanford_person',
      'display_id' => 'grid_list_all',
      'items_to_display' => 100,
      'arguments' => $random_term->label(),
    ]);

    $I->amOnPage($node->toUrl()->toString());
    $I->cantSee($news->label());

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

    return $I->createEntity([
      'type' => 'stanford_page',
      'title' => $faker->text(30),
      'su_page_components' => [
        'target_id' => $row->id(),
        'entity' => $row,
      ],
    ]);
  }

}
