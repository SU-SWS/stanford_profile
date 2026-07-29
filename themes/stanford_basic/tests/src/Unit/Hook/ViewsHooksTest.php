<?php

declare(strict_types=1);

namespace Drupal\Tests\stanford_basic\Unit\Hook;

use Drupal\stanford_basic\Hook\ViewsHooks;
use Drupal\Tests\UnitTestCase;
use Drupal\views\ViewExecutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * Unit tests for ViewsHooks.
 */
#[Group('stanford_basic')]
#[CoversClass(ViewsHooks::class)]
class ViewsHooksTest extends UnitTestCase {

  /**
   * The hook class under test.
   *
   * @var \Drupal\stanford_basic\Hook\ViewsHooks
   */
  protected ViewsHooks $hooks;

  /**
   * {@inheritDoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->hooks = new ViewsHooks();
  }

  /**
   * Base view/display classes are always added, and the special policy
   * change library is not attached for an unrelated view/display.
   */
  public function testPreprocessViewsViewAddsBaseClassesOnly(): void {
    $variables = [
      'id' => 'some_view',
      'display_id' => 'page_1',
      'attributes' => [],
    ];
    $this->hooks->preprocessViewsView($variables);

    $this->assertSame(['view', 'some-view', 'page-1'], $variables['attributes']['class']);
    $this->assertArrayNotHasKey('#attached', $variables);
  }

  /**
   * The change_logs/policy_changes view+display combination attaches the
   * stanford_policy library in addition to the base classes.
   */
  public function testPreprocessViewsViewAttachesLibraryForPolicyChanges(): void {
    $variables = [
      'id' => 'change_logs',
      'display_id' => 'policy_changes',
      'attributes' => [],
    ];
    $this->hooks->preprocessViewsView($variables);

    $this->assertSame(['view', 'change-logs', 'policy-changes'], $variables['attributes']['class']);
    $this->assertSame(['stanford_basic/content.stanford_policy'], $variables['#attached']['library']);
  }

  /**
   * Matching the view id but not the display id must not attach the
   * library.
   */
  public function testPreprocessViewsViewDoesNotAttachLibraryWhenOnlyViewIdMatches(): void {
    $variables = [
      'id' => 'change_logs',
      'display_id' => 'other_display',
      'attributes' => [],
    ];
    $this->hooks->preprocessViewsView($variables);

    $this->assertArrayNotHasKey('#attached', $variables);
  }

  /**
   * Matching the display id but not the view id must not attach the
   * library.
   */
  public function testPreprocessViewsViewDoesNotAttachLibraryWhenOnlyDisplayIdMatches(): void {
    $variables = [
      'id' => 'other_view',
      'display_id' => 'policy_changes',
      'attributes' => [],
    ];
    $this->hooks->preprocessViewsView($variables);

    $this->assertArrayNotHasKey('#attached', $variables);
  }

  /**
   * Theme suggestions are built from the view id and current display.
   */
  public function testThemeSuggestionsViewsViewListAlter(): void {
    $view = $this->getMockBuilder(ViewExecutable::class)
      ->disableOriginalConstructor()
      ->onlyMethods(['id'])
      ->getMock();
    $view->method('id')->willReturn('my_view');
    $view->current_display = 'page_1';

    $suggestions = [];
    $variables = ['view' => $view];
    $this->hooks->themeSuggestionsViewsViewListAlter($suggestions, $variables);

    $this->assertSame([
      'views_view_list__my_view',
      'views_view_list__my_view__page_1',
    ], $suggestions);
  }

  /**
   * Existing suggestions are preserved; new ones are appended.
   */
  public function testThemeSuggestionsViewsViewListAlterAppendsToExisting(): void {
    $view = $this->getMockBuilder(ViewExecutable::class)
      ->disableOriginalConstructor()
      ->onlyMethods(['id'])
      ->getMock();
    $view->method('id')->willReturn('another_view');
    $view->current_display = 'default';

    $suggestions = ['views_view_list'];
    $variables = ['view' => $view];
    $this->hooks->themeSuggestionsViewsViewListAlter($suggestions, $variables);

    $this->assertSame([
      'views_view_list',
      'views_view_list__another_view',
      'views_view_list__another_view__default',
    ], $suggestions);
  }

}
