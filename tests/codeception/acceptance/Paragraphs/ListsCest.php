<?php

use Faker\Factory;

/**
 * Class ListsCest.
 */
class ListsCest {

  /**
   * Allow all paragraph types by using state.
   */
  public function _before(){
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
