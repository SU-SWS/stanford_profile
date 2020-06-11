<?php

class StanfordEventsImporterCest {

  /**
   * Test configuration form.
   *
   * @depends EnableModule
   */
  public function testForImporterForm(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage("/admin/config/importer/events-importer");
    $I->canSeeResponseCodeIs(200);
    $I->canSee("Stanford Events Importer");
  }

  /**
   * Test cron settings.
   *
   * @depends EnableModule
   */
  public function testForCronSettings(AcceptanceTester $I) {
    $I->logInWithRole("administrator");
    $I->amOnPage("/admin/config/system/cron/jobs");
    $I->canSee("Events Migration");
    $I->amOnPage("/admin/config/system/cron/jobs/manage/stanford_migrate_stanford_events");
    $I->seeCheckboxIsChecked("#edit-status");
  }

}
