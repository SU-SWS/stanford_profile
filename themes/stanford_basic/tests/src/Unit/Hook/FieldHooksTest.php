<?php

declare(strict_types=1);

namespace Drupal\Tests\stanford_basic\Unit\Hook;

use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\paragraphs\ParagraphInterface;
use Drupal\stanford_basic\Hook\FieldHooks;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * Unit tests for FieldHooks.
 */
#[Group('stanford_basic')]
#[CoversClass(FieldHooks::class)]
class FieldHooksTest extends UnitTestCase {

  /**
   * The hook class under test.
   *
   * @var \Drupal\stanford_basic\Hook\FieldHooks
   */
  protected FieldHooks $hooks;

  /**
   * {@inheritDoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->hooks = new FieldHooks();
  }

  /**
   * Builds a mocked entity whose get() calls are driven by a value map.
   *
   * @param array $map
   *   Keyed by field name, value returned by getString() for that field.
   *   A value of NULL means ->get() itself returns NULL (nullsafe chain).
   */
  protected function mockEntity(array $map): FieldableEntityInterface {
    $entity = $this->createMock(FieldableEntityInterface::class);
    $entity->method('get')->willReturnCallback(function ($field_name) use ($map) {
      if (!array_key_exists($field_name, $map) || $map[$field_name] === NULL) {
        return NULL;
      }
      $field = $this->createMock(FieldItemListInterface::class);
      $field->method('getString')->willReturn($map[$field_name]);
      return $field;
    });
    return $entity;
  }

  /**
   * When no heading level is set, the default 'h2' is used and no dot means
   * a trailing dot is appended, resulting in an empty classes string.
   */
  public function testPreprocessFieldSuStatHeadlineDefaultHeading(): void {
    $entity = $this->mockEntity(['su_stat_headline_lvl' => NULL, 'su_stat_heading_hide' => NULL]);

    $variables = [
      'element' => ['#object' => $entity],
      'items' => [0 => ['content' => []]],
    ];
    $this->hooks->preprocessFieldSuStatHeadline($variables);

    $this->assertSame('h2', $variables['items'][0]['content']['#tag']);
    $this->assertSame([''], $variables['items'][0]['content']['#attributes']['class']);
  }

  /**
   * A custom heading level with a dot-separated class list is split up.
   */
  public function testPreprocessFieldSuStatHeadlineCustomHeadingWithClasses(): void {
    $entity = $this->mockEntity([
      'su_stat_headline_lvl' => 'h3.foo.bar',
      'su_stat_heading_hide' => NULL,
    ]);

    $variables = [
      'element' => ['#object' => $entity],
      'items' => [0 => ['content' => []]],
    ];
    $this->hooks->preprocessFieldSuStatHeadline($variables);

    $this->assertSame('h3', $variables['items'][0]['content']['#tag']);
    $this->assertSame(['foo', 'bar'], $variables['items'][0]['content']['#attributes']['class']);
  }

  /**
   * When su_stat_heading_hide is set, the visually-hidden class is appended.
   */
  public function testPreprocessFieldSuStatHeadlineHiddenHeading(): void {
    $entity = $this->mockEntity([
      'su_stat_headline_lvl' => 'div.foo',
      'su_stat_heading_hide' => '1',
    ]);

    $variables = [
      'element' => ['#object' => $entity],
      'items' => [0 => ['content' => []]],
    ];
    $this->hooks->preprocessFieldSuStatHeadline($variables);

    $this->assertSame('div', $variables['items'][0]['content']['#tag']);
    $this->assertSame(['foo', 'visually-hidden'], $variables['items'][0]['content']['#attributes']['class']);
  }

  /**
   * The fontawesome icon field name is copied onto the icon render array.
   */
  public function testPreprocessFieldFontawesomeIcon(): void {
    $variables = [
      'element' => ['#field_name' => 'field_icon'],
      'items' => [0 => ['content' => ['#icons' => [0 => []]]]],
    ];
    $this->hooks->preprocessFieldFontawesomeIcon($variables);

    $this->assertSame('field_icon', $variables['items'][0]['content']['#icons'][0]['#field_name']);
  }

