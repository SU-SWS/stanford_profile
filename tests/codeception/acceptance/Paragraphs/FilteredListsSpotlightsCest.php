<?php

use Codeception\Attribute as CodeceptionAttribute;
use Faker\Factory;

/**
 * Test the filtered lists paragraph with news spotlights functionality.
 */
#[CodeceptionAttribute\Group('paragraphs')]
#[CodeceptionAttribute\Group('news-variant')]
#[CodeceptionAttribute\Group('filtered-lists')]
class FilteredListsSpotlightsCest {

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
   * Test that stanford_news_filtered view is available in filtered lists paragraph.
   */
  public function testNewsFilteredViewAvailable(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');

    // Create a filtered list paragraph.
    $paragraph = $I->createEntity([
      'type' => 'stanford_filtered_lists',
    ], 'paragraph');

    $node = $I->createEntity([
      'type' => 'stanford_page',
      'title' => $this->faker->text(30),
      'su_page_components' => [
        'target_id' => $paragraph->id(),
        'entity' => $paragraph,
      ],
    ]);

    $I->amOnPage("/paragraphs_edit/node/{$node->id()}/paragraphs/{$paragraph->id()}/edit");
    $I->canSee('View');
    $I->seeElement("//label[contains(text(), 'View')]");
    $I->seeElement("//select/option[contains(text(), 'News Spotlights - Filtered')]");
  }

  /**
   * Test news spotlights filtered view displays spotlight news items.
   */
  public function testNewsSpotlightsFilteredViewDisplaysSpotlightNews(AcceptanceTester $I) {
    // Create spotlight filter terms.
    $filter_term = $I->createEntity([
      'vid' => 'stanford_news_spotlight_filters',
      'name' => $this->faker->words(3, TRUE),
    ], 'taxonomy_term');

    // Create a news item with spotlight layout and filter.
    $spotlight_news = $I->createEntity([
      'type' => 'stanford_news',
      'title' => 'Spotlight News ' . $this->faker->words(3, TRUE),
      'layout_selection' => 'news_spotlight',
      'su_news_spotlight_filters' => $filter_term->id(),
    ]);

    // Create a regular news item (should not appear).
    $regular_news = $I->createEntity([
      'type' => 'stanford_news',
      'title' => 'Regular News ' . $this->faker->words(3, TRUE),
    ]);

    // Create filtered list paragraph with stanford_news_filtered view.
    $paragraph = $I->createEntity([
      'type' => 'stanford_filtered_lists',
      'su_filtered_list_view' => [
        'target_id' => 'stanford_news_filtered',
        'display_id' => 'cards',
      ],
    ], 'paragraph');

    $node = $I->createEntity([
      'type' => 'stanford_page',
      'title' => $this->faker->text(30),
      'su_page_components' => [
        'target_id' => $paragraph->id(),
        'entity' => $paragraph,
      ],
    ]);

    $I->amOnPage($node->toUrl()->toString());
    $I->canSee($spotlight_news->label(), 'h3');
    $I->cantSee($regular_news->label());
  }

