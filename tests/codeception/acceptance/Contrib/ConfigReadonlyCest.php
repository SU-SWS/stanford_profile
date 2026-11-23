<?php

use Codeception\Attribute as CodeceptionAttribute;

/**
 * Tests that the config readonly module is enabled.
 */
#[CodeceptionAttribute\Group('config_readonly')]
#[CodeceptionAttribute\Group('contrib')]
// phpcs:ignore DrupalPractice.General.ClassName.ClassPrefix
class ConfigReadonlyCest {

  /**
   * Config readonly module should be enabled.
   */
  public function testConfigReadonlyModule(AcceptanceTester $I) {
    $drush_result = $I->runDrush('pm-list --filter=name=config_readonly --format=json');
    $drush_result = json_decode($drush_result, TRUE);
    $I->assertEquals('Enabled', $drush_result['config_readonly']['status']);
  }

}
