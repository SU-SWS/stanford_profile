<?php

/**
 * Tests for various media form functionality.
 */
class MediaFormsCest {

  /**
   * Test embeddables form alters
   */
  public function testFormAlters(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/media/add/embeddable');
    $I->seeLink('request support', 'https://stanford.service-now.com/it_services?id=sc_cat_item&sys_id=83daed294f4143009a9a97411310c70a');
    $I->fillField('Name', 'Test embed');
    $I->fillField('Embed Code', '<div>test</div>');
    $I->click('Save');
    $I->seeInCurrentUrl('/admin/content/media');
    $I->click('test embed');
    $I->seeInCurrentUrl('edit');
    $I->seeLink('request support', 'https://stanford.service-now.com/it_services?id=sc_cat_item&sys_id=83daed294f4143009a9a97411310c70a');
    $I->click('Delete');
    $I->seeInCurrentUrl('delete');
    $I->click('Delete');
    $I->dontSeeLink('Test embed');
    $I->seeInCurrentUrl('/admin/content/media');
  }



}
