<?php

declare(strict_types=1);

namespace Drupal\stanford_basic\Hook;

use Drupal\Core\Extension\ThemeSettingsProvider;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Site\Settings;

class PageAttachmentHooks {

  public function __construct(
    protected AccountProxyInterface $currentUser,
    protected RouteMatchInterface $routeMatch,
    protected ThemeSettingsProvider $themeSettingsProvider,
  ) {}

  /**
   * Implements hook_page_attachments_alter().
   *
   * Because hook_page_attachments() is only executed in modules, not themes.
   */
  #[Hook('page_attachments_alter')]
  public function pageAttachmentsAlter(array &$attachments): void {
    if (Settings::get('stanford_profile_hotfix_styles')) {
      $attachments['#attached']['library'][] = 'stanford_basic/hotfix';
    }
    if ($this->currentUser->isAuthenticated()) {
      $attachments['#attached']['library'][] = 'stanford_basic/admin';
    }
    if ($this->routeMatch->getRouteName() == 'user.login') {
      $attachments['#attached']['library'][] = 'stanford_basic/user_login';
    }
    // Check if dropdown menus are activated.
    $attachments['#attached']['drupalSettings']['stanford_basic']['nav_dropdown_enabled'] = (bool) $this->themeSettingsProvider->getSetting('nav_dropdown_enabled', 'stanford_basic');
  }

}
