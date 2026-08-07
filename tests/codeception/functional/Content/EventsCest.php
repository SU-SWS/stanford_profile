<?php

use Codeception\Attribute as CodeceptionAttribute;
use Faker\Factory;
use Drupal\layout_builder\Section;
use Drupal\layout_builder\SectionComponent;
use Facebook\WebDriver\WebDriverElement;

#[CodeceptionAttribute\Group('events')]
class EventsCest {

  /**
   * Faker generator.
   *
   * @var \Faker\Generator $faker
   */
  protected $faker;

  /**
   * EventsCest constructor.
   */
  public function __construct() {
    $this->faker = Factory::create();
  }

  /**
   * Mini calendar display.
   */
  #[CodeceptionAttribute\Group('mini-calendar')]
  public function testMiniCalendar(FunctionalTester $I) {
    $events = [];
    $current_month = (int) date('n');
    for ($i = $current_month; $i < $current_month + 12; $i++) {
      // Use the 10th and 20th of the month to ensure we have events with
      // double-digit days and to avoid the previous/next month days displaying.
      $begin = mktime(0, 0, 0, $i, 10);
      $end = mktime(0, 0, 0, $i, 20);
      $start_time = rand($begin, $end);
      $events[$i] = $I->createEntity([
        'type' => 'stanford_event',
        'title' => $this->faker->words(3, TRUE),
        'su_event_date_time' => [
          'value' => $start_time,
          'end_value' => $start_time,
        ],
      ]);
    }

    /** @var \Drupal\Component\Uuid\UuidInterface $uuid_service */
    $uuid_service = \Drupal::service('uuid');
    $components = [
      new SectionComponent($uuid_service->generate(), 'main', [
        'id' => 'pdb_component:mini_calendar',
        'label' => 'Mini Calendar',
        'label_display' => 'hidden',
        'provider' => 'stanford_profile_helper',
      ]),
    ];
    $layout = [
      ['section' => new Section('jumpstart_ui_one_column', [], $components)],
    ];

    $node = $I->createEntity([
      'type' => 'stanford_page',
      'title' => 'Mini Calendar Page',
      'layout_builder__layout' => $layout,
    ]);
    $I->amOnPage($node->toUrl()->toString());
    $I->canSee('Mini Calendar', 'h2');
    $I->canSee(date('F Y'), '.mini-calendar');

    foreach ($events as $event) {
      $start_timestamp = $event->get('su_event_date_time')
        ->get(0)
        ->get('value')
        ->getString();

      $button_label = date('M jS Y', $start_timestamp);

      $I->waitForElementClickable("button[aria-label='$button_label']");
      $I->wait(1);
      $I->click("[aria-label='$button_label']");
      $I->waitForText($event->label(), 5);
      $I->canSee($event->label(), 'dialog');
      $I->click('Close Dialog');
      $I->click('Next Month');;
    }
  }

  public function testContentType(FunctionalTester $I) {
    [
      $parent_1,
      $parent_2,
      $child_1_1,
      $child_2_1,
      $child_1_2,
      $child_2_2,
    ] = $this->buildTaxonomyTerms($I);

    $node = $I->createEntity([
      'type' => 'stanford_event',
      'title' => $this->faker->words(3, TRUE),
      'su_event_date_time' => [
        'value' => time() + 60 * 60,
        'end_value' => time() + 60 * 60,
      ],
    ]);

    $I->logInWithRole('contributor');

    $I->amOnPage($node->toUrl('edit-form')->toString());

    $I->canSee($parent_1->label(), 'legend');
    $I->canSee($parent_2->label(), 'legend');

    $parent_1_id = preg_replace('@[^a-z0-9_.]+@', '_', mb_strtolower($parent_1->label()));
    $parent_2_id = preg_replace('@[^a-z0-9_.]+@', '_', mb_strtolower($parent_2->label()));

    $I->selectOption("#$parent_1_id select.simpler-select", $child_1_1->label());
    $I->click('Add More', "#$parent_1_id");
    $I->waitForElementVisible("#$parent_1_id [class*='1-target-id'] select.simpler-select");
    $I->selectOption("#$parent_1_id [class*='1-target-id'] select.simpler-select", $child_1_2->label());

    $I->selectOption("#$parent_2_id select.simpler-select", $child_2_1->label());

    $I->waitForElementVisible("#$parent_2_id [class*='--level-1'] select.simpler-select");

    $I->click('Save');
    $I->canSeeInCurrentUrl($node->toUrl()->toString());
    $I->canSee($node->label(), 'h1');

    $paragraph = $I->createEntity([
      'type' => 'stanford_filtered_lists',
      'su_list_headline' => $this->faker->words(3, TRUE),
      'su_filtered_list_view' => [
        'target_id' => 'stanford_events_filtered',
        'display_id' => 'list',
        'arguments' => '',
        'items_to_display' => NULL,
      ],
    ], 'paragraph');
    $page = $I->createEntity([
      'type' => 'stanford_page',
      'title' => $this->faker->words(3, TRUE),
      'su_page_components' => [
        'target_id' => $paragraph->id(),
        'entity' => $paragraph,
      ],
    ]);
    $I->amOnPage($page->toUrl()->toString());
    $I->canSee($node->label(), 'h3');

    $I->checkOption('-' . $child_2_2->label());
    $I->waitForText('No events available');
    $I->cantSee($node->label());

    $I->checkOption($child_2_1->label());
    $I->waitForText($node->label());
    $I->canSee($node->label(), 'h3');
  }

  protected function buildTaxonomyTerms(FunctionalTester $I) {
    $parent_1 = $I->createEntity([
      'vid' => 'event_filters',
      'name' => $this->faker->words(2, TRUE),
      'weight' => 0,
    ], 'taxonomy_term');
    $parent_2 = $I->createEntity([
      'vid' => 'event_filters',
      'name' => $this->faker->words(2, TRUE),
      'weight' => 10,
    ], 'taxonomy_term');

    $child_1_1 = $I->createEntity([
      'vid' => 'event_filters',
      'name' => $this->faker->words(2, TRUE),
      'parent' => $parent_1->id(),
    ], 'taxonomy_term');

    $child_1_2 = $I->createEntity([
      'vid' => 'event_filters',
      'name' => $this->faker->words(2, TRUE),
      'parent' => $parent_1->id(),
    ], 'taxonomy_term');

    $child_2_1 = $I->createEntity([
      'vid' => 'event_filters',
      'name' => $this->faker->words(2, TRUE),
      'parent' => $parent_2->id(),
    ], 'taxonomy_term');

    $child_2_2 = $I->createEntity([
      'vid' => 'event_filters',
      'name' => $this->faker->words(2, TRUE),
      'parent' => $child_2_1->id(),
    ], 'taxonomy_term');
    return [
      $parent_1,
      $parent_2,
      $child_1_1,
      $child_2_1,
      $child_1_2,
      $child_2_2,
    ];
  }

}
