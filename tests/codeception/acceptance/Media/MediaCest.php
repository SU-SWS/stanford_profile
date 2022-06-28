<?php

use Faker\Factory;

require_once __DIR__ . '/../TestFilesTrait.php';

/**
 * Tests for various media functionality.
 */
class MediaCest {

  use TestFilesTrait;

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
    $I->attachFile('File', $this->filePath);
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

}
