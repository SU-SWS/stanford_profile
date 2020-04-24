<?php

class NodeRevisionDeleteCest {

  public function testNodeRevisionDelete(AcceptanceTester $I) {
    $node = $I->createEntity([
      'type' => 'stanford_page',
      'title' => 'revision test',
    ]);
  }

}