  /**
   * When the field name variable is present, a theme suggestion is added.
   */
  public function testThemeSuggestionsFontawesomeiconsAlterWithFieldName(): void {
    $suggestions = [];
    $variables = ['icons' => [0 => ['#field_name' => 'field_icon']]];
    $this->hooks->themeSuggestionsFontawesomeiconsAlter($suggestions, $variables);

    $this->assertSame(['fontawesomeicons__field_icon'], $suggestions);
  }

  /**
   * When the field name variable is absent, no suggestion is added.
   */
  public function testThemeSuggestionsFontawesomeiconsAlterWithoutFieldName(): void {
    $suggestions = ['existing'];
    $variables = [];
    $this->hooks->themeSuggestionsFontawesomeiconsAlter($suggestions, $variables);

    $this->assertSame(['existing'], $suggestions);
  }

  /**
   * Builds the base set of $variables passed to preprocessField().
   */
  protected function baseFieldVariables(): array {
    return [
      'element' => ['#bundle' => 'basic_page'],
      'entity_type' => 'node',
      'bundle' => 'basic_page',
      'field_name' => 'field_body',
      'field_type' => 'text_long',
      'label_display' => 'above',
      'attributes' => [],
    ];
  }

  /**
   * Non-paragraph fields only get the standard attribute classes.
   */
  public function testPreprocessFieldNonParagraphField(): void {
    $variables = $this->baseFieldVariables();
    $this->hooks->preprocessField($variables, 'field');

    $this->assertSame('basic_page', $variables['bundle']);
    $this->assertSame([
      'node',
      'basic-page',
      'field-body',
      'text-long',
      'label-above',
    ], $variables['attributes']['class']);
    // No items key at all, so nothing paragraph-specific happens.
    $this->assertArrayNotHasKey('items', $variables);
  }

  /**
   * A field of type entity_reference_revisions whose first item is not
   * flagged as a paragraph does not get paragraph classes added.
   */
  public function testPreprocessFieldEntityReferenceRevisionsNonParagraph(): void {
    $variables = $this->baseFieldVariables();
    $variables['field_type'] = 'entity_reference_revisions';
    $variables['element'][0] = [];
    $variables['items'] = [
      0 => ['content' => []],
    ];

    $this->hooks->preprocessField($variables, 'field');

    $this->assertArrayNotHasKey('attributes', $variables['items'][0]);
  }

  /**
   * A paragraph field with no items does nothing paragraph-specific either.
   */
  public function testPreprocessFieldParagraphWithNoItems(): void {
    $variables = $this->baseFieldVariables();
    $variables['field_type'] = 'entity_reference_revisions';
    $variables['element'][0] = ['#paragraph' => TRUE];
    $variables['items'] = [];

    $this->hooks->preprocessField($variables, 'field');

    $this->assertSame([], $variables['items']);
  }

  /**
   * Happy path: a paragraph field with items gets paragraph-item classes
   * and a ptype-* class derived from the paragraph bundle.
   */
  public function testPreprocessFieldParagraphHappyPath(): void {
    $paragraph = $this->createMock(ParagraphInterface::class);
    $paragraph->method('getType')->willReturn('stanford card!');

    $variables = $this->baseFieldVariables();
    $variables['field_type'] = 'entity_reference_revisions';
    $variables['element'][0] = ['#paragraph' => TRUE];
    $variables['items'] = [
      0 => ['content' => ['#paragraph' => $paragraph]],
    ];

    $this->hooks->preprocessField($variables, 'field');

    $this->assertSame(
      ['paragraph-item', 'ptype-stanford-card'],
      $variables['items'][0]['attributes']['class']
    );
  }

  /**
   * When the item already has classes, the paragraph classes are appended
   * rather than overwriting the existing ones.
   */
  public function testPreprocessFieldParagraphAppendsToExistingClasses(): void {
    $paragraph = $this->createMock(ParagraphInterface::class);
    $paragraph->method('getType')->willReturn('stanford_card');

    $variables = $this->baseFieldVariables();
    $variables['field_type'] = 'entity_reference_revisions';
    $variables['element'][0] = ['#paragraph' => TRUE];
    $variables['items'] = [
      0 => [
        'content' => ['#paragraph' => $paragraph],
        'attributes' => ['class' => ['pre-existing']],
      ],
    ];

    $this->hooks->preprocessField($variables, 'field');

    $this->assertSame(
      ['pre-existing', 'paragraph-item', 'ptype-stanford-card'],
      $variables['items'][0]['attributes']['class']
    );
  }

}
