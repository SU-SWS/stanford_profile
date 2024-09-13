<?php

use Drupal\user\Entity\Role;
use Faker\Factory;

/**
 * Test the restrictions on authenticated users.
 */
class AuthenticatedPermissionsCest {

  /**
   * Faker generator.
   *
   * @var \Faker\Generator
   */
  protected $faker;

  /**
   * Test consturctor.
   */
  public function __construct() {
    $this->faker = Factory::create();
  }

  /**
   * Set up a file to test PHP injection.
   */
  public function _before(AcceptanceTester $I) {
    $dir = rtrim(codecept_data_dir(), '/');
    $file = "$dir/injection.php";
    if (!file_exists($dir)) {
      mkdir($dir, 0777, TRUE);
    }
    if (!file_exists($file)) {
      file_put_contents($file, '<?php echo("injection test"); die(); ?>');
    }
  }

  /**
   * Remove the php injection file.
   */
  public function _after(AcceptanceTester $I) {
    $file = rtrim(codecept_data_dir(), '/') . '/injection.php';
    if (file_exists($file)) {
      unlink($file);
    }
  }

  /**
   * Make sure authenticated users can't access things they should not.
   */
  public function testAuthenticatedUserRestrictions(AcceptanceTester $I) {
    $I->logInWithRole('authenticated');
    $I->amOnPage('/');
    $I->canSeeResponseCodeIs(200);
    $I->amOnPage('/admin');
    $I->canSeeResponseCodeIs(403);
    $I->amOnPage('/admin/content');
    $I->canSeeResponseCodeIs(403);
    $I->amOnPage('/admin/structure');
    $I->canSeeResponseCodeIs(403);
    $I->amOnPage('/admin/appearance');
    $I->canSeeResponseCodeIs(403);
    $I->amOnPage('/admin/modules');
    $I->canSeeResponseCodeIs(403);
    $I->amOnPage('/admin/config');
    $I->canSeeResponseCodeIs(403);
    $I->amOnPage('/admin/users');
    $I->canSeeResponseCodeIs(403);
    $I->amOnPage('/admin/reports');
    $I->canSeeResponseCodeIs(403);
    $I->amOnPage('/admin/reports/status');
    $I->canSeeResponseCodeIs(403);
    $I->amOnPage('/admin/patterns');
    $I->canSeeResponseCodeIs(403);
  }

  /**
   * Site Manager cannot escalate their own role above Site Manager.
   */
  public function testSiteManagerEscalationSelf(AcceptanceTester $I) {
    $site_manager = $I->logInWithRole('site_manager');
    $I->amOnPage($site_manager->toUrl('edit-form')->toString());

    $I->dontSee('Administrator');
    $I->dontSee('Site Builder');
    $I->dontSee('Site Developer');
  }

  /**
   * Site Manager cannot escalate others' role above Site Manager.
   */
  public function testSiteManagerEscalationOthers(AcceptanceTester $I) {
    $name = $this->faker->words(3, TRUE);
    $user = $I->createEntity(['name' => $name], 'user');
    $I->logInWithRole('site_manager');
    $I->amOnPage($user->toUrl('edit-form')->toString());
    $I->canSeeInField('Username', $name);
    $I->dontSee('Administrator');
    $I->dontSee('Site Builder');
    $I->dontSee('Site Developer');
  }

  /**
   * PHP code is not allowed in redirects.
   */
  public function testPhpInRedirect(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/config/search/redirect/add');
    $I->fillField('#edit-redirect-source-0-path', 'home');
    $I->fillField('#edit-redirect-redirect-0-uri', '<?php echo("injection"); ?>');
    $I->click('Save');
    $I->dontSee('injection');
    $I->see('Manually entered paths should start with one of the following characters:');
  }

  /**
   * PHP code is escaped and not run when added to content.
   */
  public function testPhpInContent(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $I->amOnPage('/node/add/stanford_page');
    $I->fillField('#edit-title-0-value', '<?php echo("injection test"); die(); ?>');
    $I->click('Save');
    $I->seeInCurrentUrl('node');
    $I->seeElement('.su-global-footer__copyright');
  }

  /**
   * PHP files are not allowed as uploads for favicons.
   */
  public function testPhpUploadInFavicon(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/appearance/settings');
    $I->seeCheckboxIsChecked('#edit-default-favicon');
    $I->uncheckOption('#edit-default-favicon');
    $I->see('Upload favicon image');
    $I->attachFile('Upload favicon image', 'injection.php');
    $I->click('#edit-submit');
    $I->see('Only files with the following extensions are allowed');
    $I->checkOption('#edit-default-favicon');
    $I->click('#edit-submit');
  }

  /**
   * PHP files are not allowed as uploads for the logo.
   */
  public function testPhpUploadInLogo(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/appearance/settings');
    $I->seeCheckboxIsChecked('Use the logo supplied by the theme');
    $I->uncheckOption('Use the logo supplied by the theme');
    $I->see('Upload logo image');
    $I->attachFile('Upload logo image', 'injection.php');
    $I->click('Save configuration');
    $I->see('Only files with the following extensions are allowed');
  }

  /**
   * Vocabs aren't seen if there are no permissions for them.
   */
  public function testTaxonomyOverviewPage(AcceptanceTester $I) {
    $name = $this->faker->firstName;
    $vocab = $I->createEntity([
      'vid' => strtolower($name),
      'name' => $name,
    ], 'taxonomy_vocabulary');
    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/structure/taxonomy');
    $I->cantSee($vocab->label());

    Role::load('site_manager')
      ->grantPermission('create terms in ' . $vocab->id())
      ->save();
    $I->amOnPage('/admin/structure/taxonomy');
    $I->canSee($vocab->label());
  }

}
