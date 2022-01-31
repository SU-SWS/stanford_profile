<?php

/**
 * Tests for various media functionality.
 *
 * @group testthis
 */
class MediaCest {

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

    $I->fillField('Name', "Twitter Test");
    $I->fillField('oEmbed URL', 'https://twitter.com/SLAClab/status/1303365422583099392');
    $I->click('Save');

    $I->amOnPage('/admin/content/media');
    $I->canSee("Twitter Test");

    $I->amOnPage('/media/add/embeddable');
    $I->fillField('Name', "Hello World");
    $I->fillField('Embed Code', '<script>alert("Hello World");</script>');
    $I->click('Save');

    $I->amOnPage('/admin/content/media');
    $I->canSee("Hello World");
  }

  /**
   * Google Form additional field
   */
  public function testForGoogleFormFields(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $I->amOnPage('/media/add/google_form');
    $I->canSee("Form Height");
  }

  /**
   * Administrative file listing can delete files.
   *
   * @group mikes
   */
  public function deleteFiles(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/content/files');
    $I->canSeeResponseCodeIs(403);
    $I->amOnPage('/user/logout');

    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/content/files');
    $I->canSeeResponseCodeIs(200);

    $I->amOnPage('/media/add/file');

    $filename = 'foo_bar.pdf';
    $file_path = codecept_data_dir() . $filename;
    copy(__DIR__ . '/test.pdf', $file_path);

    $I->fillField('Name', 'Test File');
    $I->attachFile('File', $filename);
    $I->click('Save');
    $I->canSee('File Test File has been created.');
    $I->amOnPage('/admin/content/files');
    $I->canSee($filename);

    $fids = \Drupal::entityTypeManager()
      ->getStorage('file')
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('uri', "%$filename", 'LIKE')
      ->execute();
    $I->assertNotEmpty($fids);

    $dom = new DOMDocument();
    libxml_use_internal_errors(TRUE);
    $dom->loadHTML($I->grabPageSource());
    libxml_clear_errors();
    $xpath = new DOMXPath($dom);

    /** @var \DOMNodeList $nodes */
    $nodes = $xpath->evaluate("//label[contains(., '$filename')]");
    $input_id = $nodes->item(0)->attributes['for']->value;
    $I->checkOption('#' . $input_id);
    $I->selectOption('Action', 'Delete File');
    $I->click('Apply to selected items');
    $I->canSee('Are you sure you wish to perform');
    $I->canSee($filename);
    $I->click('Execute action');
    $I->canSee('Action processing results: Delete entities');
    $I->amOnPage('/admin/content/files');
    $I->cantSee($filename);

    $fids = \Drupal::entityTypeManager()
      ->getStorage('file')
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('uri', "%$filename", 'LIKE')
      ->execute();
    $I->assertEmpty($fids);
  }

}
