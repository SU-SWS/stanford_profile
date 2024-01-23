<?php

use Faker\Factory;

/**
 * Test the page title banner paragraph.
 *
 * @group page-title-banner
 */
class PageTitleBannerCest {

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
   * Test the title banner title is auto generated.
   */
  public function testAutoTitle(AcceptanceTester $I) {
    $this->prepareImage();
    $node = $I->createEntity([
      'type' => 'stanford_page',
      'title' => $this->faker->words(3, TRUE),
    ]);
    $file = $I->createEntity(['uri' => $this->logoPath], 'file');
    $media = $I->createEntity([
      'bundle' => 'image',
      'field_media_image' => ['target_id' => $file->id()],
    ], 'media');
    $I->logInWithRole('contributor');
    $I->amOnPage($node->toUrl('edit-form')->toString());
    $I->seeInField('Title', $node->label());
    $I->click('Add Page Title Banner');
    $I->fillField('[name="su_page_banner[0][subform][su_title_banner_image][media_library_selection]"]', $media->id());
    $I->click('Update widget');
    $I->click('Save');
    $I->canSeeInCurrentUrl($node->toUrl()->toString());
    $I->canSee($node->label(), '.ptype-stanford-page-title-banner h1');
    $I->canSeeElement('.ptype-stanford-page-title-banner img');

    // Edit the node and change the title.
    $I->amOnPage($node->toUrl('edit-form')->toString());
    $new_title = $this->faker->words(4, TRUE);
    $I->fillField('Title', $new_title);
    $I->click('Save');
    $I->canSee($new_title, '.ptype-stanford-page-title-banner h1');
  }

}
