<?php

use Faker\Factory;

/**
 * Class AccordionCest.
 *
 * @group paragraphs
 * @group accordions
 */
class AccordionCest {

  protected $faker;

  public function __construct() {
    $this->faker = Factory::create();
  }

  /**
   * Create and check the accordion.
   */
  public function testCreatingAccordion(FunctionalTester $I) {
    $q_and_a = [
      [$this->faker->words(3, TRUE), $this->faker->paragraph()],
      [$this->faker->words(3, TRUE), $this->faker->paragraph()],
      [$this->faker->words(3, TRUE), $this->faker->paragraph()],
    ];

    $questions = [];
    foreach ($q_and_a as $item) {
      $question_paragraph = $I->createEntity([
        'type' => 'stanford_accordion',
        'su_accordion_title' => $item[0],
        'su_accordion_body' => [
          'value' => $item[1],
          'format' => 'stanford_minimal_html',
        ],
      ], 'paragraph');
      $questions[] = [
        'target_id' => $question_paragraph->id(),
        'entity' => $question_paragraph,
      ];
    }

    $paragraph = $I->createEntity([
      'type' => 'stanford_faq',
      'su_faq_headline' => $this->faker->words(4, TRUE),
      'su_faq_description' => [
        'value' => $this->faker->paragraph,
        'format' => 'stanford_html',
      ],
      'su_faq_questions' => $questions,
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
    $I->canSee($node->label(), 'h1');

    foreach ($q_and_a as $delta => $item) {
      [$question, $answer] = $item;
      $I->canSee($question);
      $I->cantSee($answer);

      $child_index = $delta + 1;
      $I->click($question);
      $I->waitForText($answer);
      $I->click($question);
    }

    $I->click('Expand All');
    foreach ($q_and_a as $item) {
      $I->canSee($item[1]);
    }

    $I->click('Collapse All');
    foreach ($q_and_a as $item) {
      $I->cantSee($item[1]);
    }
  }

}
