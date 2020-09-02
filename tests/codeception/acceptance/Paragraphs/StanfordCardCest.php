<?php

/**
 * Codeception tests on card paragraph type.
 */
class StanfordCardCest {

  /**
   * Test a card with a button link.
   */
  public function testCardButtonLinkText(\AcceptanceTester $I) {
    $node = $this->createNodeWithLink($I);
    $I->amOnPage($node->toUrl()->toString());
    $I->canSeeLink('It\'s a "test" link & title', 'http://google.com');
  }

  /**
   * Test a card with an action link.
   */
  public function testCardActionLinkText(\AcceptanceTester $I) {
    $node = $this->createNodeWithLink($I, 'action');
    $I->amOnPage($node->toUrl()->toString());
    $I->canSeeLink('It\'s a "test" link & title', 'http://google.com');
  }

  /**
   * Generate a node with a paragraph that contains a link.
   */
  protected function createNodeWithLink(\AcceptanceTester $I, $link_type = 'button') {
    /** @var \Drupal\paragraphs\ParagraphInterface $paragraph */
    $paragraph = $I->createEntity([
      'type' => 'stanford_card',
      'su_card_link' => [
        'uri' => 'http://google.com',
        'title' => 'It\'s a "test" link & title',
      ],
      'su_card_link_display' => $link_type,
    ], 'paragraph');

    $row = $I->createEntity([
      'type' => 'node_stanford_page_row',
      'su_page_components' => [
        'target_id' => $paragraph->id(),
        'entity' => $paragraph,
      ],
    ], 'paragraph_row');

    $node = $I->createEntity([
      'type' => 'stanford_page',
      'title' => 'Test Card Links',
      'su_page_components' => [
        'target_id' => $row->id(),
        'entity' => $row,
      ],
    ]);
    // Clear router and menu cache so that the node urls work.
    $I->runDrush('cache-clear router');
    return $node;
  }

}