  /**
   * Test filtering spotlight news by taxonomy term.
   */
  public function testSpotlightNewsFilteringByTerm(AcceptanceTester $I) {
    // Create multiple spotlight filter terms.
    $filter_term_1 = $I->createEntity([
      'vid' => 'stanford_news_spotlight_filters',
      'name' => 'FilterTermOne',
    ], 'taxonomy_term');

    $filter_term_2 = $I->createEntity([
      'vid' => 'stanford_news_spotlight_filters',
      'name' => 'FilterTermTwo',
    ], 'taxonomy_term');

    // Create spotlight news with filter term 1.
    $spotlight_news_1 = $I->createEntity([
      'type' => 'stanford_news',
      'title' => 'Spotlight One ' . $this->faker->words(3, TRUE),
      'layout_selection' => 'news_spotlight',
      'su_news_spotlight_filters' => $filter_term_1->id(),
    ]);

    // Create spotlight news with filter term 2.
    $spotlight_news_2 = $I->createEntity([
      'type' => 'stanford_news',
      'title' => 'Spotlight Two ' . $this->faker->words(3, TRUE),
      'layout_selection' => 'news_spotlight',
      'su_news_spotlight_filters' => $filter_term_2->id(),
    ]);

    // Create filtered list paragraph with argument for term 1.
    $paragraph = $I->createEntity([
      'type' => 'stanford_filtered_lists',
      'su_filtered_list_view' => [
        'target_id' => 'stanford_news_filtered',
        'display_id' => 'cards',
        'arguments' => $filter_term_1->label(),
      ],
    ], 'paragraph');

    $node = $I->createEntity([
      'type' => 'stanford_page',
      'title' => $this->faker->text(30),
      'su_page_components' => [
        'target_id' => $paragraph->id(),
        'entity' => $paragraph,
      ],
    ]);

    // Should show only spotlight news 1.
    $I->amOnPage($node->toUrl()->toString());
    $I->canSee($spotlight_news_1->label());
    $I->cantSee($spotlight_news_2->label());
  }

  /**
   * Test spotlight news with hierarchical filter terms.
   */
  public function testSpotlightNewsHierarchicalFiltering(AcceptanceTester $I) {
    // Create parent and child filter terms.
    $parent_term = $I->createEntity([
      'vid' => 'stanford_news_spotlight_filters',
      'name' => $this->faker->words(2, TRUE),
    ], 'taxonomy_term');

    $child_term = $I->createEntity([
      'vid' => 'stanford_news_spotlight_filters',
      'name' => $this->faker->words(2, TRUE),
      'parent' => $parent_term->id(),
    ], 'taxonomy_term');

    // Create spotlight news with child term.
    $spotlight_news = $I->createEntity([
      'type' => 'stanford_news',
      'title' => 'Child Term Spotlight ' . $this->faker->words(3, TRUE),
      'layout_selection' => 'news_spotlight',
      'su_news_spotlight_filters' => $child_term->id(),
    ]);

    // Create filtered list with parent term argument.
    $paragraph = $I->createEntity([
      'type' => 'stanford_filtered_lists',
      'su_filtered_list_view' => [
        'target_id' => 'stanford_news_filtered',
        'display_id' => 'cards',
        'arguments' => $parent_term->label(),
      ],
    ], 'paragraph');

    $node = $I->createEntity([
      'type' => 'stanford_page',
      'title' => $this->faker->text(30),
      'su_page_components' => [
        'target_id' => $paragraph->id(),
        'entity' => $paragraph,
      ],
    ]);

    // Should show spotlight news with child term when filtering by parent.
    $I->amOnPage($node->toUrl()->toString());
    $I->canSee($spotlight_news->label(), 'h3');
  }

  /**
   * Test exposed filter functionality in card grid display.
   */
  public function testExposedFilterFunctionality(AcceptanceTester $I) {
    // Create filter terms.
    $filter_term_1 = $I->createEntity([
      'vid' => 'stanford_news_spotlight_filters',
      'name' => $this->faker->words(2, TRUE),
    ], 'taxonomy_term');

    $filter_term_2 = $I->createEntity([
      'vid' => 'stanford_news_spotlight_filters',
      'name' => $this->faker->words(2, TRUE),
    ], 'taxonomy_term');

    // Create spotlight news items.
    $spotlight_news_1 = $I->createEntity([
      'type' => 'stanford_news',
      'title' => 'Filter Test One ' . $this->faker->words(2, TRUE),
      'layout_selection' => 'news_spotlight',
      'su_news_spotlight_filters' => $filter_term_1->id(),
    ]);

    $spotlight_news_2 = $I->createEntity([
      'type' => 'stanford_news',
      'title' => 'Filter Test Two ' . $this->faker->words(2, TRUE),
      'layout_selection' => 'news_spotlight',
      'su_news_spotlight_filters' => $filter_term_2->id(),
    ]);

    // Create filtered list paragraph.
    $paragraph = $I->createEntity([
      'type' => 'stanford_filtered_lists',
      'su_filtered_list_view' => [
        'target_id' => 'stanford_news_filtered',
        'display_id' => 'cards',
      ],
    ], 'paragraph');

    $node = $I->createEntity([
      'type' => 'stanford_page',
      'title' => $this->faker->text(30),
      'su_page_components' => [
        'target_id' => $paragraph->id(),
        'entity' => $paragraph,
      ],
    ]);

    $I->amOnPage($node->toUrl()->toString());

    // Both items should be visible initially.
    $I->canSee($spotlight_news_1->label(), 'h3');
    $I->canSee($spotlight_news_2->label(), 'h3');

    // Exposed filter should be present.
    $I->canSee('Filtered by');
  }

