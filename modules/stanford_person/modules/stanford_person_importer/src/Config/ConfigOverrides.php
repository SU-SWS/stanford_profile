<?php

namespace Drupal\stanford_person_importer\Config;

use Drupal\config_pages\ConfigPagesLoaderServiceInterface;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ConfigFactoryOverrideInterface;
use Drupal\Core\Config\StorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\stanford_person_importer\CapInterface;

/**
 * Configuration overrides for stanford person importer migration entity.
 *
 * @package Drupal\stanford_person_importer\Config
 */
class ConfigOverrides implements ConfigFactoryOverrideInterface {

  /**
   * How many profiles are returned in each url.
   */
  const URL_CHUNKS = 15;

  /**
   * Config pages loader service.
   *
   * @var \Drupal\config_pages\ConfigPagesLoaderServiceInterface
   */
  protected $configPages;

  /**
   * Entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Cap API service.
   *
   * @var \Drupal\stanford_person_importer\CapInterface
   */
  protected $cap;

  /**
   * Config factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * ConfigOverrides constructor.
   *
   * @param \Drupal\config_pages\ConfigPagesLoaderServiceInterface $config_pages
   *   Config pages loader service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Entity type manager service.
   * @param \Drupal\stanford_person_importer\CapInterface $cap
   *   Cap API service.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   Config factory service.
   */
  public function __construct(ConfigPagesLoaderServiceInterface $config_pages, EntityTypeManagerInterface $entity_type_manager, CapInterface $cap, ConfigFactoryInterface $config_factory) {
    $this->configPages = $config_pages;
    $this->entityTypeManager = $entity_type_manager;
    $this->cap = $cap;
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritDoc}
   */
  public function createConfigObject($name, $collection = StorageInterface::DEFAULT_COLLECTION) {
    return NULL;
  }

  /**
   * {@inheritDoc}
   */
  public function getCacheSuffix() {
    return 'StanfordPersonImporterConfigOverride';
  }

  /**
   * {@inheritDoc}
   */
  public function getCacheableMetadata($name) {
    return new CacheableMetadata();
  }

  /**
   * {@inheritDoc}
   */
  public function loadOverrides($names) {
    $overrides = [];
    if (in_array('migrate_plus.migration.su_stanford_person', $names)) {
      $this->cap->setClientId($this->getCapClientId());
      $this->cap->setClientSecret($this->getCapClientSecret());

      $urls = $this->getOrgsUrls();
      $urls = array_merge($urls, $this->getWorkgroupUrls());
      $urls = array_merge($urls, $this->getSunetUrls());

      $allowed_fields = $this->getAllowedFields();
      foreach ($urls as &$url) {
        $url .= '&whitelist=' . implode(',', $allowed_fields);
      }
      $overrides['migrate_plus.migration.su_stanford_person']['source']['urls'] = $urls;
      $overrides['migrate_plus.migration.su_stanford_person']['source']['authentication']['client_id'] = $this->getCapClientId();
      $overrides['migrate_plus.migration.su_stanford_person']['source']['authentication']['client_secret'] = $this->getCapClientSecret();
    }
    return $overrides;
  }

  /**
   * Get a list of the fields from CAP that should be fetched.
   *
   * @return string[]
   *   Array of CAP selectors.
   */
  protected function getAllowedFields(): array {
    $allowed_fields = $this->configFactory->getEditable('migrate_plus.migration.su_stanford_person')
      ->getOriginal('source.fields') ?: [];
    foreach ($allowed_fields as &$field) {
      $field = $field['selector'];
      if ($slash_position = strpos($field, '/')) {
        $field = substr($field, 0, $slash_position);
      }
    }
    return $allowed_fields;
  }

  /**
   * Get a list of CAP urls that have an org code specified.
   *
   * @return string[]
   *   List of urls.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function getOrgsUrls(): array {
    $org_tids = $this->configPages->getValue('stanford_person_importer', 'su_person_orgs', [], 'target_id');
    $include_children = $this->configPages->getValue('stanford_person_importer', 'su_person_child_orgs', 0, 'value');
    $org_tids = array_filter($org_tids);
    // No field values populated.
    if (empty($org_tids)) {
      return [];
    }
    $org_codes = [];

    // Load the taxonomy term that the field is pointing at and use the org code
    // field on the term entity.
    $term_storage = $this->entityTypeManager->getStorage('taxonomy_term');
    foreach ($org_tids as &$tid) {
      if ($term = $term_storage->load($tid)) {
        $org_code = $term->get('su_cap_org_code')
          ->getString();
        $org_codes[] = str_replace(' ', '', $org_code);
      }
    }
    $org_codes = implode(',', $org_codes);
    return $this->getUrlChunks($this->cap->getOrganizationUrl($org_codes, $include_children));
  }

  /**
   * Get a list of CAP urls that have a workgroup filter.
   *
   * @return string[]
   *   List of urls.
   */
  protected function getWorkgroupUrls(): array {
    $workgroups = $this->configPages->getValue('stanford_person_importer', 'su_person_workgroup', [], 'value');

    if ($workgroups) {
      return $this->getUrlChunks($this->cap->getWorkgroupUrl(implode(',', $workgroups)));
    }
    return [];
  }

  /**
   * Get the list of CAP urls for a sunetid filter.
   *
   * @return string[]
   *   List of urls.
   */
  protected function getSunetUrls(): array {
    $sunets = $this->configPages->getValue('stanford_person_importer', 'su_person_sunetid', [], 'value') ?: [];

    $urls = [];
    foreach (array_chunk($sunets, self::URL_CHUNKS) as $chunk) {
      $urls[] = $this->cap->getSunetUrl(implode(',', $chunk));
    }
    return $urls;
  }

  /**
   * Break up the url into multiple urls based on the number of results.
   *
   * @param string $url
   *   Cap API Url.
   *
   * @return string[]
   *   Array of Cap API Urls.
   */
  protected function getUrlChunks($url): array {
    $count = $this->cap->getTotalProfileCount($url);
    $number_chunks = ceil($count / self::URL_CHUNKS);

    if ($number_chunks <= 1) {
      return ["$url&ps=" . self::URL_CHUNKS];
    }

    $urls = [];
    for ($i = 1; $i <= $number_chunks; $i++) {
      $urls[] = "$url&p=$i&ps=" . self::URL_CHUNKS;
    }
    return $urls;
  }

  /**
   * Get the username from the config pages field.
   *
   * @return string|null
   *   Client ID string.
   */
  protected function getCapClientId(): ?string {
    return $this->configPages->getValue('stanford_person_importer', 'su_person_cap_username', 0, 'value');
  }

  /**
   * Get the password from the config pages field.
   *
   * @return string|null
   *   Client secret string.
   */
  protected function getCapClientSecret(): ?string {
    return $this->configPages->getValue('stanford_person_importer', 'su_person_cap_password', 0, 'value');
  }

}
