<?php

use Faker\Factory;

/**
 * Class PublicationsCest.
 *
 * @group content
 */
class PublicationsCest {

  /**
   * @group testme
   */
  public function testBookCitation(AcceptanceTester $I) {
    $I->createEntity([
      'vid' => 'stanford_publication_topics',
      'name' => 'Foo Bar',
    ], 'taxonomy_term');

    $faker = Factory::create();
    $I->logInWithRole('contributor');
    $I->amOnPage('/node/add/stanford_publication');
    $I->fillField('Title', 'Test Publication');
    $I->fillField('Publication Topic Terms (value 1)', 'Foo Bar');
    $I->selectOption('su_publication_citation[actions][bundle]', 'Book');
    $I->click('Add Citation');
    $I->fillField('First Name', $faker->firstName);
    $I->fillField('Last Name', $faker->lastName);
    $I->fillField('Subtitle', $faker->text);
    $I->fillField('Publication Place', $faker->text);
    $I->fillField('Publisher', $faker->text);
    $I->fillField('Year', $faker->numberBetween(1900, 2020));
    $I->fillField('su_publication_cta[0][uri]', $faker->url);
    $I->fillField('Link text', $faker->text);

    $I->click('Save');
    $I->canSee('Test Publication', 'h1');
  }

  /**
   * Test out the list pages.
   */
  public function testAllPublicationListPage(AcceptanceTester $I) {
    $this->testBookCitation($I);
    $I->amOnPage('/publications');
    $I->canSee('Test Publication');
    $I->click('Foo Bar');
    $I->canSee('Test Publication');
  }

}
