<?php

use Faker\Factory;

/**
 * Tests for various media form functionality.
 */
class MediaFormsCest {

  /**
   * @var \Faker\Generator
   */
  protected $faker;

  /**
   * Test Constructor
   */
  public function __construct() {
    $this->faker = Factory::create();
  }

  /**
   * Test embeddables form alters
   */
  public function testFormAlters(AcceptanceTester $I) {
    $support_url = 'https://stanford.service-now.com/it_services?id=sc_cat_item&sys_id=83daed294f4143009a9a97411310c70a';
    $I->logInWithRole('site_manager');
    $I->amOnPage('/media/add/embeddable');
    $I->seeLink('request support.', $support_url);
    $I->amOnPage('/user/logout');
    $I->logInWithRole('administrator');
    $I->amOnPage('/media/add/embeddable');
    $name = $this->faker->words(3, true);
    $I->fillField('Name', $name);
    $I->fillField('Embed Code', '<div>test</div>');
    $I->click('Save');
    $I->seeInCurrentUrl('/admin/content/media');
    $I->amOnPage('/user/logout');
    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/content/media');
    $I->click($name);
    $I->seeInCurrentUrl('edit');
    $I->seeLink('request support.', $support_url);
    $I->click('Delete');
    $I->seeInCurrentUrl('delete');
    $I->click('Delete');
    $I->dontSeeLink($name);
    $I->seeInCurrentUrl('/admin/content/media');
  }

}
