<?php

use Drupal\Core\Serialization\Yaml;
use Faker\Factory;

/**
 * Class SubThemeCest.
 *
 * @group no-parallel
 */
abstract class SubThemeCest {

  /**
   * Human readable name for the theme.
   *
   * @var string
   */
  protected $themeName;

  /**
   * Real path to the theme directory.
   *
   * @var string
   */
  protected $themePath;

  /**
   * Faker service.
   *
   * @var \Faker\Generator
   */
  protected $faker;

  /**
   * SubThemeCest constructor.
   */
  public function __construct() {
    $this->themeName = Factory::create()->firstName;
    $path = \Drupal::service('extension.list.theme')->getPath('stanford_basic');
    $this->themePath = realpath(dirname($path)) . '/' . strtolower($this->themeName);
    $this->faker = Factory::create();
  }

  /**
   * Before the tests, create a stubbed out subtheme.
   *
   * @param \AcceptanceTester $I
   *   Tester.
   */
  public function _before(AcceptanceTester $I) {
    $this->createTheme();
  }

  /**
   * Always cleanup the config after testing.
   *
   * @param \AcceptanceTester $I
   *   Tester.
   */
  public function _after(AcceptanceTester $I) {
    $I->runDrush('config:set system.theme default stanford_basic -y');
    try {
      $I->runDrush('theme:uninstall ' . strtolower($this->themeName));
    }
    catch (\Throwable $e) {
      // Nothing to do if the theme wasn't enabled to begin.
    }
    $info_path = $this->themePath . '/' . strtolower($this->themeName) . '.info.yml';
    if (file_exists($info_path)) {
      unlink($info_path);
      rmdir($this->themePath);
    }
  }

  /**
   * Enable the subtheme and the config should reflect the changes done.
   *
   * @group subtheme
   */
  public function testSubTheme(AcceptanceTester $I) {
    $paragraph_text = $this->faker->paragraph;
    $paragraph = $I->createEntity([
      'type' => 'stanford_wysiwyg',
      'su_wysiwyg_text' => [
        'value' => $paragraph_text,
        'format' => 'stanford_html',
      ],
    ], 'paragraph');
    $node = $I->createEntity([
      'type' => 'stanford_page',
      'title' => $this->faker->words(3, TRUE),
      'su_page_components' => [
        'target_id' => $paragraph->id(),
        'entity' => $paragraph,
      ],
    ]);
    $I->amOnPage($node->toUrl()->toString());
    $I->canSee($node->label(), 'h1');

    $I->canSee($paragraph_text);

    $I->runDrush('theme:enable -y ' . strtolower($this->themeName));
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/appearance');
    $I->click('Set as default', sprintf('a[title="Set %s as default theme"]', $this->themeName));
    $I->canSee("{$this->themeName} 1.0.0 (default theme)");
    $I->runDrush('cache:rebuild');

    $I->amOnPage($node->toUrl()->toString());
    $I->canSee($node->label(), 'h1');
    $I->canSee($paragraph_text);

    $I->runDrush('config-import -y');
    $result = $I->runDrush('config-get system.theme default --format=json');
    $result = json_decode($result, TRUE);
    $I->assertEquals(strtolower($this->themeName), $result['system.theme:default']);

    $I->amOnPage('/admin/appearance');
    $I->click('Set as default', sprintf('a[title="Set %s as default theme"]', 'Stanford Basic'));
    $I->runDrush('config-import -y');

    $result = $I->runDrush('config-get system.theme default --format=json');
    $result = json_decode($result, TRUE);
    $I->assertEquals('stanford_basic', $result['system.theme:default']);

    $I->amOnPage($node->toUrl()->toString());
    $I->canSee($node->label(), 'h1');
    $I->canSee($paragraph_text);
  }

  /**
   * Enable the minimally branded subtheme and the config should reflect the
   * changes done. Test the changes are there.
   *
   * @group minimal-theme
   */
  public function testMinimalSubtheme(AcceptanceTester $I) {
    $I->amOnPage('/');
    $I->seeElement('.su-brand-bar__logo');
    $I->seeElement('.su-global-footer__container');
    $I->seeElement('.su-brand-bar--default');

    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/appearance');
    $I->click('Set as default', 'a[title="Set Stanford Minimally Branded Subtheme as default theme"]');
    $I->amOnPage('/');
    $I->canSeeResponseCodeIs(200);
    $I->seeInSource('<span class="su-lockup__wordmark"></span>');
    $I->dontSeeElement('.su-brand-bar__logo');
    $I->dontSeeElement('.su-global-footer__container');
    $I->dontSeeElement('.su-brand-bar--default');
  }

  /**
   * Create a stub of a subtheme based on stanford_basic.
   */
  protected function createTheme() {
    if (!file_exists("{$this->themePath}/{$this->themeName}.info.yml")) {
      mkdir($this->themePath, 0777, TRUE);
      $info = file_get_contents(\Drupal::service('extension.list.theme')
          ->getPath('stanford_basic') . '/stanford_basic.info.yml');
      $stanford_basic_info = Yaml::decode($info);
      $info = [
        'name' => $this->themeName,
        'type' => 'theme',
        'description' => $this->themeName,
        'package' => 'testing',
        'version' => '1.0.0',
        'core_version_requirement' => '^10',
        'base theme' => 'stanford_basic',
        'regions' => $stanford_basic_info['regions'],
      ];

      $info_path = $this->themePath . '/' . strtolower($this->themeName) . '.info.yml';
      file_put_contents($info_path, Yaml::encode($info));
    }
  }

}
