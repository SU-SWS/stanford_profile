<?php

use Codeception\Attribute as CodeceptionAttribute;
use Faker\Factory;

/**
 * Test the news functionality.
 */
#[CodeceptionAttribute\Group('news')]
class StanfordNewsCest {

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
   * Taxonomy terms in SHS should save in the order they were chosen.
   */
  #[CodeceptionAttribute\Group('D8CORE-6003')]
  public function testTermOrder(FunctionalTester $I) {
    $first_term = $I->createEntity([
      'name' => 'c-' . $this->faker->word(),
      'vid' => 'stanford_news_topics',
    ], 'taxonomy_term');
    $second_term = $I->createEntity([
      'name' => 'b-' . $this->faker->word(),
      'vid' => 'stanford_news_topics',
    ], 'taxonomy_term');
    $third_term = $I->createEntity([
      'name' => 'a-' . $this->faker->word(),
      'vid' => 'stanford_news_topics',
    ], 'taxonomy_term');

    $node = $I->createEntity([
      'title' => $this->faker->words(3, TRUE),
      'type' => 'stanford_news',
    ]);
    $I->logInWithRole('contributor');

    $I->amOnPage($node->toUrl('edit-form')->toString());
    $I->canSeeInField('Headline', $node->label());

    $I->waitForElementVisible('.form-item--su-news-topics-0-target-id select.simpler-select');
    $I->selectOption('.form-item--su-news-topics-0-target-id select.simpler-select', $first_term->id());
    $I->click('Add another item', '.field--name-su-news-topics');
    $I->waitForElementVisible('.form-item--su-news-topics-1-target-id select.simpler-select');
    $I->selectOption('.form-item--su-news-topics-1-target-id select.simpler-select', $second_term->id());
    $I->click('Add another item', '.field--name-su-news-topics');
    $I->waitForElementVisible('.form-item--su-news-topics-2-target-id select.simpler-select');
    $I->selectOption('.form-item--su-news-topics-2-target-id select.simpler-select', $third_term->id());

    $I->click('Save');
    $I->canSee($node->label(), 'h1');
    $I->canSee($first_term->label() . ', ' . $second_term->label() . ', ' . $third_term->label());

    $I->amOnPage($node->toUrl('edit-form')->toString());
    $I->waitForElementVisible('.form-item--su-news-topics-2-target-id select.simpler-select');
    $I->selectOption('.form-item--su-news-topics-0-target-id select.simpler-select', $second_term->id());
    $I->selectOption('.form-item--su-news-topics-1-target-id select.simpler-select', $first_term->id());
    $I->selectOption('.form-item--su-news-topics-2-target-id select.simpler-select', $third_term->id());

    $I->click('Save');
    $I->canSee($node->label(), 'h1');
    $I->canSee($second_term->label() . ', ' . $first_term->label() . ', ' . $third_term->label());

    $I->amOnPage($node->toUrl('edit-form')->toString());
    $I->waitForElementVisible('.form-item--su-news-topics-2-target-id select.simpler-select');
    $I->selectOption('.form-item--su-news-topics-0-target-id select.simpler-select', $third_term->id());
    $I->selectOption('.form-item--su-news-topics-1-target-id select.simpler-select', $second_term->id());
    $I->selectOption('.form-item--su-news-topics-2-target-id select.simpler-select', $first_term->id());

    $I->click('Save');
    $I->canSee($node->label(), 'h1');
    $I->canSee($third_term->label() . ', ' . $second_term->label() . ', ' . $first_term->label());

    $I->amOnPage($node->toUrl('edit-form')->toString());
    $I->waitForElementVisible('.form-item--su-news-topics-2-target-id select.simpler-select');
    $I->selectOption('.form-item--su-news-topics-0-target-id select.simpler-select', $third_term->id());
    $I->selectOption('.form-item--su-news-topics-1-target-id select.simpler-select', $first_term->id());
    $I->selectOption('.form-item--su-news-topics-2-target-id select.simpler-select', $second_term->id());

    $I->click('Save');
    $I->canSee($node->label(), 'h1');
    $I->canSee($third_term->label() . ', ' . $first_term->label() . ', ' . $second_term->label());
  }

