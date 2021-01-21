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
    $faker = Factory::create();
    $I->logInWithRole('contributor');
    $I->amOnPage('/node/add/stanford_publication');
    $I->fillField('Title', 'Test Publication');
    $I->selectOption('su_publication_citation[actions][bundle]', 'Book');
    $I->click('Add new citation');
    $I->fillField('First Name', $faker->firstName);
    $I->fillField('Last Name', $faker->lastName);
    $I->fillField('Day', $faker->numberBetween(1, 28));
    $I->fillField('Month', $faker->numberBetween(1, 12));
    $I->fillField('Year', $faker->numberBetween(1900, 2020));
    $I->fillField('Publisher', $faker->text);
    $I->fillField('Volume', $faker->numberBetween(1, 10));
    $I->fillField('Page Number(s)', $faker->numberBetween(1, 99));
    $I->fillField('DOI/ISBN/Database Name', $faker->text);
    $I->fillField('su_publication_cta[0][uri]', $faker->url);
    $I->fillField('Link text', $faker->text);

    $I->fillField('External Link', $faker->url);
    $I->click('Save');
    $I->canSee('Test Publication', 'h1');
  }

  /**
   * @group testme
   */
  public function testJournalCitation(AcceptanceTester $I) {
    $faker = Factory::create();
    $I->logInWithRole('contributor');
    $I->amOnPage('/node/add/stanford_publication');
    $I->fillField('Title', 'Test Publication');
    $I->selectOption('su_publication_citation[actions][bundle]', 'Book');
    $I->click('Add new citation');
    $I->fillField('First Name', $faker->firstName);
    $I->fillField('Last Name', $faker->lastName);
    $I->fillField('Day', $faker->numberBetween(1, 28));
    $I->fillField('Month', $faker->numberBetween(1, 12));
    $I->fillField('Year', $faker->numberBetween(1900, 2020));
    $I->fillField('Publisher', $faker->text);
    $I->fillField('Volume', $faker->numberBetween(1, 10));
    $I->fillField('Page Number(s)', $faker->numberBetween(1, 99));
    $I->fillField('DOI/ISBN/Database Name', $faker->text);
    $I->fillField('su_publication_cta[0][uri]', $faker->url);
    $I->fillField('Link text', $faker->text);

    $I->fillField('External Link', $faker->url);
    $I->click('Save');
    $I->canSee('Test Publication', 'h1');
  }
  /**
   * @group testme
   */
  public function testArticleCitation(AcceptanceTester $I) {
    $faker = Factory::create();
    $I->logInWithRole('contributor');
    $I->amOnPage('/node/add/stanford_publication');
    $I->fillField('Title', 'Test Publication');
    $I->selectOption('su_publication_citation[actions][bundle]', 'Book');
    $I->click('Add new citation');
    $I->fillField('First Name', $faker->firstName);
    $I->fillField('Last Name', $faker->lastName);
    $I->fillField('Day', $faker->numberBetween(1, 28));
    $I->fillField('Month', $faker->numberBetween(1, 12));
    $I->fillField('Year', $faker->numberBetween(1900, 2020));
    $I->fillField('Publisher', $faker->text);
    $I->fillField('Volume', $faker->numberBetween(1, 10));
    $I->fillField('Page Number(s)', $faker->numberBetween(1, 99));
    $I->fillField('DOI/ISBN/Database Name', $faker->text);
    $I->fillField('su_publication_cta[0][uri]', $faker->url);
    $I->fillField('Link text', $faker->text);

    $I->fillField('External Link', $faker->url);
    $I->click('Save');
    $I->canSee('Test Publication', 'h1');
  }

  /**
   * @group testme
   */
  public function testThesisCitation(AcceptanceTester $I) {
    $faker = Factory::create();
    $I->logInWithRole('contributor');
    $I->amOnPage('/node/add/stanford_publication');
    $I->fillField('Title', 'Test Publication');
    $I->selectOption('su_publication_citation[actions][bundle]', 'Book');
    $I->click('Add new citation');
    $I->fillField('First Name', $faker->firstName);
    $I->fillField('Last Name', $faker->lastName);
    $I->fillField('Day', $faker->numberBetween(1, 28));
    $I->fillField('Month', $faker->numberBetween(1, 12));
    $I->fillField('Year', $faker->numberBetween(1900, 2020));
    $I->fillField('Publisher', $faker->text);
    $I->fillField('Volume', $faker->numberBetween(1, 10));
    $I->fillField('Page Number(s)', $faker->numberBetween(1, 99));
    $I->fillField('DOI/ISBN/Database Name', $faker->text);
    $I->fillField('su_publication_cta[0][uri]', $faker->url);
    $I->fillField('Link text', $faker->text);

    $I->fillField('External Link', $faker->url);
    $I->click('Save');
    $I->canSee('Test Publication', 'h1');
  }
}
