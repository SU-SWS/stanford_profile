<?php

declare(strict_types=1);

namespace Drupal\stanford_basic\Hook;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Path\PathMatcherInterface;
use Drupal\Core\Routing\RedirectDestinationInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Url;

/**
 * @codeCoverageIgnore
 */
class StanfordBasicConfigPageHooks {

  public function __construct(
    protected ConfigFactoryInterface $configFactory,
    protected PathMatcherInterface $pathMatcher,
    protected RedirectDestinationInterface $redirectDestination,
    protected AccountProxyInterface $account
  ) {}

  #[Hook('preprocess_config_pages__stanford_local_footer')]
  public function preprocessConfigPagesLocalFooter(&$variables) {
    $redirectDestination = preg_replace('/\?.*$/', '', $this->redirectDestination->get());
    if ($this->pathMatcher->isFrontPage()) {
      $redirectDestination = '/';
    }

    $login_path = Url::fromRoute('samlauth.saml_controller_login', ['destination' => $redirectDestination]);

    $login_url = $this->account->isAnonymous() ? $login_path->toString() : NULL;
    $lockup_title = $this->configFactory->get('system.site')->get('name');

    $variables['weblogin_url'] = $login_url;
    $variables['lockup_title'] = $lockup_title;
    $variables['#cache']['contexts'][] = 'url.path';
  }

}
