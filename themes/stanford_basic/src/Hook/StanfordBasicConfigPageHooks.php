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

    $login_url = $this->account->isAnonymous() ? $login_path->toString() : NULL;
    $lockup_title = $this->configFactory->get('system.site')->get('name');

    $variables['component'] = [
      'weblogin_url' => $login_url,
      'lockup_title' => $lockup_title,
    ];
    $variables['#cache']['contexts'][] = 'url.path';
  }

}
