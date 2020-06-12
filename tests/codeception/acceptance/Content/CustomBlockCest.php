<?php

/**
 * Test for custom block types.
 *
 * @group block
 */
class CustomBlockCest {

  /**
   * Site managers should be able to edit custom blocks.
   */
  public function testCustomBlockAccess(AcceptanceTester $I) {
    $block = $I->createEntity([
      'type' => 'stanford_component_block',
      'info' => 'Custom Block Test',
    ], 'block_content');
    $I->logInWithRole('site_manager');
    $I->amOnPage($block->toUrl()->toString());
    $I->fillField('Block description', 'Foo Bar');
    $I->click('Save');
  }

}
