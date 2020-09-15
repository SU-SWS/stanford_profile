<?php

namespace Drupal\cardinal_service_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Path\PathMatcherInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Provides a 'User links' block.
 *
 * @Block(
 *  id = "user_links",
 *  admin_label = @Translation("User Links block")
 * )
 */
class UserLinksBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Current user or anonymous.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Path matcher service.
   *
   * @var \Drupal\Core\Path\PathMatcherInterface
   */
  protected $pathMatcher;

  /**
   * Entity Type Manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Request Stack Service.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * Module handler service.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_user'),
      $container->get('path.matcher'),
      $container->get('entity_type.manager'),
      $container->get('request_stack'),
      $container->get('module_handler')
    );
  }

  /**
   * {@inheritDoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, AccountProxyInterface $current_user, PathMatcherInterface $path_matcher, EntityTypeManagerInterface $entity_manager, RequestStack $request_stack, ModuleHandlerInterface $module_handler) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentUser = $current_user;
    $this->pathMatcher = $path_matcher;
    $this->entityTypeManager = $entity_manager;
    $this->requestStack = $request_stack;
    $this->moduleHandler = $module_handler;
  }

  /**
   * {@inheritDoc}
   */
  public function getCacheContexts() {
    $context = parent::getCacheContexts();
    // Make the block cache different for each page since the login link has a
    // destination parameter.
    return Cache::mergeContexts($context, ['user', 'url.path', 'url.query_args']);
  }

  /**
   * {@inheritDoc}
   */
  public function getCacheTags() {
    $tags = parent::getCacheTags();
    // Add the cache tag for the user so that if the user entity changes, the
    // block will invalidate.
    if ($this->currentUser->isAuthenticated()) {
      $tags = Cache::mergeTags($tags, ['user:' . $this->currentUser->id()]);
    }
    return $tags;
  }

  /**
   * {@inheritDoc}
   */
  public function build() {
    // Display only a login link for anonymous users.
    if ($this->currentUser->isAnonymous()) {
      return [
        '#type' => 'link',
        '#title' => $this->t('Log In'),
        '#url' => $this->getLoginUrl(),
      ];
    }

    // Display the list of links for logged in users.
    $user_dashboard = Url::fromRoute('entity.user.canonical', ['user' => $this->currentUser->id()]);
    return [
      '#type' => 'dropbutton',
      '#links' => [
        'name' => [
          'title' => $this->getUserName(),
          'url' => $user_dashboard,
        ],
        'dashboard' => [
          'title' => $this->t('Favorites'),
          'url' => $user_dashboard,
        ],
        'edit' => [
          'title' => $this->t('Edit Profile'),
          'url' => Url::fromRoute('entity.user.edit_form', ['user' => $this->currentUser->id()]),
        ],
        'logout' => [
          'title' => $this->t('Log Out'),
          'url' => Url::fromRoute('user.logout'),
        ],
      ],
    ];
  }

  /**
   * Get the current users display name or there account name.
   *
   * @return string
   *   Display name.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function getUserName() {
    /** @var \Drupal\user\UserInterface $user */
    $user = $this->entityTypeManager->getStorage('user')
      ->load($this->currentUser->id());

    $name = $this->currentUser->getDisplayName();
    // Use the display name field if its available and populated.
    if ($user && $user->hasField('su_display_name')) {
      $name = $user->get('su_display_name')->getString() ?: $user->label();
    }
    return $name;
  }

  /**
   * Get the url object with appropriate queries.
   *
   * @return \Drupal\Core\Url
   *   Url object.
   */
  protected function getLoginUrl() {
    $options = [];
    if (!$this->pathMatcher->isFrontPage()) {
      $destination = $this->requestStack->getCurrentRequest()->getRequestUri();
      // Only add the destination if the user was on the list of opportunities.
      // All other login urls will push the user to their dashboard upon logging
      // in.
      if (strpos($destination, '/opportunities') === 0) {
        $options = [
          'query' => ['destination' => htmlspecialchars($destination)],
        ];
      }
    }
    // Different routes for saml and core.
    $route = $this->moduleHandler->moduleExists('simplesamlphp_auth') ? 'simplesamlphp_auth.saml_login' : 'user.login';
    return Url::fromRoute($route, [], $options);
  }

}
