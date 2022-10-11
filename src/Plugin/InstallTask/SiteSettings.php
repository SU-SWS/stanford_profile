<?php

namespace Drupal\stanford_profile\Plugin\InstallTask;

use Drupal\Component\Utility\Html;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Password\PasswordGeneratorInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Site\Settings;
use Drupal\Core\State\StateInterface;
use Drupal\externalauth\AuthmapInterface;
use Drupal\stanford_profile\InstallTaskBase;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * SNOW site settings installation.
 *
 * @InstallTask(
 *   id="stanford_profile_site_settings"
 * )
 */
class SiteSettings extends InstallTaskBase implements ContainerFactoryPluginInterface {

  /**
   * The fallback site name.
   */
  const DEFAULT_SITE = 'default';

  /**
   * Service now api endpoint.
   */
  const SNOW_API = 'https://stanford.service-now.com/api/stu/su_acsf_site_requester_information/requestor';

  /**
   * Guzzle service.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $client;

  /**
   * Authmap service.
   *
   * @var \Drupal\externalauth\AuthmapInterface
   */
  protected $authmap;

  /**
   * Password generator service.
   *
   * @var \Drupal\Core\Password\PasswordGeneratorInterface
   */
  protected $passwordGenerator;

  /**
   * State Service.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * Logger channel service.
   *
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected $logger;

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('http_client'),
      $container->get('externalauth.authmap'),
      $container->get('password_generator'),
      $container->get('state'),
      $container->get('logger.factory')
    );
  }

  /**
   * {@inheritDoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entityTypeManager, ClientInterface $client, AuthmapInterface $authmap, PasswordGeneratorInterface $password_generator, StateInterface $state, LoggerChannelFactoryInterface $logger_factory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entityTypeManager;
    $this->client = $client;
    $this->authmap = $authmap;
    $this->passwordGenerator = $password_generator;
    $this->state = $state;
    $this->logger = $logger_factory->get('stanford_profile');
  }

  /**
   * {@inheritDoc}
   */
  public function runTask(array &$install_state) {
    $this->state->set('nobots', FALSE);

    $node_pages = [
      '403_page' => '4b8018dc-49a6-4018-9c54-e8c3e462beee',
      '404_page' => '6d51339d-ff67-498d-98e9-d8228d36fd51',
      'front_page' => '72f0069b-f1ec-4122-af73-6aa841faea90',
    ];

    // @codeCoverageIgnoreStart
    foreach ($node_pages as $page => $uuid) {
      if ($node = $this->getNode($uuid)) {
        $this->state->set("stanford_profile.$page", '/node/' . $node->id());
      }
    }

    if (!static::isAhEnv()) {
      return;
    }
    // @codeCoverageIgnoreEnd
    $site_name = $install_state['forms']['install_configure_form']['site_name'] ?? self::DEFAULT_SITE;
    $site_name = Html::escape($site_name);

    $site_data = $this->getSnowData($site_name);
    if (empty($site_data)) {
      return;
    }
    $this->state->set('xmlsitemap_base_url', "https://$site_name.sites.stanford.edu");

    $config_page = $this->entityTypeManager->getStorage('config_pages')
      ->load('stanford_basic_site_settings');
    if (!$config_page) {
      $config_page = $this->entityTypeManager->getStorage('config_pages')
        ->create([
          'type' => 'stanford_basic_site_settings',
          'context' => 'a:0:{}',
          'su_hide_ext_link_icons' => TRUE,
        ]);
    }
    $config_page->set('su_site_email', $site_data['email']);
    $config_page->set('su_site_name', $site_data['webSiteTitle']);
    $config_page->save();

    $this->addSiteOwner($site_data['sunetId'], $site_data['email']);

    if (isset($site_data['webSiteOwners'])) {
      foreach ($site_data['webSiteOwners'] as $owner) {
        if ($owner['sunetId'] == $site_data['sunetId']) {
          continue;
        }

        $this->addSiteOwner($owner['sunetId'], $owner['email']);
      }
    }
  }

  /**
   * Add a user with the site manager role.
   *
   * @param string $sunet
   *   User SunetId.
   * @param string $email
   *   User email.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function addSiteOwner($sunet, $email) {
    $new_user = $this->entityTypeManager->getStorage('user')->create([
      'name' => $sunet,
      'pass' => $this->passwordGenerator->generate(),
      'mail' => $email,
      'roles' => ['site_manager'],
      'status' => 1,
    ]);
    $new_user->save();
    $this->authmap->save($new_user, 'simplesamlphp_auth', $sunet);
  }

  /**
   * Get site information from the SNOW API.
   *
   * @param string $site_name
   *   The requested name of the site.
   *
   * @return array|null
   *   Returned data if any exist.
   */
  protected function getSnowData($site_name) {
    $api_url = Settings::get('stanford_profile_snow_api_url', self::SNOW_API);
    try {
      $response = $this->client->request('GET', $api_url, [
        'query' => ['website_address' => $site_name],
        'auth' => [
          Settings::get('stanford_profile_snow_api_user'),
          Settings::get('stanford_profile_snow_api_pass'),
        ],
      ]);

      $response = json_decode((string) $response->getBody(), TRUE);

      // If the response body was not a json string.
      if (!is_array($response)) {
        throw new \Exception('Could not decode JSON from SNOW API.');
      }

      if (isset($response['result'][0]['message']) && preg_match('/no records found/i', $response['result'][0]['message'])) {
        throw new \Exception($response['result'][0]['message']);
      }

      return reset($response['result'][0]);
    }
    catch (GuzzleException $e) {
      $this->logger->alert($this->t('Unable to fetch SNOW data for %site. Message: %message'), [
        '%site' => $site_name,
        '%message' => $e->getMessage(),
      ]);
    }
    catch (\Exception $e) {
      $this->logger->alert($this->t('Unable to fetch SNOW data for %site. Message: %message'), [
        '%site' => $site_name,
        '%message' => $e->getMessage(),
      ]);
      if ($site_name != 'default') {
        return $this->getSnowData('default');
      }
    }
  }

  /**
   * Load a node by the UUID value.
   *
   * @param string $uuid
   *   Node uuid.
   *
   * @return \Drupal\node\NodeInterface
   *   Node object.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function getNode($uuid) {
    $nodes = $this->entityTypeManager->getStorage('node')
      ->loadByProperties(['uuid' => $uuid]);
    return reset($nodes);
  }

}
