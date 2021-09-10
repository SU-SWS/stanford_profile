<?php

/**
 * Tests for various media access functionality.
 */
class MediaPermissionsCest {

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

    $I->fillField('Name', 'Foo Bar');
    $I->fillField('Embed Code', 'Lorem Ipsum');
    $I->click('Save');
    $I->canSee('The given embeddable code is not permitted.');
    $code = [
      '<div id="localist-widget-88041469" class="localist-widget"></div>',
      '<script defer type="text/javascript" src="http://stanford.enterprise.localist.com/widget/view?schools=stanford&days=31&num=50&container=localist-widget-88041469&template=modern"></script>',
    ];
    $I->fillField('Embed Code', implode("\n", $code));
    $I->click('Save');
    $I->canSee('Embeddable Foo Bar has been created.');
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

    $I->fillField('Name', 'Foo Bar');
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
