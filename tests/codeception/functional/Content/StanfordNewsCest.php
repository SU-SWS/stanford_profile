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
    $I->waitForElementVisible('.field--name-su-news-topics [data-shs-delta="0"] select');
    $I->selectOption('.field--name-su-news-topics [data-shs-delta="0"] select', $first_term->label());
    $I->click('Add another item', '.field--name-su-news-topics');
    $I->waitForElementVisible('.field--name-su-news-topics [data-shs-delta="1"] select');
    $I->selectOption('.field--name-su-news-topics [data-shs-delta="1"] select', $second_term->label());
    $I->click('Add another item', '.field--name-su-news-topics');
    $I->waitForElementVisible('.field--name-su-news-topics [data-shs-delta="2"] select');
    $I->selectOption('.field--name-su-news-topics [data-shs-delta="2"] select', $third_term->label());

    $I->click('Save');
    $I->canSee($node->label(), 'h1');
    $I->canSee($first_term->label() . ', ' . $second_term->label() . ', '. $third_term->label());

    $I->amOnPage($node->toUrl('edit-form')->toString());
    $I->waitForElementVisible('.field--name-su-news-topics [data-shs-delta="2"] select');
    $I->selectOption('.field--name-su-news-topics [data-shs-delta="0"] select', $second_term->label());
    $I->selectOption('.field--name-su-news-topics [data-shs-delta="1"] select', $first_term->label());
    $I->selectOption('.field--name-su-news-topics [data-shs-delta="2"] select', $third_term->label());

    $I->click('Save');
    $I->canSee($node->label(), 'h1');
    $I->canSee($second_term->label() . ', ' . $first_term->label() . ', '. $third_term->label());

    $I->amOnPage($node->toUrl('edit-form')->toString());
    $I->waitForElementVisible('.field--name-su-news-topics [data-shs-delta="2"] select');
    $I->selectOption('.field--name-su-news-topics [data-shs-delta="0"] select', $third_term->label());
    $I->selectOption('.field--name-su-news-topics [data-shs-delta="1"] select', $second_term->label());
    $I->selectOption('.field--name-su-news-topics [data-shs-delta="2"] select', $first_term->label());

    $I->click('Save');
    $I->canSee($node->label(), 'h1');
    $I->canSee($third_term->label() . ', ' . $second_term->label() . ', '. $first_term->label());

    $I->amOnPage($node->toUrl('edit-form')->toString());
    $I->waitForElementVisible('.field--name-su-news-topics [data-shs-delta="2"] select');
    $I->selectOption('.field--name-su-news-topics [data-shs-delta="0"] select', $third_term->label());
    $I->selectOption('.field--name-su-news-topics [data-shs-delta="1"] select', $first_term->label());
    $I->selectOption('.field--name-su-news-topics [data-shs-delta="2"] select', $second_term->label());

    $I->click('Save');
    $I->canSee($node->label(), 'h1');
    $I->canSee($third_term->label() . ', ' . $first_term->label() . ', '. $second_term->label());
  }

}
