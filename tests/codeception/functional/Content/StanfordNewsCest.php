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

  /**
   * Test that conditional fields work correctly for default and spotlight variants.
   */
  #[CodeceptionAttribute\Group('news_variant')]
  public function testNewsVariantConditionalFields(FunctionalTester $I) {
    // Create a default news node
    $default_news = $I->createEntity([
      'title' => $this->faker->words(3, TRUE),
      'type' => 'stanford_news',
      'su_news_dek' => $this->faker->sentence(),
      'su_news_byline' => $this->faker->name(),
    ]);

    // Create a spotlight news node with spotlight variant
    $spotlight_news = $I->createEntity([
      'title' => $this->faker->words(2, TRUE),
      'type' => 'stanford_news',
      'layout_selection' => 'news_spotlight',
      'su_news_quote' => $this->faker->sentence(),
    ]);

    $I->logInWithRole('site_manager');

    // Test default news variant
    $I->amOnPage($default_news->toUrl('edit-form')->toString());
    $I->waitForElement('[name="layout_selection"]');
    $I->canSeeInField('Variant', 'News');
    $I->canSeeInField('Headline', $default_news->label());
    $I->canSeeInField('Dek', $default_news->get('su_news_dek')->value);
    $I->canSeeInField('Byline', $default_news->get('su_news_byline')->value);

    // Body, Shared Tags, Social Share Icons, and Related Person should be visible
    $I->canSee('Body');
    $I->canSee('Shared Tags');
    $I->canSee('Hide Social Share Icons');
    $I->canSee('Related Person');

    // Test spotlight news variant
    $I->amOnPage($spotlight_news->toUrl('edit-form')->toString());
    $I->waitForElement('[name="layout_selection"]');
    $I->canSeeInField('Variant', 'Spotlight');
    $I->canSeeInField('Headline', $spotlight_news->label());
    $I->canSeeInField('Quote / Big Text', $spotlight_news->get('su_news_quote')->value);

    // Body, Shared Tags, Social Share Icons, and Related Person should be hidden
    $I->cantSee('Body');
    $I->cantSee('Shared Tags');
    $I->cantSee('Hide Social Share Icons');
    $I->cantSee('Related Person');
    // Verify spotlight-specific fields are visible
    $I->canSee('Quote / Big Text');
    $I->canSee('Spotlight Filters');
  }

}
