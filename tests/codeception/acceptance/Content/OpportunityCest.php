<?php

use Faker\Factory;

/**
 * Test opportunity content type.
 *
 * @group opportunity
 */
class OpportunityCest {

  /**
   * Faker.
   *
   * @var \Faker\Generator
   */
  protected $faker;

  /**
   * Test Constructor.
   */
  public function __construct() {
    $this->faker = Factory::create();
  }

  public function testContentAccess(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $I->amOnPage('/node/add');
    $I->canSee('Opportunity');
    $I->amOnPage('/admin/structure/taxonomy');
    $I->cantSee('Opportunity');
    $I->amOnPage('/user/logout');
    $I->click('Log out', 'form');

    $I->logInWithRole('contibutor');
    $I->amOnPage('/node/add');
    $I->cantSee('Opportunity');
    $I->amOnPage('/admin/structure/taxonomy');
    $I->cantSee('Opportunity');
    $I->amOnPage('/user/logout');
    $I->click('Log out', 'form');
  }

}
