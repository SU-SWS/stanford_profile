<?php

/**
 * Class TextFormatsCest.
 *
 * @group text_formats
 */
class TextFormatsCest {

  /**
   * Basic HTML should have certain configs set.
   */
  public function testBasicHtml(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/config/content/formats/manage/stanford_html');
    $I->canSeeCheckboxIsChecked('Large (480px wide, un-cropped)');
    $I->canSeeCheckboxIsChecked('Large square (480px, cropped)');
    $I->canSeeCheckboxIsChecked('Medium (220px wide, un-cropped)');
    $I->canSeeCheckboxIsChecked('Medium square (220px, cropped)');
    $I->canSeeCheckboxIsChecked('Circle headshot (112px, cropped)');
    $I->canSeeCheckboxIsChecked('Thumb (100px, cropped)');
    $I->canSeeCheckboxIsChecked('Thumb (100px wide, un-cropped)');

    $I->cantSeeCheckboxIsChecked('Full content');
    $I->cantSeeCheckboxIsChecked('Media library');

    $I->amOnPage('/admin/structure/media/manage/image/display/stanford_image_large');
    $I->canSee('Image style: Large (480 wide)');
    $I->amOnPage('/admin/structure/media/manage/image/display/stanford_image_large_square');
    $I->canSee('Image style: Large Square (480x480)');
    $I->amOnPage('/admin/structure/media/manage/image/display/stanford_image_medium');
    $I->canSee('Image style: Medium (220 wide)');
    $I->amOnPage('/admin/structure/media/manage/image/display/stanford_image_medium_square');
    $I->canSee('Image style: Medium Square (220x220)');
    $I->amOnPage('/admin/structure/media/manage/image/display/stanford_image_stanford_circle');
    $I->canSee('Image style: Circle');
    $I->amOnPage('/admin/structure/media/manage/image/display/stanford_image_thumb_square');
    $I->canSee('Image style: Thumbnail Square (100x100)');
    $I->amOnPage('/admin/structure/media/manage/image/display/thumbnail');
    $I->canSee('Image style: Thumbnail (100 wide)');
  }

}
