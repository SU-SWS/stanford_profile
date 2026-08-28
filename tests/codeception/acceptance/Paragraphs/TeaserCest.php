<?php

use Codeception\Attribute as CodeceptionAttribute;
use Faker\Factory;

#[CodeceptionAttribute\Group('paragraphs')]
class TeaserCest {

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

  #[CodeceptionAttribute\Group('teaser-headers')]
  public function testTeaserParagraphHeaders(AcceptanceTester $I) {
    $node_types = \Drupal::entityTypeManager()
      ->getStorage('node_type')
      ->loadMultiple();
    $teaser_entities = [];
    $teaser_item_field = [];
    foreach ($node_types as $node_type) {
      $title_key = $node_type->id() == 'stanford_policy' ? 'su_policy_title' : 'title';
      $teaser_entities[$node_type->id()] = $I->createEntity([
        $title_key => $this->faker->words(3, TRUE),
        'type' => $node_type->id(),
      ]);
      $teaser_item_field[]['target_id'] = $teaser_entities[$node_type->id()]->id();
    }

    $paragraph = $I->createEntity([
      'type' => 'stanford_entity',
      'su_entity_item' => $teaser_item_field,
    ], 'paragraph');
    $node = $I->createEntity([
      'title' => $this->faker->words(3, TRUE),
      'type' => 'stanford_page',
      'su_page_components' => [
        'target_id' => $paragraph->id(),
        'entity' => $paragraph,
      ],
    ]);
    $I->amOnPage($node->toUrl()->toString());
    $I->canSee($node->label(), 'h1');
    foreach ($teaser_entities as $entity) {
      $I->canSee($entity->label(), '.su-entity-item h2');
    }

    $header_text = $this->faker->words(3, TRUE);
    $paragraph = $I->createEntity([
      'type' => 'stanford_entity',
      'su_entity_item' => $teaser_item_field,
      'su_entity_headline' => $header_text,
    ], 'paragraph');
    $node = $I->createEntity([
      'title' => $this->faker->words(3, TRUE),
      'type' => 'stanford_page',
      'su_page_components' => [
        'target_id' => $paragraph->id(),
        'entity' => $paragraph,
      ],
    ]);
    $I->amOnPage($node->toUrl()->toString());
    $I->canSee($node->label(), 'h1');
    $I->canSee($header_text, 'h2');
    foreach ($teaser_entities as $entity) {
      $I->canSee($entity->label(), '.su-entity-item h3');
    }
  }

  /**
   * Test news without publishing date.
   */
  public function testNewsTeaser(AcceptanceTester $I) {
    $news = $I->createEntity([
      'type' => 'stanford_news',
      'title' => $this->faker->words(3, TRUE),
      'su_news_publishing_date' => [],
    ]);
    $paragraph = $I->createEntity([
      'type' => 'stanford_entity',
      'su_entity_item' => [['target_id' => $news->id()]],
    ], 'paragraph');
    $node = $I->createEntity([
      'title' => $this->faker->words(3, TRUE),
      'type' => 'stanford_page',
      'su_page_components' => [
        'target_id' => $paragraph->id(),
        'entity' => $paragraph,
      ],
    ]);
    $I->amOnPage($node->toUrl()->toString());
    $I->canSee($node->label(), 'h1');
    $I->canSee($news->label(), 'h2');
  }

}
