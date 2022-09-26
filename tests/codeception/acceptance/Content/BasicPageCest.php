<?php

use Drupal\Component\Utility\Unicode;
use Faker\Factory;

/**
 * Class BasicPageCest.
 *
 * @group content
 * @group basic_page
 */
class BasicPageCest {

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
   * Test placing a basic page in the menu with a child menu item.
   *
   * @group pathauto
   */
  public function testCreatingPage(AcceptanceTester $I) {
    $node_title = $this->faker->text(20);

    $I->logInWithRole('site_manager');
    $I->amOnPage('/node/add/stanford_page');
    $I->fillField('Title', $node_title);
    $I->checkOption('Provide a menu link');
    $I->fillField('Menu link title', "$node_title Item");
    // The label on the menu parent changes in D9 vs D8
    $I->selectOption('Parent link', ' <Main navigation>');
    $I->uncheckOption('Generate automatic URL alias');
    $alias = preg_replace('/[^a-z0-9]/', '-', strtolower($this->faker->words(3, TRUE)));
    $I->fillField('URL alias', "/$alias");
    $I->click('Save');
    $I->canSeeLink("$node_title Item");
    $I->canSeeInCurrentUrl("/$alias");
    $I->assertStringContainsString("/$alias", $I->grabFromCurrentUrl());

    $child_title = $this->faker->text('15');

    $I->amOnPage('/node/add/stanford_page');
    $I->fillField('Title', $child_title);
    $I->checkOption('Provide a menu link');
    $I->fillField('Menu link title', "$child_title Item");
    $I->selectOption('Parent link', "-- $node_title Item");
    $I->click('Change parent (update list of weights)');
    $I->click('Save');
    $I->canSeeLink("$child_title Item");
    $I->canSeeInCurrentUrl("/$alias");
  }

  /**
   * Number of h1 tags should always be 1.
   */
  public function testH1Tags(AcceptanceTester $I) {
    $I->amOnPage('/' . $this->faker->text);
    $I->canSeeResponseCodeIs(404);
    $I->canSeeNumberOfElements('h1', 1);

    $I->amOnPage('/search?keys=stuff&search=');
    $I->canSeeResponseCodeIs(200);
    $I->canSeeNumberOfElements('h1', 1);
  }

  /**
   * The revision history tab should be functional.
   *
   * Regression test for D8CORE-1547.
   */
  public function testRevisionPage(AcceptanceTester $I) {
    $title = $this->faker->words(3, TRUE);
    $I->logInWithRole('site_manager');
    $node = $I->createEntity(['title' => $title, 'type' => 'stanford_page']);
    $I->amOnPage($node->toUrl()->toString());
    $I->click('Version History');
    $I->canSeeResponseCodeIs(200);
  }

  /**
   * There should be Page Metadata fields
   */
  public function testPageDescription(AcceptanceTester $I) {
    $title = $this->faker->words(3, TRUE);
    $description = $this->faker->words(10, TRUE);
    $I->logInWithRole('site_manager');
    $I->amOnPage('/node/add/stanford_page');
    $I->see('Page Metadata');
    $I->see('Page Image');
    $I->see('Basic Page Type');
    $I->fillField('Title', $title);
    $I->fillField('Page Description', $description);
    $I->selectOption('Basic Page Type', 'Research');
    $I->click('Save');
    $I->seeInSource('<meta name="description" content="' . $description . '" />');
  }

  /**
   * Test that the vocabulary and default terms exist.
   */
  public function testBasicPageVocabularyTermsExists(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $I->amOnPage("/admin/structure/taxonomy/manage/basic_page_types/overview");
    $I->canSeeResponseCodeIs(200);
    $I->canSee('Research');
    $I->amOnPage("/admin/structure/taxonomy/manage/basic_page_types/add");
    $I->canSeeResponseCodeIs(200);
    $I->fillField('Name', $this->faker->words(4, TRUE));
    $I->click('Save');
    $I->canSee('Created new term');
  }

  /**
   * A site manager should be able to place a page under an unpublished page.
   */
  public function testUnpublishedMenuItems(AcceptanceTester $I) {
    $unpublished_title = $this->faker->words(5, TRUE);
    $child_page_title = $this->faker->words(5, TRUE);

    $I->logInWithRole('site_manager');
    $I->amOnPage('/node/add/stanford_page');
    $I->fillField('Title', $unpublished_title);
    $I->checkOption('Provide a menu link');
    $I->fillField('Menu link title', $unpublished_title);
    $I->uncheckOption('Published');
    $I->click('Save');
    $I->canSee($unpublished_title, 'h1');
    $unpublished_url = $I->grabFromCurrentUrl();

    $I->amOnPage('/node/add/stanford_page');
    $I->fillField('Title', $child_page_title);
    $I->checkOption('Provide a menu link');
    $I->fillField('Menu link title', $child_page_title);
    $I->selectOption('Parent link', '-- ' . Unicode::truncate($unpublished_title, 30, TRUE, FALSE));
    $I->click('Change parent (update list of weights)');
    $I->uncheckOption('Published');
    $I->click('Save');
    $I->canSee($child_page_title, 'h1');

    $I->canSeeInCurrentUrl("$unpublished_url/");
  }

