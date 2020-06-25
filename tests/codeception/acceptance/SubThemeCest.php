<?php

use Drupal\Core\Serialization\Yaml;
use Faker\Factory;

/**
 * Class SubThemeCest.
 */
class SubThemeCest {

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
   * SubThemeCest constructor.
   */
  public function __construct() {
    $this->themeName = Factory::create()->firstName;
    $this->themePath = realpath(dirname(drupal_get_path('theme', 'stanford_basic'))) . '/' . strtolower($this->themeName);
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
    $this->_failed($I);
  }

  /**
   * Always cleanup the config after testing.
   *
   * @param \AcceptanceTester $I
   *   Tester.
   */
  public function _failed(AcceptanceTester $I) {
    $this->runConfigImport($I, TRUE);
    unlink($this->themePath . '/' . strtolower($this->themeName) . '.info.yml');
    rmdir($this->themePath);
  }

  /**
   * Enable the subtheme and the config should reflect the changes done.
   */
  public function testSubTheme(AcceptanceTester $I) {
    $I->runDrush('theme:enable -y ' . strtolower($this->themeName));
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/appearance');
    $I->click('Set as default', sprintf('a[title="Set %s as default theme"]', $this->themeName));
    $I->amOnPage('/');
    $I->canSeeResponseCodeIs(200);

    $this->runConfigImport($I);
    $result = $I->runDrush('config-get system.theme default --format=json');
    $result = json_decode($result, TRUE);
    $I->assertEquals(strtolower($this->themeName), $result['system.theme:default']);

    $I->amOnPage('/admin/appearance');
    $I->click('Set as default', sprintf('a[title="Set %s as default theme"]', 'Stanford Basic'));
    $this->runConfigImport($I);

    $result = $I->runDrush('config-get system.theme default --format=json');
    $result = json_decode($result, TRUE);
    $I->assertEquals('stanford_basic', $result['system.theme:default']);

    $I->amOnPage('/');
    $I->canSeeResponseCodeIs(200);
  }

  /**
   * Run config import and adjust saml module if necessary.
   *
   * @param \AcceptanceTester $I
   *   Tester.
   * @param bool $disable_config_ignore
   *   If config ignore module should be disabled first.
   */
  protected function runConfigImport(AcceptanceTester $I, $disable_config_ignore = FALSE) {
    $drush_response = $I->runDrush('pm-list --filter=name=stanford_ssp --format=json');
    $drush_response = json_decode($drush_response, TRUE);
    $saml_enabled = $drush_response['stanford_ssp']['status'] == 'Enabled';

    if ($disable_config_ignore) {
      $I->runDrush('pmu config_ignore');
    }

    $I->runDrush('config-import -y');
    if (!$saml_enabled) {
      $I->runDrush('pm-uninstall simplesamlphp_auth -y');
    }
  }

  /**
   * Create a stub of a subtheme based on stanford_basic.
   */
  protected function createTheme() {
    if (!file_exists("{$this->themePath}/{$this->themeName}.info.yml")) {
      mkdir($this->themePath, 0777, TRUE);
      $info = file_get_contents(drupal_get_path('theme', 'stanford_basic') . '/stanford_basic.info.yml');
      $info = Yaml::decode($info);
      $info['name'] = $this->themeName;
      $info['base theme'] = 'stanford_basic';
      unset($info['component-libraries']);

      $info_path = $this->themePath . '/' . strtolower($this->themeName) . '.info.yml';
      file_put_contents($info_path, Yaml::encode($info));
    }
  }

}
