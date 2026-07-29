<?php

declare(strict_types=1);

namespace Drupal\Tests\stanford_basic\Unit\Hook;

use Drupal\Core\Extension\ThemeSettingsProvider;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\stanford_basic\Hook\PageAttachmentHooks;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * Unit tests for PageAttachmentHooks.
 */
#[Group('stanford_basic')]
#[CoversClass(PageAttachmentHooks::class)]
class PageAttachmentHooksTest extends UnitTestCase {

  /**
   * Mocked current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected AccountProxyInterface $currentUser;

  /**
   * Mocked route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected RouteMatchInterface $routeMatch;

  /**
   * Mocked theme settings provider.
   *
   * @var \Drupal\Core\Extension\ThemeSettingsProvider
   */
  protected ThemeSettingsProvider $themeSettingsProvider;

  /**
   * The hook class under test.
   *
   * @var \Drupal\stanford_basic\Hook\PageAttachmentHooks
   */
  protected PageAttachmentHooks $hooks;

  /**
   * {@inheritDoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->currentUser = $this->createMock(AccountProxyInterface::class);
    $this->routeMatch = $this->createMock(RouteMatchInterface::class);
    $this->themeSettingsProvider = $this->createMock(ThemeSettingsProvider::class);
    $this->hooks = new PageAttachmentHooks($this->currentUser, $this->routeMatch, $this->themeSettingsProvider);
  }

  /**
   * Anonymous users on a non-login route with dropdowns disabled get no
   * extra libraries attached and a FALSE drupalSettings flag.
   */
  public function testPageAttachmentsAlterAnonymousNonLoginDropdownDisabled(): void {
    $this->currentUser->method('isAuthenticated')->willReturn(FALSE);
    $this->routeMatch->method('getRouteName')->willReturn('some.other.route');
    $this->themeSettingsProvider->method('getSetting')
      ->with('nav_dropdown_enabled', 'stanford_basic')
      ->willReturn(FALSE);

    $attachments = [];
    $this->hooks->pageAttachmentsAlter($attachments);

    $this->assertArrayNotHasKey('library', $attachments['#attached']);
    $this->assertFalse($attachments['#attached']['drupalSettings']['stanford_basic']['nav_dropdown_enabled']);
  }

  /**
   * Authenticated users get the admin library attached.
   */
  public function testPageAttachmentsAlterAuthenticatedUser(): void {
    $this->currentUser->method('isAuthenticated')->willReturn(TRUE);
    $this->routeMatch->method('getRouteName')->willReturn('some.other.route');
    $this->themeSettingsProvider->method('getSetting')->willReturn(FALSE);

    $attachments = [];
    $this->hooks->pageAttachmentsAlter($attachments);

    $this->assertContains('stanford_basic/admin', $attachments['#attached']['library']);
    $this->assertNotContains('stanford_basic/user_login', $attachments['#attached']['library']);
  }

  /**
   * The user login route gets the user_login library attached.
   */
  public function testPageAttachmentsAlterUserLoginRoute(): void {
    $this->currentUser->method('isAuthenticated')->willReturn(FALSE);
    $this->routeMatch->method('getRouteName')->willReturn('user.login');
    $this->themeSettingsProvider->method('getSetting')->willReturn(FALSE);

    $attachments = [];
    $this->hooks->pageAttachmentsAlter($attachments);

    $this->assertContains('stanford_basic/user_login', $attachments['#attached']['library']);
    $this->assertNotContains('stanford_basic/admin', $attachments['#attached']['library']);
  }

  /**
   * Both libraries are attached when the user is authenticated and on the
   * login route, and the dropdown setting is cast to a boolean TRUE.
   */
  public function testPageAttachmentsAlterAuthenticatedOnLoginRouteWithDropdownEnabled(): void {
    $this->currentUser->method('isAuthenticated')->willReturn(TRUE);
    $this->routeMatch->method('getRouteName')->willReturn('user.login');
    $this->themeSettingsProvider->method('getSetting')
      ->with('nav_dropdown_enabled', 'stanford_basic')
      ->willReturn(1);

    $attachments = [];
    $this->hooks->pageAttachmentsAlter($attachments);

    $this->assertContains('stanford_basic/admin', $attachments['#attached']['library']);
    $this->assertContains('stanford_basic/user_login', $attachments['#attached']['library']);
    $this->assertTrue($attachments['#attached']['drupalSettings']['stanford_basic']['nav_dropdown_enabled']);
  }

}
