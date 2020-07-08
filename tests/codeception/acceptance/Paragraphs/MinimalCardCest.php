<?php

/**
 * Class MinimalCardCest.
 *
 * @group paragraphs
 */
class MinimalCardCest {

  /**
   * Minimal card paragraph type should exist.
   */
  public function testMinimalCard(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/structure/paragraphs_type/su_minimal_card/fields');
    $I->canSee('CTA Link');
    $I->canSee('Image');
  }

}
