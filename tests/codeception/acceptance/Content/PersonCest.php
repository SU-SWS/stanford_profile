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
    $I->amOnPage('/admin/content');
    $I->see('Haley Jackson');
    $I->amOnPage('/people/haley-jackson');
    $I->see('This page is currently unpublished and not visible to the public.');
    $I->see('Haley Jackson');
    $I->see('People', '.su-multi-menu');
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
    $first_name = $this->faker->firstName;
    $middle_name = $this->faker->firstName;
    $last_name = $this->faker->lastName;

    $I->logInWithRole('contributor');
    $I->amOnPage('/node/add/stanford_person');
    $I->fillField('First Name', $first_name);
    $I->fillField('Last Name', "$middle_name & $last_name");
    $I->fillField('Short Title', $this->faker->text);
    $I->click('Save');
    $I->canSee("$first_name $middle_name & $last_name", 'h1');
  }

  /**
   * D8CORE-2613: Taxonomy menu items don't respect the UI.
   *
   * @group 4704
   */
  public function testD8Core2613Terms(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');

    $term1 = $I->createEntity([
      'name' => $this->faker->words(2, TRUE),
      'vid' => 'stanford_person_types',
    ], 'taxonomy_term');
    $term2 = $I->createEntity([
      'name' => $this->faker->words(2, TRUE),
      'vid' => 'stanford_person_types',
    ], 'taxonomy_term');
    $term3 = $I->createEntity([
      'name' => $this->faker->words(2, TRUE),
      'vid' => 'stanford_person_types',
      'parent' => ['target_id' => $term1->id()],
    ], 'taxonomy_term');
    $I->amOnPage($term3->toUrl('edit-form')->toString());
    $I->click('Save');
    $I->canSee('Updated term');

    $I->amOnPage($term3->toUrl()->toString());
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
      'name' => $this->faker->word,
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

  /**
   * Validate metadata information.
   *
   * @group metadata
   */
  public function testMetaData(AcceptanceTester $I) {
    $values = [
      'image_alt' => $this->faker->words(3, TRUE),
      'body' => $this->faker->paragraph,
      'first_name' => $this->faker->firstName,
      'last_name' => $this->faker->lastName,
      'profile_link' => $this->faker->url,
    ];

    /** @var \Drupal\Core\File\FileSystemInterface $file_system */
    $file_system = \Drupal::service('file_system');
    $image_path = $file_system->copy(__DIR__ . '/../assets/logo.jpg', 'public://' . $this->faker->word . '.jpg');

    $file = $I->createEntity(['uri' => $image_path], 'file');
    $media = $I->createEntity([
      'bundle' => 'image',
      'field_media_image' => [
        'target_id' => $file->id(),
        'alt' => $values['image_alt'],
      ],
    ], 'media');

    /** @var \Drupal\node\NodeInterface $node */
    $node = $I->createEntity([
      'title' => $this->faker->words(3, TRUE),
      'su_person_first_name' => $values['first_name'],
      'su_person_last_name' => $values['last_name'],
      'su_person_photo' => $media->id(),
      'type' => 'stanford_person',
      'body' => $values['body'],
      'su_person_profile_link' => [
        'uri' => $values['profile_link'],
        'title' => $this->faker->words(3, TRUE),
      ],
    ]);
    $I->amOnPage($node->toUrl()->toString());
    $I->canSee($node->label(), 'h1');

    $I->assertEquals($node->label(), $I->grabAttributeFrom('meta[property="og:title"]', 'content'), 'Metadata "og:title" should match.');
    $I->assertEquals($node->label(), $I->grabAttributeFrom('meta[name="twitter:title"]', 'content'), 'Metadata "twitter:title" should match.');
    $I->assertEquals($values['body'], $I->grabAttributeFrom('meta[name="description"]', 'content'), 'Metadata "description" should match.');
    $I->assertEquals('profile', $I->grabAttributeFrom('meta[property="og:type"]', 'content'), 'Metadata "og:type" should match.');
    $I->assertStringContainsString(basename($image_path), $I->grabAttributeFrom('meta[property="og:image"]', 'content'), 'Metadata "og:image" should match.');
    $I->assertStringContainsString(basename($image_path), $I->grabAttributeFrom('meta[property="og:image:url"]', 'content'), 'Metadata "og:image:url" should match.');
    $I->assertStringContainsString($values['first_name'], $I->grabAttributeFrom('meta[property="profile:first_name"]', 'content'), 'Metadata "profile:first_name" should match.');
    $I->assertStringContainsString($values['last_name'], $I->grabAttributeFrom('meta[property="profile:last_name"]', 'content'), 'Metadata "profile:last_name" should match.');
    $I->assertStringContainsString(basename($image_path), $I->grabAttributeFrom('meta[name="twitter:image"]', 'content'), 'Metadata "twitter:image" should match.');
    $I->assertEquals($values['image_alt'], $I->grabAttributeFrom('meta[property="og:image:alt"]', 'content'), 'Metadata "og:image:alt" should match.');
    $I->assertEquals($values['image_alt'], $I->grabAttributeFrom('meta[name="twitter:image:alt"]', 'content'), 'Metadata "twitter:image:alt" should match.');
    $I->assertEquals($values['body'], $I->grabAttributeFrom('meta[name="twitter:description"]', 'content'), 'Metadata "twitter:description" should match.');
    $I->assertEquals($values['profile_link'], $I->grabAttributeFrom('link[rel="canonical"]', 'href'), 'Metadata "canonical" should match.');
  }

}
