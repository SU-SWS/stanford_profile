<?php

declare(strict_types=1);

namespace Drupal\Tests\stanford_basic\Unit\Hook;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\paragraphs\ParagraphInterface;
use Drupal\stanford_basic\Hook\StanfordBasicParagraphHooks;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Group;

/**
 * Unit tests for StanfordBasicParagraphHooks.
 *
 * Only the two new, in-scope methods are covered here:
 * preprocessParagraph() and preprocessParagraphStanfordSpacer().
 */
#[Group('stanford_basic')]
#[CoversMethod(StanfordBasicParagraphHooks::class, 'preprocessParagraph')]
#[CoversMethod(StanfordBasicParagraphHooks::class, 'preprocessParagraphStanfordSpacer')]
class StanfordBasicParagraphHooksTest extends UnitTestCase {

  /**
   * The hook class under test.
   *
   * @var \Drupal\stanford_basic\Hook\StanfordBasicParagraphHooks
   */
  protected StanfordBasicParagraphHooks $hooks;

  /**
   * {@inheritDoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->hooks = new StanfordBasicParagraphHooks();
  }

  /**
   * preprocessParagraph() should add a css class derived from the bundle.
   */
  public function testPreprocessParagraphAddsBundleClass() {
    $paragraph = $this->createMock(ParagraphInterface::class);
    $paragraph->method('bundle')->willReturn('stanford_card');

    $variables = [
      'paragraph' => $paragraph,
      'attributes' => [],
    ];

    $this->hooks->preprocessParagraph($variables);

    $this->assertSame(['ptype-stanford-card'], $variables['attributes']['class']);
  }

  /**
   * The class name should be cleaned/sanitized for CSS identifier use.
   */
  public function testPreprocessParagraphSanitizesBundleName() {
    $paragraph = $this->createMock(ParagraphInterface::class);
    $paragraph->method('bundle')->willReturn('stanford weird bundle!');

    $variables = [
      'paragraph' => $paragraph,
      'attributes' => [],
    ];

    $this->hooks->preprocessParagraph($variables);

    $this->assertSame(['ptype-stanford-weird-bundle'], $variables['attributes']['class']);
  }

  /**
   * When the field doesn't exist on the paragraph, no class is added.
   */
  public function testPreprocessParagraphStanfordSpacerFieldDoesNotExist() {
    $paragraph = $this->createMock(ParagraphInterface::class);
    $paragraph->method('hasField')->with('su_spacer_size')->willReturn(FALSE);
    $paragraph->expects($this->never())->method('get');

    $variables = [
      'elements' => ['#paragraph' => $paragraph],
      'attributes' => [],
    ];

    $this->hooks->preprocessParagraphStanfordSpacer($variables);

    $this->assertArrayNotHasKey('class', $variables['attributes']);
  }

  /**
   * When the field exists but is empty, no class is added.
   */
  public function testPreprocessParagraphStanfordSpacerFieldEmpty() {
    $field = $this->createMock(FieldItemListInterface::class);
    $field->method('isEmpty')->willReturn(TRUE);
    $field->expects($this->never())->method('getString');

    $paragraph = $this->createMock(ParagraphInterface::class);
    $paragraph->method('hasField')->with('su_spacer_size')->willReturn(TRUE);
    $paragraph->method('get')->with('su_spacer_size')->willReturn($field);

    $variables = [
      'elements' => ['#paragraph' => $paragraph],
      'attributes' => [],
    ];

    $this->hooks->preprocessParagraphStanfordSpacer($variables);

    $this->assertArrayNotHasKey('class', $variables['attributes']);
  }

  /**
   * When the field exists and has a value, the class is added.
   */
  public function testPreprocessParagraphStanfordSpacerFieldHasValue() {
    $field = $this->createMock(FieldItemListInterface::class);
    $field->method('isEmpty')->willReturn(FALSE);
    $field->method('getString')->willReturn('large');

    $paragraph = $this->createMock(ParagraphInterface::class);
    $paragraph->method('hasField')->with('su_spacer_size')->willReturn(TRUE);
    $paragraph->method('get')->with('su_spacer_size')->willReturn($field);

    $variables = [
      'elements' => ['#paragraph' => $paragraph],
      'attributes' => [],
    ];

    $this->hooks->preprocessParagraphStanfordSpacer($variables);

    $this->assertSame(['large'], $variables['attributes']['class']);
  }

}
