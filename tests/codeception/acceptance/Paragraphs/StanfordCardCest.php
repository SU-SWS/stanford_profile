<?php

use Faker\Factory;

/**
 * Codeception tests on card paragraph type.
 */
class StanfordCardCest {

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
   * Test a card with a button link.
   */
  public function testCardButtonLinkText(AcceptanceTester $I) {
    $node = $this->createNodeWithLink($I);
    $I->amOnPage($node->toUrl()->toString());
    $I->canSeeLink('It\'s a "test" link & title', 'http://google.com');
  }

  /**
   * Test a card with an action link.
   */
  public function testCardActionLinkText(AcceptanceTester $I) {
    $node = $this->createNodeWithLink($I, 'action');
    $I->amOnPage($node->toUrl()->toString());
    $I->canSeeLink('It\'s a "test" link & title', 'http://google.com');
  }

  /**
   * Generate a node with a paragraph that contains a link.
   */
  protected function createNodeWithLink(AcceptanceTester $I, $link_type = 'button') {
    /** @var \Drupal\paragraphs\ParagraphInterface $paragraph */
    $paragraph = $I->createEntity([
      'type' => 'stanford_card',
      'su_card_link' => [
        'uri' => 'http://google.com',
        'title' => 'It\'s a "test" link & title',
      ],
      'su_card_link_display' => $link_type,
    ], 'paragraph');

    $node = $I->createEntity([
      'type' => 'stanford_page',
      'title' => $this->faker->words(3, TRUE),
      'su_page_components' => [
        'target_id' => $paragraph->id(),
        'entity' => $paragraph,
      ],
    ]);

    return $node;
  }

}
