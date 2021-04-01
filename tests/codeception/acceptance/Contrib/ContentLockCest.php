<?php

/**
 * Class ContentLockCest.
 *
 * @group contrib
 */
class ContentLockCest {

  /**
   * When on a node edit form, the content is locked from simultaneous edits.
   */
  public function testContentLock(AcceptanceTester $I){
    $I->logInWithRole('site_manager');
    $node = $I->createEntity(['type' => 'stanford_page','title' => 'Foo Bar']);
    $I->amOnPage("/node/{$node->id()}/edit");
    $I->canSee('This content is now locked against simultaneous editing');
  }

}
