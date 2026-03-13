<?php

use Faker\Factory;
use Codeception\Attribute\Examples;
use Codeception\Example;
use Codeception\Attribute\Group;

/**
 * Anchor nav tests.
 */
#[Group('anchor-nav')]
class AnchorNavCest {

  /**
   * @var \Faker\Generator
   */
  protected $faker;

  public function __construct() {
    $this->faker = Factory::create();
  }

  #[Examples(layout: 'left_anchor_nav')]
  #[Examples(layout: 'left_anchor_no_nav')]
  #[Examples(layout: 'stanford_basic_page_full')]
  #[Examples(layout: 'top_anchor_nav')]
  #[Examples(layout: 'top_anchor_nav_full_width')]
  public function testAnchorNav(FunctionalTester $I, Example $example) {
    $parentTitle = $this->faker->unique()->uuid();
    $parent = $I->createEntity([
      'title' => $this->faker->unique()->uuid(),
      'type' => 'stanford_page',
      'field_menulink' => [
        'menu_name' => 'main',
        'title' => $parentTitle,
        'expanded' => 1,
        'weight' => -99,
        'parent' => NULL,
      ],
    ]);

    $headings = [];
    $text = '';
    for ($j = 0; $j < 5; $j++) {
      $heading = $this->faker->unique()->uuid();
      $headings[] = $heading;
      $text .= '<h2>' . $heading . '</h2>';
      $text .= '<p>' . $this->faker->paragraph(5) . '</p>';
    }

    $nodeTitle = $this->faker->unique()->uuid();
    $node = $I->createEntity([
      'title' => $this->faker->unique()->uuid(),
      'type' => 'stanford_page',
      'layout_selection' => $example['layout'],
      'field_menulink' => [
        'menu_name' => 'main',
        'title' => $nodeTitle,
        'expanded' => 1,
        'weight' => 0,
        'parent' => "menu_link_field:node_field_menulink_{$parent->uuid()}_und",
      ],
      'body' => [
        'value' => $text,
        'format' => 'stanford_html',
      ],
    ]);
    $I->amOnPage($node->toUrl()->toString());
    $I->resizeWindow(1400, 1000);

    if ($example['layout'] == 'stanford_basic_page_full') {
      $I->cantSeeElement('.anchor-link-nav');
      foreach ($headings as $heading) {
        $I->canSee($heading, '.su-wysiwyg-text h2');
        $I->cantSeeLink($heading);
      }
      return;
    }

    $I->canSee($headings[0], '.su-wysiwyg-text h2');
    $I->canSee($headings[0], '.anchor-link-nav');
    $I->canSeeLink($headings[0], "#$headings[0]");
    unset($headings[0]);

    // Top anchor will have other links in the collapsed area.
    if (str_starts_with($example['layout'], 'top_anchor_nav')) {
      $I->click('See More', '.anchor-link-nav');
    }

    foreach ($headings as $heading) {
      $I->canSee($heading, '.su-wysiwyg-text h2');
      $I->canSee($heading, '.anchor-link-nav');
      $I->canSeeLink($heading, "#$heading");
    }
  }

}