  /**
   * Clone a basic page.
   */
  public function testClone(AcceptanceTester $I) {
    $title = $this->faker->words(3, TRUE);

    $I->logInWithRole('contributor');
    $I->amOnPage('/node/add/stanford_page');
    $I->fillField('Title', $title);
    $I->click('Save');
    $I->amOnPage('/admin/content');
    $I->canSee($title);
    $I->checkOption('tr:contains("' . $title . '") input[name^="views_bulk_operations_bulk_form"]');
    $I->selectOption('Action', 'Clone selected content');
    $I->click('Apply to selected items');
    $I->selectOption('Clone how many times', 2);
    $I->click('Apply');
    $links = $I->grabMultiple('a:contains("' . $title . '")');
    $I->assertCount(3, $links);
  }

  /**
   * Test the basic page scheduled publishing.
   *
   * @group scheduler
   */
  public function testScheduler(AcceptanceTester $I) {
    $time = \Drupal::time();

    /** @var \Drupal\system\TimeZoneResolver $timezone_resolver */
    $timezone_resolver = \Drupal::service('system.timezone_resolver');
    $timezone_resolver->setDefaultTimeZone();

    $I->logInWithRole('site_manager');
    $node = $I->createEntity([
      'title' => $this->faker->words(3, TRUE),
      'type' => 'stanford_page',
    ]);
    $I->amOnPage($node->toUrl('edit-form')->toString());
    $I->fillField('publish_on[0][value][date]', date('Y-m-d'));
    $I->fillField('publish_on[0][value][time]', date('H:i:s', $time->getCurrentTime() + 10));
    $I->click('Save');
    $I->canSee('This page is currently unpublished');
    echo 'sleep 15 seconds' . PHP_EOL;
    sleep(15);
    $I->runDrush('sch-cron');
    $I->amOnPage($node->toUrl()->toString());
    $I->cantSee('This page is currently unpublished');
    $I->amOnPage($node->toUrl('edit-form')->toString());
    $I->canSeeCheckboxIsChecked('Published');
  }

  /**
   * Validate the Spacer Paragraph type exists
   */
  public function testSpacerParagraph(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/structure/paragraphs_type');
    $I->canSee('Spacer');
    $I->canSee("stanford_spacer");
  }

