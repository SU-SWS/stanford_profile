<?php

use Faker\Factory;

/**
 * Test the news functionality.
 */
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
   *
   * @group D8CORE-6003
   */
  public function testTermOrder(FunctionalTester $I) {
    $first_term = $I->createEntity([
      'name' => 'c-' . $this->faker->word,
      'vid' => 'stanford_news_topics',
    ], 'taxonomy_term');
    $second_term = $I->createEntity([
      'name' => 'b-' . $this->faker->word,
      'vid' => 'stanford_news_topics',
    ], 'taxonomy_term');
    $third_term = $I->createEntity([
      'name' => 'a-' . $this->faker->word,
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

}
