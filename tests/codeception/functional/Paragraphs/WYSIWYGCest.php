<?php

use Drupal\paragraphs\ParagraphInterface;
use Faker\Factory;

/**
 * Class WYSIWYGCest.
 *
 * @group paragraphs
 * @group wysiwyg
 */
class WYSIWYGCest {

  /**
   * HTML should be properly stripped.
   */
  public function testFilteredHtml(FunctionalTester $I) {


    $paragraph = $I->createEntity([
      'type' => 'stanford_wysiwyg',
      'su_wysiwyg_text' => [
        'format' => 'stanford_html',
        'value' => file_get_contents(__DIR__ . '/WYSIWYG.html'),
      ],
    ], 'paragraph');
    $node = $this->getNodeWithParagraph($I, $paragraph);
    $I->logInWithRole('administrator');
    $I->amOnPage($node->toUrl()->toString());

    # Stripped Tags
    $I->cantSee("alert('testme')");

    $I->cantSeeElement('iframe');
    $I->cantSeeElement('form');
    $I->cantSeeElement('label');
    $I->cantSeeElement('input');
    $I->cantSeeElement('select');
    $I->cantSeeElement('option');
    $I->cantSeeElement('textarea');
    $I->cantSeeElement('fieldset');
    $I->cantSeeElement('legend');
    $I->cantSeeElement('address');

    # Headers
    $I->cantSee('Level 01 heading', 'h1');
    $I->canSee('Level 02 Heading', 'h2');
    $I->canSee('Level 03 Heading', 'h3');
    $I->canSee('Level 04 Heading', 'h4');
    $I->canSee('Level 05 Heading', 'h5');
    $I->cantSeeElement('h6');


    # Text Tags
    $I->canSee('A small paragraph', 'p');
    $I->canSee('Normal Link', 'a');
    $I->canSee('Button', 'a.su-button');
    $I->canSee('Big Button', 'a.su-button--big');
    $I->canSee('Secondary Button', 'a.su-button--secondary');
    $I->canSee('emphasis', 'em');
    $I->canSee('important', 'strong');
    $I->canSeeNumberOfElements('blockquote', 1);
    $I->cantSeeElement('.su-page-components footer');
    $I->canSeeNumberOfElements('code', 2);
    $I->canSeeNumberOfElements('dl', 1);
    $I->canSeeNumberOfElements('dt', 2);
    $I->canSeeNumberOfElements('dd', 2);

    # List Tags
    $I->canSee('This is a list', 'ul li');
    $I->canSee('child list items', 'ul ul li');
    $I->canSee('Ordered list item', 'ol li');
    $I->canSee('Child ordered list item', 'ol ol li');

    # Table Tags
    $I->canSeeElement('table');
    $I->canSeeNumberOfElements('caption', 1);
    $I->canSeeNumberOfElements('caption', 1);
    $I->canSeeNumberOfElements('tbody', 1);
    $I->canSeeNumberOfElements('tr', 3);
    $I->canSeeNumberOfElements('th[scope]', 2);
    $I->canSeeNumberOfElements('td', 4);
  }

  /**
   * Images in the WYSIWYG should display correctly.
   */
  public function testEmbeddedImage(AcceptanceTester $I) {

  }

  /**
   * Videos in the WYSIWYG should display correctly.
   */
  public function testEmbeddedVideo(AcceptanceTester $I) {

  }

  /**
   * Documents in the WYSIWYG should display correctly.
   */
  public function testEmbeddedDocument(AcceptanceTester $I) {

  }

  protected function getNodeWithParagraph(FunctionalTester $I, ParagraphInterface $paragraph) {
    $faker = Factory::create();

    return $I->createEntity([
      'type' => 'stanford_page',
      'title' => $faker->text(30),
      'su_page_components' => [
        'target_id' => $paragraph->id(),
        'entity' => $paragraph,
        'settings' => json_encode([
          'row' => 0,
          'index' => 0,
          'width' => 12,
          'admin_title' => 'Banner',
        ]),
      ],
    ]);
  }

}