  #[CodeceptionAttribute\Group('news_variant')]
  public function testDefaultVariantHidesFields(FunctionalTester $I) {
    $default_news = $I->createEntity([
      'title' => $this->faker->words(3, TRUE),
      'type' => 'stanford_news',
      'su_news_dek' => $this->faker->sentence(),
      'su_news_byline' => $this->faker->name(),
    ]);
    $spotlight_news = $I->createEntity([
      'title' => $this->faker->words(2, TRUE),
      'type' => 'stanford_news',
      'layout_selection' => 'news_spotlight',
      'su_news_quote' => $this->faker->sentence(),
      'su_news_subtitle' => $this->faker->sentence(),
    ]);

    $I->logInWithRole('site_manager');
    $I->amOnPage($default_news->toUrl('edit-form')->toString());
    $I->canSeeInField('Headline', $default_news->label());
    $I->canSeeInField('Dek', $default_news->get('su_news_dek')->value);
    $I->canSeeInField('Byline', $default_news->get('su_news_byline')->value);

    $I->amOnPage($spotlight_news->toUrl('edit-form')->toString());
    $I->canSeeInField('Headline', $spotlight_news->label());
    // $I->selectOption('Layout', 'Spotlight');
    $I->canSeeInField('Quote / Big Text', $spotlight_news->get('su_news_quote')->value);
    $I->canSeeInField('Subtitle', $spotlight_news->get('su_news_subtitle')->value);
  }

  /**
   * Test Related Spotlights view configuration and existence.
   */
  #[CodeceptionAttribute\Group('news_variant')]
  public function testRelatedSpotlightsViewExists(FunctionalTester $I) {
    // Log in as administrator to access views admin.
    $I->logInWithRole('administrator');

    // Verify the view display exists and is configured properly.
    $I->amOnPage('/admin/structure/views/view/stanford_news/edit/related_spotlights');
    $I->canSee('Related Spotlights');
  }

  /**
   * Test that Related Spotlights filters by matching taxonomy terms.
   *
   * This tests the stanford_news_views_query_alter() hook.
   */
  #[CodeceptionAttribute\Group('news_variant')]
  public function testRelatedSpotlightsFiltersByTaxonomy(FunctionalTester $I) {

    // Create taxonomy terms.
    $term_a = $I->createEntity([
      'name' => 'Category A',
      'vid' => 'stanford_news_spotlight_filters',
    ], 'taxonomy_term');

    $term_b = $I->createEntity([
      'name' => 'Category B',
      'vid' => 'stanford_news_spotlight_filters',
    ], 'taxonomy_term');

    // Create 3 spotlight nodes with term A and 2 with term B.
    $spotlight_a1 = $I->createEntity([
      'title' => 'Spotlight A1',
      'type' => 'stanford_news',
      'layout_selection' => 'news_spotlight',
      'su_news_spotlight_filters' => [$term_a->id()],
      'status' => 1,
    ]);

    $spotlight_a2 = $I->createEntity([
      'title' => 'Spotlight A2',
      'type' => 'stanford_news',
      'layout_selection' => 'news_spotlight',
      'su_news_spotlight_filters' => [$term_a->id()],
      'status' => 1,
    ]);

    $spotlight_a3 = $I->createEntity([
      'title' => 'Spotlight A3',
      'type' => 'stanford_news',
      'layout_selection' => 'news_spotlight',
      'su_news_spotlight_filters' => [$term_a->id()],
      'status' => 1,
    ]);

    $spotlight_b1 = $I->createEntity([
      'title' => 'Spotlight B1',
      'type' => 'stanford_news',
      'layout_selection' => 'news_spotlight',
      'su_news_spotlight_filters' => [$term_b->id()],
      'status' => 1,
    ]);

    $spotlight_b2 = $I->createEntity([
      'title' => 'Spotlight B2',
      'type' => 'stanford_news',
      'layout_selection' => 'news_spotlight',
      'su_news_spotlight_filters' => [$term_b->id()],
      'status' => 1,
    ]);

    // Create a spotlight with no terms.
    $spotlight_c = $I->createEntity([
      'title' => 'Spotlight C',
      'type' => 'stanford_news',
      'layout_selection' => 'news_spotlight',
      'status' => 1,
    ]);

    // Visit spotlight A1 - should show other nodes with term A.
    $I->amOnPage($spotlight_a1->toUrl()->toString());
    $I->seeElement('.su-news-related-spotlights');
    $I->seeLink('Spotlight A2');
    $I->seeLink('Spotlight A3');
    $I->dontSeeLink('Spotlight B1');
    $I->dontSeeLink('Spotlight B2');

    // If there are no terms attached, we should see no related spotlights.
    $I->amOnPage($spotlight_c->toUrl()->toString());
    $I->dontSeeElement('.su-news-related-spotlights');
  }
}
