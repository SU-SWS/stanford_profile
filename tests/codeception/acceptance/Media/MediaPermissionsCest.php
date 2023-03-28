<?php

use Faker\Factory;

/**
 * Tests for various media access functionality.
 */
class MediaPermissionsCest {

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
   * Test admin perms
   */
  public function testAdminPerms(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/media/add/embeddable');
    $I->canSeeResponseCodeIs(200);
    $I->canSee('oEmbed URL');
    $I->canSee('Embed Code');
  }

  /**
   * Test site manager perms
   */
  public function testSiteManagerPerms(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $I->amOnPage('/media/add/embeddable');
    $I->canSeeResponseCodeIs(200);
    $I->canSee('oEmbed URL');
    $I->canSee('Embed Code');

    $I->fillField('Name', $this->faker->words(3, TRUE));
    $I->fillField('Embed Code', 'Lorem Ipsum');
    $I->click('Save');
    $I->canSee('The given embeddable code is not permitted.');
    $code = [
      '<div id="localist-widget-88041469" class="localist-widget"></div>',
      '<script defer type="text/javascript" src="http://events.stanford.edu/widget/view?schools=stanford&days=31&num=50&container=localist-widget-88041469&template=modern"></script>',
    ];
    $I->fillField('Embed Code', implode("\n", $code));
    $I->click('Save');
    $I->canSee('has been created.');
  }

  /**
   * Test site editor perms
   */
  public function testSiteEditorPerms(AcceptanceTester $I) {
    $I->logInWithRole('site_editor');
    $I->amOnPage('/media/add/embeddable');
    $I->canSeeResponseCodeIs(200);
    $I->canSee('oEmbed URL');
    $I->canSee('Embed Code');

    $I->fillField('Name', $this->faker->words(3, TRUE));
    $I->fillField('Embed Code', 'Lorem Ipsum');
    $I->click('Save');
    $I->canSee('The given embeddable code is not permitted.');
  }

  /**
   * Test contributor perms
   */
  public function testContributorPerms(AcceptanceTester $I) {
    $I->logInWithRole('contributor');
    $I->amOnPage('/media/add/embeddable');
    $I->canSeeResponseCodeIs(200);
    $I->canSee('oEmbed URL');
    $I->cantSee('Embed Code');
  }

}
