<?php

use Faker\Factory;

require_once __DIR__ . '/../TestFilesTrait.php';

/**
 * Tests for various media functionality.
 */
class MediaCest {

  use TestFilesTrait;

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
   * Documents can be embedded as links.
   */
  public function testFileLinks(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/config/content/linkit/manage/default/matchers');
    $I->canSee('Metadata: [media:field_media_file:entity:basename]: [media:field_media_file:entity:mime]');
  }

  /**
   * Media Types Exist.
   */
  public function testForMediaTypes(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/structure/media');
    $I->canSee('Embeddable');
    $I->canSee('File');
    $I->canSee('Google Form');
    $I->canSee('Image');
    $I->canSee('Video');
  }

  /**
   * Embedded media should not have a </source> tag.
   */
  public function testSourceTag(AcceptanceTester $I) {
    /** @var \Drupal\Core\File\FileSystemInterface $file_system */
    $file_system = \Drupal::service('file_system');
    $image_path = $file_system->copy(__DIR__ . '/../assets/logo.jpg', 'public://' . $this->faker->word . '.jpg');

    $file = $I->createEntity(['uri' => $image_path], 'file');
    $image_media = $I->createEntity([
      'bundle' => 'image',
      'field_media_image' => [
        'target_id' => $file->id(),
        'alt' => '',
      ],
    ], 'media');
    $wysiwyg = $I->createEntity([
      'type' => 'stanford_wysiwyg',
      'su_wysiwyg_text' => [
        'value' => '<drupal-media data-entity-type="media" data-entity-uuid="' . $image_media->uuid() . '">&nbsp;</drupal-media>',
        'format' => 'stanford_html',
      ],
    ], 'paragraph');
    $node = $I->createEntity([
      'type' => 'stanford_page',
      'title' => $this->faker->words(3, true),
      'su_page_components' => [
        [
          'target_id' => $wysiwyg->id(),
          'entity' => $wysiwyg,
        ],
      ],
    ], 'node');
    $I->amOnPage($node->toUrl()->toString());
    $page = $I->grabPageSource();
    preg_match('/<\/source>/', $page, $source_tags);
    $I->assertEmpty($source_tags);
  }

  /**
   * Embeddable types enabled.
   */
  public function testForEmbeddableOptions(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/structure/media/manage/embeddable');
    $I->canSeeCheckboxIsChecked('ArcGIS StoryMaps');
    $I->canSeeCheckboxIsChecked('CircuitLab');
    $I->canSeeCheckboxIsChecked('Dailymotion');
    $I->canSeeCheckboxIsChecked('Facebook');
    $I->canSeeCheckboxIsChecked('Flickr');
    $I->canSeeCheckboxIsChecked('Getty Images');
    $I->canSeeCheckboxIsChecked('Instagram');
    $I->canSeeCheckboxIsChecked('Issuu');
    $I->canSeeCheckboxIsChecked('Livestream');
    $I->canSeeCheckboxIsChecked('MathEmbed');
    $I->canSeeCheckboxIsChecked('SimpleCast');
    $I->canSeeCheckboxIsChecked('SlideShare');
    $I->canSeeCheckboxIsChecked('SoundCloud');
    $I->canSeeCheckboxIsChecked('Spotify');
    $I->canSeeCheckboxIsChecked('Stanford Digital Repository');
    $I->canSeeCheckboxIsChecked('Twitter');

    $I->cantSeeCheckboxIsChecked('Codepen');
  }

  /**
   * Embeddable fields set right.
   */
  public function testForEmbeddableFields(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/structure/media/manage/embeddable');
    $I->seeOptionIsSelected('Field for unstructured embed codes', 'media.field_media_embeddable_code');
  }

  /**
   * Embeddable form fields.
   */
  public function testForEmbeddableFormFields(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/media/add/embeddable');
    $I->canSee('Name');
    $I->canSee('oEmbed URL');
    $I->canSee('Embed Code');

    $name = $this->faker->words(2, TRUE);
    $I->fillField('Name', $name);
    $I->fillField('oEmbed URL', 'https://twitter.com/SLAClab/status/1303365422583099392');
    $I->click('Save');

    $I->amOnPage('/admin/content/media');
    $I->canSee($name);

    $I->amOnPage('/media/add/embeddable');
    $name = $this->faker->words(2, TRUE);
    $I->fillField('Name', $name);
    $I->fillField('Embed Code', '<script>alert("Hello World");</script>');
    $I->click('Save');

    $I->amOnPage('/admin/content/media');
    $I->canSee($name);
  }

