<?php

declare(strict_types=1);

namespace Drupal\stanford_basic\Hook;

use Drupal\Component\Utility\Html;
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

    /** @var \Drupal\config_pages\ConfigPagesInterface $config_page */
    $config_page = $variables['config_pages'];

    foreach ($variables['content']['su_local_foot_social'][0]['#items'] as &$link) {
      $url = $link['#url']->toString();
      $host = explode('.', parse_url($url, PHP_URL_HOST));
      $host = count($host) == 2 ? $host[0] : $host[1];
      $link['#attributes']['class'][] = Html::cleanCssIdentifier("su-local-footer__social-$host");
      $link['#title'] = [
        ['#type' => 'html_tag', '#tag' => 'i'],
        ['#type' => 'html_tag', '#tag' => 'span', '#value' => $link['#title']],
      ];
    }
    $custom_lockup = !$config_page->get('su_local_foot_use_loc')?->getString();
    $lockup = $config_page->get('su_local_foot_loc_op')?->getString();
    $use_default_logo = !!$config_page->get('su_local_foot_use_logo')
      ?->getString();
    $login_url = $this->account->isAnonymous() ? $login_path->toString() : NULL;
    $lockup_title = $this->configFactory->get('system.site')->get('name');

    $variables['component'] = [
      'custom_lockup' => $custom_lockup,
      'lockup_option' => $lockup,
      'use_default_logo' => $use_default_logo,
      'weblogin_url' => $login_url,
      'lockup_title' => $lockup_title,
    ];
    $variables['#cache']['contexts'][] = 'url.path';
  }

  #[Hook('preprocess_config_pages__stanford_global_message')]
  public function preprocessGlobalMessage(&$variables) {
    /** @var \Drupal\config_pages\ConfigPagesInterface $config_page */
    $config_page = $variables['config_pages'];
    $variables['component'] = [
      'level' => $config_page->get('su_global_msg_type')->getString(),
    ];
  }

}
