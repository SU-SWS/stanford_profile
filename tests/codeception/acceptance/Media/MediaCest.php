<?php

/**
 * Tests for various media functionality.
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
    $I->canSeeCheckboxIsChecked('#edit-source-configuration-providers-arcgis-storymaps');
    $I->canSeeCheckboxIsChecked('#edit-source-configuration-providers-circuitlab');
    $I->canSeeCheckboxIsChecked('#edit-source-configuration-providers-dailymotion');
    $I->canSeeCheckboxIsChecked('#edit-source-configuration-providers-facebook');
    $I->canSeeCheckboxIsChecked('#edit-source-configuration-providers-flickr');
    $I->canSeeCheckboxIsChecked('#edit-source-configuration-providers-getty-images');
    $I->canSeeCheckboxIsChecked('#edit-source-configuration-providers-instagram');
    $I->canSeeCheckboxIsChecked('#edit-source-configuration-providers-issuu');
    $I->canSeeCheckboxIsChecked('#edit-source-configuration-providers-livestream');
    $I->canSeeCheckboxIsChecked('#edit-source-configuration-providers-mathembed');
    $I->canSeeCheckboxIsChecked('#edit-source-configuration-providers-simplecast');
    $I->canSeeCheckboxIsChecked('#edit-source-configuration-providers-slideshare');
    $I->canSeeCheckboxIsChecked('#edit-source-configuration-providers-soundcloud');
    $I->canSeeCheckboxIsChecked('#edit-source-configuration-providers-spotify');
    $I->canSeeCheckboxIsChecked('#edit-source-configuration-providers-stanford-digital-repository');
    $I->canSeeCheckboxIsChecked('#edit-source-configuration-providers-twitter');

    $I->cantSeeCheckboxIsChecked('#edit-source-configuration-providers-codepen');
  }

  /**
   * Embeddable fields set right.
   */
  public function testForEmbeddableFields(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/structure/media/manage/embeddable');
    $I->seeOptionIsSelected('#edit-source-configuration-oembed-field-name', 'media.field_media_embeddable_oembed');
    $I->seeOptionIsSelected('#edit-source-configuration-unstructured-field-name', 'media.field_media_embeddable_code');
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

}