  /**
   * Validate metadata information.
   *
   * @group metadata
   */
  public function testMetaData(AcceptanceTester $I) {
    $values = [
      'banner_image_alt' => $this->faker->words(3, TRUE),
      'meta_image_alt' => $this->faker->words(3, TRUE),
      'banner_header' => $this->faker->words(3, TRUE),
      'page_description' => $this->faker->words(10, TRUE),
    ];

    /** @var \Drupal\Core\File\FileSystemInterface $file_system */
    $file_system = \Drupal::service('file_system');
    $banner_image_path = $file_system->copy(__DIR__ . '/../assets/logo.jpg', 'public://' . $this->faker->word . '.jpg');
    $meta_image_path = $file_system->copy(__DIR__ . '/../assets/logo.jpg', 'public://' . $this->faker->word . '.jpg');

    $file = $I->createEntity(['uri' => $banner_image_path], 'file');
    $banner_media = $I->createEntity([
      'bundle' => 'image',
      'field_media_image' => [
        'target_id' => $file->id(),
        'alt' => $values['banner_image_alt'],
      ],
    ], 'media');

    $banner_paragraph = $I->createEntity([
      'type' => 'stanford_banner',
      'su_banner_image' => $banner_media,
      'su_banner_header' => $values['banner_header'],
    ], 'paragraph');

    $file = $I->createEntity(['uri' => $meta_image_path], 'file');
    $meta_media = $I->createEntity([
      'bundle' => 'image',
      'field_media_image' => [
        'target_id' => $file->id(),
        'alt' => $values['meta_image_alt'],
      ],
    ], 'media');

    /** @var \Drupal\node\NodeInterface $node */
    $node = $I->createEntity([
      'title' => $this->faker->words(3, TRUE),
      'type' => 'stanford_page',
    ]);
    $I->amOnPage($node->toUrl()->toString());
    $I->canSee($node->label(), 'h1');

    $I->assertEquals($node->label(), $I->grabAttributeFrom('meta[property="og:title"]', 'content'), 'Metadata "og:title" should match.');
    $I->assertEquals($node->label(), $I->grabAttributeFrom('meta[name="twitter:title"]', 'content'), 'Metadata "twitter:title" should match.');
    $I->assertEquals('website', $I->grabAttributeFrom('meta[property="og:type"]', 'content'), 'Metadata "og:type" should match.');
    $I->cantSeeElement('meta', ['name' => 'description']);
    $I->cantSeeElement('meta', ['property' => 'og:image']);
    $I->cantSeeElement('meta', ['property' => 'og:image:url']);
    $I->cantSeeElement('meta', ['name' => 'twitter:image']);
    $I->cantSeeElement('meta', ['name' => 'twitter:image:alt']);
    $I->cantSeeElement('meta', ['name' => 'twitter:description']);

    $node = $I->createEntity([
      'title' => $this->faker->words(3, TRUE),
      'type' => 'stanford_page',
      'su_page_banner' => ['entity' => $banner_paragraph],
    ]);
    $I->amOnPage($node->toUrl()->toString());
    $I->canSee($node->label(), 'h1');

    $I->canSee($values['banner_header']);
    $I->assertEquals($node->label(), $I->grabAttributeFrom('meta[name="twitter:title"]', 'content'), 'Metadata "twitter:title" should match.');
    $I->assertStringContainsString(basename($banner_image_path), $I->grabAttributeFrom('meta[property="og:image"]', 'content'), 'Metadata "og:image" should match.');
    $I->assertStringContainsString(basename($banner_image_path), $I->grabAttributeFrom('meta[property="og:image:url"]', 'content'), 'Metadata "og:image:url" should match.');
    $I->assertStringContainsString(basename($banner_image_path), $I->grabAttributeFrom('meta[name="twitter:image"]', 'content'), 'Metadata "twitter:image" should match.');
    $I->assertEquals($values['banner_image_alt'], $I->grabAttributeFrom('meta[property="og:image:alt"]', 'content'), 'Metadata "og:image:alt" should match.');
    $I->assertEquals($values['banner_image_alt'], $I->grabAttributeFrom('meta[name="twitter:image:alt"]', 'content'), 'Metadata "twitter:image:alt" should match.');

    $node = $I->createEntity([
      'title' => $this->faker->words(3, TRUE),
      'type' => 'stanford_page',
      'su_page_banner' => ['entity' => $banner_paragraph],
      'su_page_image' => $meta_media->id(),
      'su_page_description' => $values['page_description'],
    ]);
    $I->amOnPage($node->toUrl()->toString());
    $I->canSee($node->label(), 'h1');

    $I->canSee($values['banner_header']);
    $I->assertEquals($node->label(), $I->grabAttributeFrom('meta[name="twitter:title"]', 'content'), 'Metadata "twitter:title" should match.');
    $I->assertStringContainsString(basename($meta_image_path), $I->grabAttributeFrom('meta[property="og:image"]', 'content'), 'Metadata "og:image" should match.');
    $I->assertStringContainsString(basename($meta_image_path), $I->grabAttributeFrom('meta[property="og:image:url"]', 'content'), 'Metadata "og:image:url" should match.');
    $I->assertStringContainsString(basename($meta_image_path), $I->grabAttributeFrom('meta[name="twitter:image"]', 'content'), 'Metadata "twitter:image" should match.');
    $I->assertEquals($values['meta_image_alt'], $I->grabAttributeFrom('meta[property="og:image:alt"]', 'content'), 'Metadata "og:image:alt" should match.');
    $I->assertEquals($values['meta_image_alt'], $I->grabAttributeFrom('meta[name="twitter:image:alt"]', 'content'), 'Metadata "twitter:image:alt" should match.');
    $I->assertEquals($values['page_description'], $I->grabAttributeFrom('meta[name="twitter:description"]', 'content'), 'Metadata "twitter:description" should match.');
    $I->assertEquals($values['page_description'], $I->grabAttributeFrom('meta[name="description"]', 'content'), 'Metadata "description" should match.');
  }

  /**
   * @group search-results
   */
  public function testSearchResult(AcceptanceTester $I) {
    $text = $this->faker->paragraphs(2, TRUE);
    $wysiwyg = $I->createEntity([
      'type' => 'stanford_wysiwyg',
      'su_wysiwyg_text' => ['value' => $text, 'format' => 'stanford_html'],
    ], 'paragraph');
    $row = $I->createEntity([
      'type' => 'node_stanford_page_row',
      'su_page_components' => $wysiwyg,
    ], 'paragraph_row');
    $node = $I->createEntity([
      'title' => $this->faker->words(3, TRUE),
      'type' => 'stanford_page',
      'su_page_components' => $row,
    ]);
    $I->logInWithRole('contributor');
    $I->amOnPage($node->toUrl('edit-form')->toString());
    $I->click('Save');
    $I->canSee($node->label(), 'h1');

    $I->fillField('Search this site', $node->label());
    $I->click('Submit Search');
    $I->canSee($node->label(), 'h2');
    $I->canSee('Last Updated: ' . date('F j, Y'));
  }

}
