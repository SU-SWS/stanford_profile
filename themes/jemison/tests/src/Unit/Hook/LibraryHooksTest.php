<?php

declare(strict_types=1);

namespace Drupal\Tests\jemison\Unit\Hook;

use Drupal\Core\Extension\ThemeHandlerInterface;
use Drupal\jemison\Hook\LibraryHooks;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * Unit tests for LibraryHooks.
 */
#[Group('jemison')]
#[CoversClass(LibraryHooks::class)]
class LibraryHooksTest extends UnitTestCase {

  /**
   * The hook class under test.
   *
   * @var \Drupal\jemison\Hook\LibraryHooks
   */
  protected LibraryHooks $hooks;

  /**
   * Mocked theme handler.
   *
   * @var \Drupal\Core\Extension\ThemeHandlerInterface
   */
  protected ThemeHandlerInterface $themeHandler;

  /**
   * {@inheritDoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->themeHandler = $this->createMock(ThemeHandlerInterface::class);
    $this->hooks = new LibraryHooks($this->themeHandler);
  }

  /**
   * Libraries belonging to a theme are always left alone.
   */
  public function testLibraryInfoAlterReturnsEarlyForThemeExtension(): void {
    $this->themeHandler->method('themeExists')->with('jemison')->willReturn(TRUE);

    $libraries = ['some_library' => ['css' => []]];
    $this->hooks->libraryInfoAlter($libraries, 'jemison');

    $this->assertSame(['some_library' => ['css' => []]], $libraries);
  }

  /**
   * Libraries belonging to a Stanford module are emptied out.
   */
  public function testLibraryInfoAlterEmptiesStanfordModuleLibraries(): void {
    $this->themeHandler->method('themeExists')->with('stanford_page')->willReturn(FALSE);

    $libraries = ['some_library' => ['css' => []]];
    $this->hooks->libraryInfoAlter($libraries, 'stanford_page');

    $this->assertSame([], $libraries);
  }

  /**
   * Extension names matching jumpstart, react, or ui_pattern are emptied.
   */
  public function testLibraryInfoAlterEmptiesOtherMatchingExtensions(): void {
    $this->themeHandler->method('themeExists')->willReturn(FALSE);

    foreach (['jumpstart', 'react', 'ui_patterns'] as $extension) {
      $libraries = ['some_library' => ['css' => []]];
      $this->hooks->libraryInfoAlter($libraries, $extension);
      $this->assertSame([], $libraries, "Expected libraries to be emptied for extension: $extension");
    }
  }

  /**
   * Extensions that don't match the theme and don't match the pattern are
   * left completely untouched.
   */
  public function testLibraryInfoAlterLeavesUnrelatedExtensionsAlone(): void {
    $this->themeHandler->method('themeExists')->with('core')->willReturn(FALSE);

    $libraries = ['some_library' => ['css' => []]];
    $this->hooks->libraryInfoAlter($libraries, 'core');

    $this->assertSame(['some_library' => ['css' => []]], $libraries);
  }

}
