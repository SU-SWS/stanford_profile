<?php

use Faker\Factory;

/**
 * Test the node revision delete module functionality.
 */
class NodeRevisionDeleteCest {

  /**
   * Faker generator.
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
   * Test that revisions are trimmed after cron runs.
   */
  public function testNodeRevisionDelete(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    /** @var \Drupal\node\NodeInterface $node */
    $node = $I->createEntity([
      'type' => 'stanford_page',
      'title' => $this->faker->words(3, TRUE),
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
    $I->canSeeNumberOfElements('.diff-revisions tbody tr', 11);

    $I->runDrush('cron:run node_revision_delete_cron --force');
    $I->amOnPage("/node/{$node->id()}/revisions");
    $I->canSeeNumberOfElements('.diff-revisions tbody tr', 5);
  }

}
