<?php

/**
 * Class ConfigReadonlyCest.
 *
 * @group config_readonly
 * @group contrib
 */
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
