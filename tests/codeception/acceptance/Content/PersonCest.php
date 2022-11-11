<?php

use Faker\Factory;

/**
 * Test the news functionality.
 */
class PersonCest {

  /**
   * Faker service.
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
   * Test that the default content has installed and is unpublished.
   */
  public function testDefaultContentExists(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage("/people/haley-jackson");
    $I->see("This page is currently unpublished and not visible to the public.");
    $I->see("Haley Jackson", 'h1');
    $I->see("People", ".su-multi-menu");

  }

  /**
   * Test that the vocabulary and terms exist.
   */
  public function testVocabularyTermsExists(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/structure/taxonomy/manage/stanford_person_types/overview');
    $I->canSeeNumberOfElements('.term-id', 14);
  }

  /**
   * Test that the view pages exist.
   */
  public function testViewPagesExist(AcceptanceTester $I) {
    $I->amOnPage('/people');
    $I->seeLink('Student');
    $I->seeLink('Staff');
    $I->click('Staff');
    $I->canSeeResponseCodeIs(200);
    $I->see('Person Type');
  }

  /**
   * Test that content that gets created has the right url, header, and shows
   * up in the all view.
   */
  public function testCreatePerson(AcceptanceTester $I) {
    $term = $I->createEntity([
      'vid' => 'stanford_person_types',
      'name' => $this->faker->word,
    ], 'taxonomy_term');

    // Use 1s in the name to be at the top of the lists.
    $first_name = '111' . $this->faker->firstName;
    $last_name = '111' . $this->faker->lastName;
    $node = $I->createEntity([
      'type' => 'stanford_person',
      'su_person_first_name' => $first_name,
      'su_person_last_name' => $last_name,
      'su_person_type_group' => $term,
    ]);
    $I->amOnPage($node->toUrl()->toString());
    $I->see("$first_name $last_name", 'h1');
    $I->amOnPage('/people');
    $I->see("$first_name $last_name", 'h2');
    $I->seeLink("$first_name $last_name");

    $I->amOnPage($term->toUrl()->toString());
    $I->canSee($term->label(), 'h1');
    $I->see("$first_name $last_name", 'h3');
    $I->seeLink("$first_name $last_name");
  }

  /**
   * Test that the XML sitemap and metatag configuration is set.
   */
  public function testXMLMetaDataRevisions(AcceptanceTester $I) {
    $I->logInWithRole('administrator');

    // Revision Delete is enabled.
    $I->amOnPage('/admin/structure/types/manage/stanford_person');
    $I->seeCheckboxIsChecked('#edit-node-revision-delete-track');
    $I->seeCheckboxIsChecked('#edit-options-revision');
    $I->seeInField('#edit-minimum-revisions-to-keep', 5);

    // XML Sitemap.
    $I->amOnPage('/admin/config/search/xmlsitemap/settings');
    $I->see('Person');
    $I->amOnPage('/admin/config/search/xmlsitemap/settings/node/stanford_person');
    $I->selectOption('#edit-xmlsitemap-status', 1);

    // Metatags.
    $I->amOnPage('/admin/config/search/metatag/node__stanford_person');
    $I->canSeeResponseCodeIs(200);
  }

  /**
   * CAP-52: Check for the new fields.
   */
  public function testCap52Fields(AcceptanceTester $I){
    $I->logInWithRole('administrator');

    $I->amOnPage('/admin/structure/types/manage/stanford_person/fields');
    $I->canSee('Academic Appointments');
    $I->canSee('Administrative Appointments');
    $I->canSee('Scholarly and Research Interests');

    $I->amOnPage('/admin/structure/types/manage/stanford_person/form-display');
    $I->canSeeOptionIsSelected('fields[su_person_academic_appt][region]', 'Disabled');
    $I->canSeeOptionIsSelected('fields[su_person_admin_appts][region]', 'Disabled');
    $I->canSeeOptionIsSelected('fields[su_person_scholarly_interests][region]', 'Disabled');
  }

}
