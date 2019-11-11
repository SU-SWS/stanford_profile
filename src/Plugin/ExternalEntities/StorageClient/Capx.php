<?php

namespace Drupal\stanford_profile\Plugin\ExternalEntities\StorageClient;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\external_entities\Plugin\ExternalEntities\StorageClient\Rest;
use Drupal\external_entities\ResponseDecoder\ResponseDecoderFactoryInterface;
use GuzzleHttp\ClientInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * External entities storage client based on a Cap API.
 *
 * @ExternalEntityStorageClient(
 *   id = "capx",
 *   label = @Translation("CapX"),
 *   description = @Translation("Retrieves external entities from a Cap API.")
 * )
 */
class Capx extends Rest {

  /**
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cache;

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('string_translation'),
      $container->get('external_entities.response_decoder_factory'),
      $container->get('http_client'),
      $container->get('cache.default')
    );
  }

  /**
   * {@inheritDoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, TranslationInterface $string_translation, ResponseDecoderFactoryInterface $response_decoder_factory, ClientInterface $http_client, CacheBackendInterface $cache) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $string_translation, $response_decoder_factory, $http_client);
    $this->cache = $cache;
  }

  /**
   * {@inheritDoc}
   */
  public function defaultConfiguration() {
    $config = parent::defaultConfiguration();
    $config['username'] = '';
    $config['password'] = '';
    return $config;
  }

  /**
   * {@inheritDoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);
    $form['username'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Username'),
      '#default_value' => $this->configuration['username'],
    ];
    $form['password'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Password'),
      '#default_value' => $this->configuration['password'],
    ];
    return $form;
  }

  /**
   * {@inheritDoc}
   */
  public function query(array $parameters = [], array $sorts = [], $start = NULL, $length = NULL, $get_all_data = FALSE) {
    $query = parent::query($parameters, $sorts, $start, $length);
    if (!$get_all_data) {
      return $query['values'];
    }
    return $query;
  }

  /**
   * {@inheritDoc}
   */
  public function load($id) {
    $response = $this->httpClient->request(
      'GET',
      $this->configuration['endpoint'],
      [
        'headers' => $this->getHttpHeaders(),
        'query' => $this->getSingleQueryParameters($id),
      ]
    );

    $body = $response->getBody();

    return $this
      ->getResponseDecoderFactory()
      ->getDecoder($this->configuration['response_format'])
      ->decode($body);
  }

  /**
   * {@inheritdoc}
   */
  public function loadMultiple(array $ids = NULL) {
    $data = parent::loadMultiple($ids);
    foreach ($data as &$item) {
      $item = $item['values'][0];
    }
    return $data;
  }

  /**
   * {@inheritDoc}
   */
  public function getSingleQueryParameters($id, array $parameters = []) {
    $query_parameters = parent::getSingleQueryParameters($id, $parameters);
    $query_parameters['ids'] = $id;
    $query_parameters['access_token'] = $this->getAccessToken();
    return $query_parameters;
  }

  /**
   * {@inheritDoc}
   */
  public function getListQueryParameters(array $parameters = [], $start = NULL, $length = NULL) {
    $query_parameters = parent::getListQueryParameters($parameters, $start, $length);
    $query_parameters['access_token'] = $this->getAccessToken();
    return $query_parameters;
  }

  /**
   * {@inheritDoc}
   */
  public function countQuery(array $parameters = []) {
    $parameters['ps'] = 1;
    $results = $this->query($parameters, [], NULL, NULL, TRUE);
    return $results['totalCount'];
  }

  /**
   * {@inheritDoc}
   */
  public function getPagingQueryParameters($start = NULL, $length = NULL) {
    $paging_parameters = parent::getPagingQueryParameters($start, $length);
    if (isset($paging_parameters[$this->configuration['pager']['page_parameter']])) {
      $paging_parameters[$this->configuration['pager']['page_parameter']]++;
    }
    return $paging_parameters;
  }

  protected function getAccessToken() {
    if ($cache = $this->cache->get('capx_access_token')) {
      return $cache->data;
    }
    $request_options = [
      'query' => ['grant_type' => 'client_credentials'],
      'auth' => [
        $this->configuration['username'],
        $this->configuration['password'],
      ],
    ];
    $response = $this->httpClient->request('POST', 'https://authz.stanford.edu/oauth/token', $request_options);
    $json_response = json_decode((string) $response->getBody(), TRUE);
    $this->cache->set('capx_access_token', $json_response['access_token'], time() + $json_response['expires_in']);
    return $json_response['access_token'];
  }

}
