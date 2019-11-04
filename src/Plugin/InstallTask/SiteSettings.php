<?php

namespace Drupal\stanford_profile\Plugin\InstallTask;

use Drupal\Component\Utility\Html;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Site\Settings;
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
      $container->get('logger.factory')
    );
  }

  /**
   * {@inheritDoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entityTypeManager, ClientInterface $client, AuthmapInterface $authmap, LoggerChannelFactoryInterface $logger_factory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entityTypeManager;
    $this->client = $client;
    $this->authmap = $authmap;
    $this->logger = $logger_factory->get('stanford_profile');
  }

  /**
   * {@inheritDoc}
   */
  public function runTask(array &$install_state) {
    // @codeCoverageIgnoreStart
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
    $this->entityTypeManager->getStorage('config_pages')->create([
      'type' => 'stanford_basic_site_settings',
      'su_site_email' => $site_data['email'],
      'su_site_name' => $site_data['webSiteTitle'],
      'context' => 'a:0:{}',
    ])->save();

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
      'pass' => user_password(),
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

    try {
      $response = $this->client->request('GET', self::SNOW_API, [
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
   * Is the install occurring on Acquia environment.
   *
   * @return bool
   *   True if on Acquia.
   *
   * @codeCoverageIgnore
   *   We want to test the class and need to fake being on Acquia.
   */
  protected static function isAhEnv() {
    return isset($_ENV['AH_SITE_ENVIRONMENT']);
  }

}
