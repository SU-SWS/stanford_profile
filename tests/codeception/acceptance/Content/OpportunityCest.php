<?php

use Codeception\Attribute as CodeceptionAttribute;
use Codeception\Example;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Faker\Factory;

/**
 * Test opportunity content type.
 */
#[CodeceptionAttribute\Group('opportunity')]
class OpportunityCest {

  /**
   * Faker.
   *
   * @var \Faker\Generator
   */
  protected $faker;

  /**
   * Test Constructor.
   */
  public function __construct() {
    $this->faker = Factory::create();
  }

  public function testContentAccess(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $I->amOnPage('/node/add');
    $I->canSee('Opportunity');
    $I->amOnPage('/admin/structure/taxonomy');
    $I->canSee('Opportunity');
    $I->amOnPage('/admin/structure/taxonomy/manage/opportunity_tag_filters/add');
    $I->canSeeInField('Name', '');
    $I->amOnPage('/admin/structure/taxonomy/manage/opportunity_sponsor/add');
    $I->canSeeInField('Name', '');
    $I->amOnPage('/admin/structure/taxonomy/manage/opportunity_type/add');
    $I->canSeeInField('Name', '');
  }

  public function sidebarDataProvider() {
    return [
      ['code' => [], 'units' => [], 'contact' => '', 'deadline' => NULL],
      [
        'code' => [$this->faker->word(), $this->faker->word()],
        'units' => [],
        'contact' => '',
        'deadline' => NULL,
      ],
      [
        'code' => [],
        'units' => [$this->faker->word(), $this->faker->word()],
        'contact' => '',
        'deadline' => NULL,
      ],
      [
        'code' => [$this->faker->word(), $this->faker->word()],
        'units' => [$this->faker->word(), $this->faker->word()],
        'contact' => '',
        'deadline' => NULL,
      ],
      [
        'code' => [$this->faker->word(), $this->faker->word()],
        'units' => [$this->faker->word(), $this->faker->word()],
        'contact' => $this->faker->name(),
        'deadline' => date(DateTimeItemInterface::DATETIME_STORAGE_FORMAT),
      ],
    ];
  }

  #[CodeceptionAttribute\DataProvider('sidebarDataProvider')]
  public function testSidebarFields(AcceptanceTester $I, Example $example) {
    $unit_ids = [];
    foreach ($example['units'] as $unit) {
      $code = $I->createEntity([
        'name' => $unit,
        'vid' => 'opportunity_units',
      ], 'taxonomy_term');
      $unit_ids[] = $code->id();
    }
    $node = $I->createEntity([
      'type' => 'stanford_opportunity',
      'title' => $this->faker->words(3, TRUE),
      'su_opp_course_code' => $example['code'],
      'su_opp_units' => $unit_ids,
      'su_opp_contact_name' => $example['contact'],
      'su_opp_application_deadline' => $example['deadline'],
    ]);
    $I->amOnPage($node->toUrl()->toString());
    $I->canSee($node->label(), 'h1');

    foreach ($example['code'] as $code) {
      $I->canSee($code);
    }

    if ($example['units']) {
      $I->canSee('Units');
    }
    else {
      $I->cantSee('Units');
    }

    foreach ($example['units'] as $code) {
      $I->canSee($code);
    }

    if ($example['contact']) {
      $I->canSee('Contact', 'h2');
      $I->canSee($example['contact']);
    }
    else {
      $I->cantSee('Contact', 'h2');
    }

    if ($example['deadline']) {
      $I->canSee('Application Deadline', 'h2');
    }
    else {
      $I->cantSee('Application Deadline');
    }
  }

}
