<?php

class NodeRevisionDeleteCest {

  public function testNodeRevisionDelete(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    /** @var \Drupal\node\NodeInterface $node */
    $node = $I->createEntity([
      'type' => 'stanford_page',
      'title' => 'revision test',
      'revision' => TRUE,
    ]);
    for ($j = 0; $j < 10; $j++) {
      $node->setNewRevision();
      $node->setRevisionLogMessage("Revision $j");
      $node->setRevisionCreationTime(time() - ($j * 30));
      $node->set('revision_translation_affected', TRUE);
      $node->save();
    }
    $I->amOnPage("/node/{$node->id()}/revisions");
    $I->canSeeNumberOfElements('.node-revision-table tbody tr', 11);
    $I->runDrush('cron');
    $I->amOnPage("/node/{$node->id()}/revisions");
    $I->canSeeNumberOfElements('.node-revision-table tbody tr', 5);
  }

}
