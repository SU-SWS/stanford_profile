<?php

use Faker\Factory;
use Codeception\Attribute as CodeceptionAttribute;

/**
 * Codeception tests on stat card paragraph type.
 */
class StanfordStatCardCest {

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

  #[CodeceptionAttribute\Group('stat-card')]
  public function testStatCard(AcceptanceTester $I) {
    /** @var \Drupal\paragraphs\ParagraphInterface $layout */
    $layout = $I->createEntity(['type' => 'stanford_layout'], 'paragraph');
    $layout->setBehaviorSettings('layout_paragraphs', [
      'layout' => 'layout_paragraphs_1_column',
    ]);
    $layout->save();
    $text = $this->faker->paragraph;

    $stat = substr($this->faker->word, 0, 5);

    /** @var \Drupal\paragraphs\ParagraphInterface $card */
    $card = $I->createEntity([
      'type' => 'stanford_stat_card',
      'su_stat_body' => ['value' => $text, 'format' => 'plain_text'],
      'su_stat_button' => [
        'uri' => $this->faker->url(),
        'title' => $this->faker->words(3, TRUE),
      ],
      'su_stat_icon' => [
        'icon_name' => 'drupal',
        'style' => 'fa-brands',
        'settings' => serialize([
          'duotone' => [],
          'masking' => [],
          'power_transforms' => [],
        ]),
      ],
      'su_stat_icon_color' => ['color' => '2e2d29'],
      'su_stat_stat' => $stat,
      'su_stat_stat_color' => ['color' => '007c92'],
      'su_stat_superhead' => $this->faker->words(3, TRUE),
    ], 'paragraph');

    $card->setBehaviorSettings('layout_paragraphs', [
      'parent_uuid' => $layout->uuid(),
      'region' => 'main',
    ]);
    $card->save();

    $node = $I->createEntity([
      'title' => $this->faker->words(3, TRUE),
      'type' => 'stanford_page',
      'su_page_components' => [
        ['target_id' => $layout->id(), 'entity' => $layout],
        ['target_id' => $card->id(), 'entity' => $card],
      ],
    ]);
    $I->amOnPage($node->toUrl()->toString());
    $I->canSee($node->label(), 'h1');
    $I->canSee($text, '.su-stat-card');
    $I->canSee($stat, '.stat-stat-007c92');
    $I->canSeeElement('.stat-icon-2e2d29 .fa-drupal');
  }

}
