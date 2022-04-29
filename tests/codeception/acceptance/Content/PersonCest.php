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
    $I->amOnPage("/admin/content");
    $I->see("Haley Jackson");
    $I->amOnPage("/people/haley-jackson");
    $I->see("This page is currently unpublished and not visible to the public.");
    $I->see("Haley Jackson");
    $I->see("People", ".su-multi-menu");

  }

  /**
   * Test that the vocabulary and terms exist.
   */
  public function testVocabularyTermsExists(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage("/admin/structure/taxonomy/manage/stanford_person_types/overview");
    $I->canSeeNumberOfElements(".term-id", 14);
  }

  /**
   * Test that the view pages exist.
   */
  public function testViewPagesExist(AcceptanceTester $I) {
    $I->amOnPage("/people");
    $I->see("Sorry, no results found");
    $I->seeLink('Student');
    $I->click("a[href='/people/staff']");
    $I->canSeeResponseCodeIs(200);
    $I->see("Sorry, no results found");
    $I->see("Person Type");
  }

  /**
   * Test that content that gets created has the right url, header, and shows
   * up in the all view.
   */
  public function testCreatePerson(AcceptanceTester $I) {
    $node = $I->createEntity([
      'type' => 'stanford_person',
      'su_person_first_name' => "John",
      'su_person_last_name' => "Wick",
    ]);
    $I->amOnPage($node->toUrl()->toString());
    $I->see("John Wick");
    $I->runDrush('cr');
    $I->amOnPage("/people");
    $I->see("John Wick");
  }

  /**
   * Test that the XML sitemap and metatag configuration is set.
   */
  public function testXMLMetaDataRevisions(AcceptanceTester $I) {
    $I->logInWithRole('administrator');

    // Revision Delete is enabled.
    $I->amOnPage('/admin/structure/types/manage/stanford_person');
    $I->seeCheckboxIsChecked("#edit-node-revision-delete-track");
    $I->seeCheckboxIsChecked("#edit-options-revision");
    $I->seeInField("#edit-minimum-revisions-to-keep", 5);

    // XML Sitemap.
    $I->amOnPage("/admin/config/search/xmlsitemap/settings");
    $I->see("Person");
    $I->amOnPage("/admin/config/search/xmlsitemap/settings/node/stanford_person");
    $I->selectOption("#edit-xmlsitemap-status", 1);

    // Metatags.
    $I->amOnPage("/admin/config/search/metatag/page_variant__people-layout_builder-0");
    $I->canSeeResponseCodeIs(200);
    $I->amOnPage("/admin/config/search/metatag/page_variant__stanford_person_list-layout_builder-1");
    $I->canSeeResponseCodeIs(200);
    $I->amOnPage("/admin/config/search/metatag/node__stanford_person");
    $I->canSeeResponseCodeIs(200);
  }

  /**
   * CAP-52: Check for the new fields.
   */
  public function testCap52Fields(AcceptanceTester $I) {
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

  /**
   * Special characters should stay.
   */
  public function testSpecialCharacters(AcceptanceTester $I) {
    $faker = Factory::create();
    $I->logInWithRole('contributor');
    $I->amOnPage('/node/add/stanford_person');
    $I->fillField('First Name', 'Foo');
    $I->fillField('Last Name', 'Bar-Baz & Foo');
    $I->fillField('Short Title', $faker->text);
    $I->click('Save');
    $I->canSee('Foo Bar-Baz & Foo', 'h1');
  }

  /**
   * D8CORE-2613: Taxonomy menu items don't respect the UI.
   *
   * @group 4704
   */
  public function testD8Core2613Terms(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');

    $term1 = $I->createEntity([
      'name' => $this->faker->words(2),
      'vid' => 'stanford_person_types',
    ], 'taxonomy_term');
    $term2 = $I->createEntity([
      'name' => $this->faker->words(2),
      'vid' => 'stanford_person_types',
    ], 'taxonomy_term');
    $term3 = $I->createEntity([
      'name' => $this->faker->words(2),
      'vid' => 'stanford_person_types',
      'parent' => ['target_id' => $term1->id()],
    ], 'taxonomy_term');

    $I->amOnPage('/people');
    $I->canSeeLink($term1->label());
    $I->canSeeLink($term2->label());
    $I->cantSeeLink($term3->label());

    $I->amOnPage($term3->toUrl('edit-form')->toString());
    $I->selectOption('Parent term', '<root>');
    $I->click('Save');

    $I->amOnPage('/people');
    $I->canSeeLink($term3->label());

    $I->amOnPage($term3->toUrl('edit-form')->toString());
    $I->selectOption('Parent term', $term2->label());
    $I->click('Save');

    $I->amOnPage('/people');
    $I->cantSeeLink($term3->label());

    $faker = Factory::create();
    $parent = $I->createEntity([
      'name' => 'Parent: ' . $faker->text(10),
      'vid' => 'stanford_person_types',
    ], 'taxonomy_term');
    $child = $I->createEntity([
      'name' => 'Child: ' . $faker->text(10),
      'vid' => 'stanford_person_types',
      'parent' => $parent->id(),
    ], 'taxonomy_term');
    $grandchild = $I->createEntity([
      'name' => 'GrandChild: ' . $faker->text(10),
      'vid' => 'stanford_person_types',
      'parent' => $child->id(),
    ], 'taxonomy_term');
    $great_grandchild = $I->createEntity([
      'name' => 'Great GrandChild: ' . $faker->text(10),
      'vid' => 'stanford_person_types',
      'parent' => $grandchild->id(),
    ], 'taxonomy_term');

    $another_parent = $I->createEntity([
      'name' => 'Parent: ' . $faker->words(2, TRUE),
      'vid' => 'stanford_person_types',
    ], 'taxonomy_term');
    $another_child = $I->createEntity([
      'name' => 'Child: ' . $faker->words(2, TRUE),
      'vid' => 'stanford_person_types',
      'parent' => $another_parent->id(),
    ], 'taxonomy_term');

    $node = $I->createEntity([
      'type' => 'stanford_person',
      'su_person_first_name' => $faker->firstName,
      'su_person_last_name' => $faker->lastName,
      'su_person_type_group' => [
        ['target_id' => $great_grandchild->id()],
        ['target_id' => $another_child->id()],
      ],
    ]);

    $I->amOnPage($great_grandchild->toUrl()->toString());
    $I->canSee($node->label());
    $I->amOnPage($grandchild->toUrl()->toString());
    $I->canSee($node->label());
    $I->amOnPage($child->toUrl()->toString());
    $I->canSee($node->label());
    $I->amOnPage($parent->toUrl()->toString());
    $I->canSee($node->label());
    $I->cantSee($another_child->label());
  }

  /**
   * Published checkbox should be hidden on term edit pages.
   */
  public function testTermPublishing(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $term = $I->createEntity([
      'vid' => 'stanford_person_types',
      'name' => 'Foo',
    ], 'taxonomy_term');
    $I->amOnPage($term->toUrl('edit-form')->toString());
    $I->cantSee('Published');
  }

  /**
   * Unpublished profiles should not display in the list.
   *
   * @group tester
   */
  public function testPublishedStatus(AcceptanceTester $I) {
    $term = $I->createEntity([
      'name' => $this->faker->words(2, TRUE),
      'vid' => 'stanford_person_types',
    ], 'taxonomy_term');
    /** @var \Drupal\node\NodeInterface $node */
    $node = $I->createEntity([
      'type' => 'stanford_person',
      'su_person_short_title' => $this->faker->title,
      'su_person_first_name' => $this->faker->firstName,
      'su_person_last_name' => $this->faker->lastName,
      'su_person_type_group' => $term->id(),
    ]);
    $I->logInWithRole('administrator');

    $I->amOnPage($term->toUrl()->toString());
    $I->canSee($node->label());
    $I->amOnPage($node->toUrl('edit-form')->toString());
    $I->uncheckOption('Published');
    $I->click('Save');
    $I->canSee('page is currently unpublished');

    $I->amOnPage($term->toUrl()->toString());
    $I->cantSee($node->label());
  }

}
