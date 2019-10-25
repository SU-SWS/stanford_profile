<?php

namespace Drupal\stanford_profile\Plugin\InstallTask;

use Drupal\Component\Utility\Html;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormState;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\stanford_profile\InstallTaskBase;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class SiteSettings
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
   * Form builder service.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  protected $formBuilder;

  /**
   * Logger channel service.
   *
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected $logger;

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('http_client'),
      $container->get('form_builder'),
      $container->get('logger.factory')
    );
  }

  /**
   * InstallTasks constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   Entity type manager service.
   * @param \GuzzleHttp\ClientInterface $client
   *   Guzzle service.
   * @param \Drupal\Core\Form\FormBuilderInterface $form_builder
   *   Form builder service.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
   *   Logger Factory service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entityTypeManager, ClientInterface $client, FormBuilderInterface $form_builder, LoggerChannelFactoryInterface $logger_factory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entityTypeManager;
    $this->client = $client;
    $this->formBuilder = $form_builder;
    $this->logger = $logger_factory->get('stanford_profile');
  }

  /**
   * {@inheritDoc}
   */
  public function runTask(&$install_state) {
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

    // TODO Figure out how to do this with the default content at the same time.
    $this->addSiteOwner($site_data['sunetId']);

    if (isset($site_data['webSiteOwners'])) {
      foreach ($site_data['webSiteOwners'] as $owner) {
        if ($owner['sunetId'] == $site_data['sunetId']) {
          continue;
        }

        $this->addSiteOwner($owner['sunetId']);
      }
    }
  }

  /**
   * Add a user with the site manager role.
   *
   * @param string $sunet
   *   User SunetId.
   */
  protected function addSiteOwner($sunet) {
    $form_state = new FormState();
    $form_state->setValue('sunetid', $sunet);
    $form_state->setValue('roles', ['site_manager']);
    $this->formBuilder->submitForm('\Drupal\stanford_ssp\Form\AddUserForm', $form_state);
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
          getenv('STANFORD_SNOW_API_USER'),
          getenv('STANFORD_SNOW_API_PASS'),
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

}