  /**
   * Specific embed codes are allowed for site managers.
   *
   * @group embed-codes
   */
  public function testAllowedEmbedCodes(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $I->amOnPage('/media/add/embeddable');
    $I->fillField('Name', $this->faker->words(3, TRUE));
    $I->fillField('Embed Code', '<iframe src="' . $this->faker->url . '"></iframe>');
    $I->click('Save');
    $I->canSee('The given embeddable code is not permitted');

    $I->fillField('Embed Code', 'https://calendar.google.com/foo-bar');
    $I->click('Save');
    $I->canSee('The given embeddable code is not permitted');

    $allowed_codes = [
      '<iframe src="https://calendar.google.com/foo-bar" title="foobar"></iframe>',
      '<iframe src="https://airtable.com/foo-bar" title="foobar"></iframe>',
      '<iframe src="https://outlook.office365.com/foo-bar" title="foobar"></iframe>',
      '<iframe src="https://office365stanford.sharepoint.com/foo-bar" title="foobar"></iframe>',
      '<iframe src="https://app.smartsheet.com/foo-bar" title="foobar"></iframe>',
    ];

    foreach ($allowed_codes as $allowed_code) {
      $I->amOnPage('/media/add/embeddable');
      $I->fillField('Name', $this->faker->words(3, TRUE));
      $I->fillField('Embed Code', $allowed_code);
      $I->click('Save');
      $I->canSee('has been created');
    }
  }

  /**
   * Google Form additional field
   */
  public function testForGoogleFormFields(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $I->amOnPage('/media/add/google_form');
    $I->canSee('Form Height');
  }

  /**
   * Administrative file listing can delete files.
   */
  public function testDeleteFiles(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/content/files');
    $I->canSeeResponseCodeIs(403);
    $I->amOnPage('/user/logout');

    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/content/files');
    $I->canSeeResponseCodeIs(200);

    $I->amOnPage('/media/add/file');

    $this->preparePdf();

    $name = $this->faker->words(2, TRUE);
    $I->fillField('Name', $name);
    $I->attachFile('Add a new file', $this->filePath);
    $I->click('Save');
    $I->canSee('has been created.');
    $I->amOnPage('/admin/content/files');
    $I->canSee($this->filePath);

    $fids = \Drupal::entityTypeManager()
      ->getStorage('file')
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('uri', "%{$this->filePath}", 'LIKE')
      ->execute();
    $I->assertNotEmpty($fids);

    $dom = new DOMDocument();
    libxml_use_internal_errors(TRUE);
    $dom->loadHTML($I->grabPageSource());
    libxml_clear_errors();
    $xpath = new DOMXPath($dom);

    /** @var \DOMNodeList $nodes */
    $nodes = $xpath->evaluate("//label[contains(., '{$this->filePath}')]");
    $input_id = $nodes->item(0)->attributes['for']->value;
    $I->checkOption('#' . $input_id);
    $I->selectOption('Action', 'Delete File');
    $I->click('Apply to selected items');
    $I->canSee('Are you sure you wish to perform');
    $I->canSee($this->filePath);
    $I->click('Execute action');
    $I->canSee('Action processing results: Delete entities');
    $I->amOnPage('/admin/content/files');
    $I->cantSee($this->filePath);

    $fids = \Drupal::entityTypeManager()
      ->getStorage('file')
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('uri', "%{$this->filePath}", 'LIKE')
      ->execute();
    $I->assertEmpty($fids);
  }

  /**
   * SUL Embeddables can be saved.
   */
  public function testArcGis(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/media/add/embeddable');
    $name = $this->faker->words(2, TRUE);
    $I->fillField('Name', $name);
    $I->fillField('oEmbed URL', 'https://purl.stanford.edu/fr477qg2469');
    $I->click('Save');
    $I->canSee('has been created.');
  }

  /**
   * Test media category taxonomy field.
   */
  public function testCategoryField(AcceptanceTester $I) {
    /** @var \Drupal\Core\File\FileSystemInterface $file_system */
    $file_system = \Drupal::service('file_system');
    $image_path = $file_system->copy(__DIR__ . '/../assets/logo.jpg', 'public://' . $this->faker->word . '.jpg');
    $file = $I->createEntity(['uri' => $image_path], 'file');

    $unrelated_term = $I->createEntity([
      'vid' => 'media_tags',
      'name' => $this->faker->word,
    ], 'taxonomy_term');

    $parent_term = $I->createEntity([
      'vid' => 'media_tags',
      'name' => $this->faker->word,
    ], 'taxonomy_term');
    $child_term = $I->createEntity([
      'vid' => 'media_tags',
      'name' => $this->faker->word,
      'parent' => $parent_term->id(),
    ], 'taxonomy_term');

    $media = $I->createEntity([
      'bundle' => 'image',
      'field_media_image' => $file->id(),
      'name' => $this->faker->words(3, TRUE),
      'su_media_category' => $child_term,
    ], 'media');

    $I->logInWithRole('site_manager');

    $I->amOnPage($media->toUrl('edit-form')->toString());
    $I->canSeeInField('Category', $child_term->id());
    $I->click('Save');

    $I->amOnPage('/admin/content/media');
    $I->canSee($media->label());

    $I->selectOption('Category', $unrelated_term->label());
    $I->click('Filter');
    $I->cantSee($media->label());

    $I->selectOption('Category', $parent_term->label());
    $I->click('Filter');
    $I->canSee($media->label());

    $I->selectOption('Category', $unrelated_term->label());
    $I->click('Filter');
    $I->cantSee($media->label());

    $I->selectOption('Category', '-' . $child_term->label());
    $I->click('Filter');
    $I->canSee($media->label());
  }

}
