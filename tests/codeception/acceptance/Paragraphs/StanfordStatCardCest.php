<?php

use Faker\Factory;
use Codeception\Attribute as CodeceptionAttribute;
use Codeception\Example;

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

  #[CodeceptionAttribute\Examples(NULL, NULL, NULL, NULL, NULL)]
  #[CodeceptionAttribute\Examples('53565a', '8c1515', '8c1515', 'h2', NULL)]
  #[CodeceptionAttribute\Examples('8c1515', '620059', '620059', 'h3', NULL)]
  #[CodeceptionAttribute\Examples('620059', '007c92', '007c92', 'h4', NULL)]
  #[CodeceptionAttribute\Examples(NULL, '279989', '279989', 'div.splash-font', NULL)]
  #[CodeceptionAttribute\Examples(NULL, 'd1660f', 'd1660f', 'h2', TRUE)]
  #[CodeceptionAttribute\Examples(NULL, 'e04f39', 'e04f39', 'h3', TRUE)]
  #[CodeceptionAttribute\Examples(NULL, 'e04f39', 'e04f39', 'h4', TRUE)]
  #[CodeceptionAttribute\Group('stat-card')]
  public function testStatCard(AcceptanceTester $I, Example $example) {
    /** @var \Drupal\paragraphs\ParagraphInterface $layout */
    $layout = $I->createEntity(['type' => 'stanford_layout'], 'paragraph');
    $layout->setBehaviorSettings('layout_paragraphs', [
      'layout' => 'layout_paragraphs_1_column',
    ]);
    $layout->save();
    $text = $this->faker->paragraph;

    $stat = substr($this->faker->word, 0, 5);
    $headline = $this->faker->words(3, TRUE);

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
      'su_stat_stat' => $stat,
      'su_stat_superhead' => $this->faker->words(3, TRUE),
      'su_stat_headline' => $headline,
      'su_stat_bg_color' => ['color' => $example[0]],
      'su_stat_icon_color' => ['color' => $example[1]],
      'su_stat_stat_color' => ['color' => $example[2]],
      'su_stat_headline_lvl' => $example[3],
      'su_stat_heading_hide' => $example[4],
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

    if ($example[0]) {
      $I->canSee($headline, ".stat-bg-$example[0] .stat-card-headline");
    }
    else {
      $I->canSee($headline, '.stat-card-headline');
    }

    if (!$example[0] && $example[1]) {
      $I->canSeeElement(".stat-icon-$example[1] .fa-drupal");
    }
    else {
      $I->canSeeElement('.stat-card-icon .fa-drupal');
    }

    if (!$example[0] && $example[2]) {
      $I->canSee($stat, ".stat-stat-$example[2] .stat-card-stat");
    }
    else {
      $I->canSee($stat, '.stat-card-stat');
    }

    if ($example[3]) {
      [$tag] = explode('.', $example[3]);
      $I->canSee($headline, $tag);
    }
    else {
      $I->canSee($headline, 'h2');
    }

    if ($example[4]) {
      $I->canSee($headline, '.visually-hidden');
    }
  }

}