  /**
   * Test that only news with spotlight layout are shown.
   */
  public function testOnlySpotlightLayoutNewsDisplayed(AcceptanceTester $I) {
    $filter_term = $I->createEntity([
      'vid' => 'stanford_news_spotlight_filters',
      'name' => $this->faker->words(2, TRUE),
    ], 'taxonomy_term');

    // Create news with spotlight layout.
    $spotlight_news = $I->createEntity([
      'type' => 'stanford_news',
      'title' => 'Has Spotlight Layout ' . $this->faker->words(3, TRUE),
      'layout_selection' => 'news_spotlight',
      'su_news_spotlight_filters' => $filter_term->id(),
    ]);

    // Create news with different layout but same filter.
    $non_spotlight_news = $I->createEntity([
      'type' => 'stanford_news',
      'title' => 'No Spotlight Layout ' . $this->faker->words(3, TRUE),
      'su_news_spotlight_filters' => $filter_term->id(),
    ]);

    // Create filtered list paragraph.
    $paragraph = $I->createEntity([
      'type' => 'stanford_filtered_lists',
      'su_filtered_list_view' => [
        'target_id' => 'stanford_news_filtered',
        'display_id' => 'cards',
      ],
    ], 'paragraph');

    $node = $I->createEntity([
      'type' => 'stanford_page',
      'title' => $this->faker->text(30),
      'su_page_components' => [
        'target_id' => $paragraph->id(),
        'entity' => $paragraph,
      ],
    ]);

    $I->amOnPage($node->toUrl()->toString());

    // Only spotlight layout should appear.
    $I->canSee($spotlight_news->label(), 'h3');
    $I->cantSee($non_spotlight_news->label());
  }

  /**
   * Test list page display.
   */
  public function testListPageDisplay(AcceptanceTester $I) {
    $filter_term = $I->createEntity([
      'vid' => 'stanford_news_spotlight_filters',
      'name' => $this->faker->words(2, TRUE),
    ], 'taxonomy_term');

    $spotlight_news = $I->createEntity([
      'type' => 'stanford_news',
      'title' => 'List Display Test ' . $this->faker->words(3, TRUE),
      'layout_selection' => 'news_spotlight',
      'su_news_spotlight_filters' => $filter_term->id(),
    ]);

    // Create filtered list paragraph with list_page display.
    $paragraph = $I->createEntity([
      'type' => 'stanford_filtered_lists',
      'su_filtered_list_view' => [
        'target_id' => 'stanford_news_filtered',
        'display_id' => 'list_page',
      ],
    ], 'paragraph');

    $node = $I->createEntity([
      'type' => 'stanford_page',
      'title' => $this->faker->text(30),
      'su_page_components' => [
        'target_id' => $paragraph->id(),
        'entity' => $paragraph,
      ],
    ]);

    $I->amOnPage($node->toUrl()->toString());
    $I->canSee($spotlight_news->label(), 'h3');
  }

}
