<?php

namespace Drupal\stanford_person_importer;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\taxonomy\TermInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Stanford CAP API helper service.
 *
 * @package Drupal\stanford_person_importer
 */
class Cap implements CapInterface {

  use StringTranslationTrait;

  /**
   * CAPx API username.
   *
   * @var string
   */
  protected $clientId;

  /**
   * CAPx API password.
   *
   * @var string
   */
  protected $clientSecret;

  /**
   * Guzzle client service.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $client;

  /**
   * Entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Cache service.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cache;

  /**
   * Database connection service.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Database logging service.
   *
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected $logger;

  /**
   * Validate the form submission credentials are valid.
   *
   * @param array $element
   *   Password form element.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Current form state.
   * @param array $form
   *   Complete form render array.
   */
  public static function validateCredentials(array $element, FormStateInterface $form_state, array $form) {
    $username = $form_state->getValue(['su_person_cap_username', 0, 'value']);
    $password = $form_state->getValue(['su_person_cap_password', 0, 'value']);

    // Call the service to test the connection.
    $success = \Drupal::service('stanford_person_importer.cap')
      ->setClientId($username)
      ->setClientSecret($password)
      ->testConnection();
    if (!$success) {
      $form_state->setError($element, 'Invalid CAP credentials.');
    }
  }

  /**
   * Capx constructor.
   *
   * @param \GuzzleHttp\ClientInterface $guzzle
   *   Guzzle http service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Entity Type Manager Service.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache
   *   Cache service.
   * @param \Drupal\Core\Database\Connection $database
   *   Database connection service.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
   *   Database logging service.
   */
  public function __construct(ClientInterface $guzzle, EntityTypeManagerInterface $entity_type_manager, CacheBackendInterface $cache, Connection $database, LoggerChannelFactoryInterface $logger_factory) {
    $this->client = $guzzle;
    $this->entityTypeManager = $entity_type_manager;
    $this->cache = $cache;
    $this->database = $database;
    $this->logger = $logger_factory->get('stanford_person_importer');
  }

  /**
   * {@inheritDoc}
   */
  public function setClientId($client_id) {
    $this->clientId = $client_id;
    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function setClientSecret($secret) {
    $this->clientSecret = $secret;
    return $this;
  }

  /**
   * Call the API and return the response.
   *
   * @param string $url
   *   API Url.
   * @param array $options
   *   Guzzle request options.
   *
   * @return bool|array
   *   Response string or false if failed.
   */
  protected function getApiResponse($url, array $options = []) {
    try {
      $response = $this->client->request('GET', $url, $options);
    }
    catch (GuzzleException | \Exception $e) {
      // Most errors originate from the API itself, log the error and let it
      // fall over.
      $this->logger->error($this->t('An unexpected error came from the API: %message'), ['%message' => $e->getMessage()]);
      throw new \Exception($e->getMessage());
    }
    return $response->getStatusCode() == 200 ? json_decode((string) $response->getBody(), TRUE, 512, JSON_THROW_ON_ERROR) : FALSE;
  }

  /**
   * {@inheritDoc}
   */
  public function getOrganizationUrl($organizations, $children = FALSE) {
    $organizations = preg_replace('/[^A-Z,0-9]/', '', strtoupper($organizations));
    $url = self::CAP_URL . "?orgCodes=$organizations";
    if ($children) {
      $url .= '&includeChildren=true';
    }
    return $url;
  }

  /**
   * {@inheritDoc}
   */
  public function getWorkgroupUrl($workgroups) {
    $workgroups = preg_replace('/[^A-Z,:~\-_0-9]/', '', strtoupper($workgroups));
    return self::CAP_URL . "?privGroups=$workgroups";
  }

  /**
   * {@inheritDoc}
   */
  public function getSunetUrl($sunetids) {
    $count = substr_count($sunetids, ',') + 1;
    $url = self::CAP_URL . "?uids=$sunetids";
    // Cap API default to 10 results. Send the argument to collect more if
    // there are more sunets to get results for.
    if ($count > 10) {
      $url .= "&ps=$count";
    }
    return $url;
  }

  /**
   * {@inheritDoc}
   */
  public function getTotalProfileCount($url) {
    $token = $this->getAccessToken();
    $response = $this->getApiResponse("$url&ps=1&access_token=$token");
    return $response['totalCount'] ?? 0;
  }

  /**
   * {@inheritDoc}
   */
  public function testConnection() {
    $this->cache->invalidate('cap:access_token');
    return !empty($this->getAccessToken());
  }

  /**
   * {@inheritDoc}
   */
  public function updateOrganizations() {
    $this->insertOrgData($this->getOrgData());
  }

  /**
   * Insert the given organization data into the database.
   *
   * @param array $org_data
   *   Keyed array of organization data.
   * @param \Drupal\taxonomy\TermInterface|null $parent
   *   The organization parent if one exists.
   *
   * @throws \Exception
   */
  protected function insertOrgData(array $org_data, TermInterface $parent = NULL) {
    if (!isset($org_data['orgCodes'])) {
      return;
    }

    $term_storage = $this->entityTypeManager->getStorage('taxonomy_term');
    $tids = $term_storage->getQuery()
      ->condition('vid', 'cap_org_codes')
      ->condition('su_cap_org_code', $org_data['orgCodes'], 'IN')
      ->execute();

    if (empty($tids)) {
      /** @var \Drupal\taxonomy\TermInterface $term */
      $term = $term_storage->create([
        'name' => $org_data['name'] . ' (' . implode(', ', $org_data['orgCodes']) . ')',
        'vid' => 'cap_org_codes',
        'su_cap_org_code' => $org_data['orgCodes'],
      ]);

      if ($parent) {
        $term->set('parent', $parent->id());
      }
      $term->save();
      $parent = $term;
    }
    else {
      $parent = $term_storage->load(reset($tids));
    }

    if (!empty($org_data['children'])) {
      foreach ($org_data['children'] as $child) {
        $this->insertOrgData($child, $parent);
      }
    }
  }

  /**
   * Get the organization data array from the API.
   *
   * @return array
   *   Keyed array of all organization data.
   */
  protected function getOrgData() {
    if ($cache = $this->cache->get('cap:org_data')) {
      return $cache->data;
    }

    $options = ['query' => ['access_token' => $this->getAccessToken()]];
    // AA00 is the root level of all Stanford.
    $result = $this->getApiResponse(self::API_URL . '/cap/v1/orgs/AA00', $options);

    if ($result) {
      $this->cache->set('cap:org_data', $result, time() + 60 * 60 * 24 * 7, [
        'cap',
        'cap:org-data',
      ]);
      return $result;
    }
    return [];
  }

  /**
   * Get the API token for CAP.
   *
   * @return string
   *   API Token.
   */
  protected function getAccessToken() {
    if ($cache = $this->cache->get('cap:access_token')) {
      return $cache->data['access_token'];
    }

    $options = [
      'query' => ['grant_type' => 'client_credentials'],
      'auth' => [$this->clientId, $this->clientSecret],
    ];
    if ($result = $this->getApiResponse(self::AUTH_URL, $options)) {
      $this->cache->set('cap:access_token', $result, time() + $result['expires_in'] - 60, [
        'cap',
        'cap:token',
      ]);
      return $result['access_token'];
    }
  }

}
