<?php

/**
 * Tests on the mission statement paragraph type.
 *
 * @group mission_statement
 */
class MissionStatementCest {

  /**
   * The paragraph should display on a basic page.
   */
  public function testMissionStatement(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $paragraph = $I->createEntity([
      'type' => 'cardinal_mission_statement',
      'su_mission_text' => 'This is the mission statement whether you choose to accept it or not.',
      'su_mission_cta' => [
        'uri' => 'http://google.com',
        'title' => 'Verify your identity',
      ],
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
      'title' => 'Bond, Drupal Bond',
      'su_page_components' => [
        'target_id' => $row->id(),
        'entity' => $row,
      ],
    ]);

    $I->amOnPage($node->toUrl()->toString());
    $I->canSee('This is the mission statement whether you choose to accept it or not');
    $I->canSeeLink('Verify your identity', 'http://google.com');
  }

}
