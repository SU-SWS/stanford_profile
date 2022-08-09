<?php

namespace Drupal\stanford_events_importer\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\link\LinkItemInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\link\Plugin\Field\FieldWidget\LinkWidget;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\stanford_events_importer\StanfordEventsImporter;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'stanford_events_importer_apiurl_field_widget'.
 *
 * @FieldWidget(
 *   id = "stanford_events_importer_apiurl_field_widget",
 *   module = "stanford_events_importer",
 *   label = @Translation("Stanford Events API URL Builder Widget"),
 *   field_types = {
 *     "link"
 *   }
 * )
 */
class StanfordEventsImporterAPIURLFieldWidget extends LinkWidget {

  /**
   * Path to feed xml.
   */
  const XML_FEED = "https://events-legacy.stanford.edu/xml/drupal/v2.php";

  /**
   * The cache backend service interface.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cache;

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($plugin_id, $plugin_definition, $configuration['field_definition'], $configuration['settings'], $configuration['third_party_settings'], $container->get('cache.default'));
  }

  /**
   * {@inheritdoc}
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, array $third_party_settings, CacheBackendInterface $cache) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
    $this->cache = $cache;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {

    // De-falt.
    $element = parent::formElement($items, $delta, $element, $form, $form_state);
    $element['#attached']['library'][] = 'core/drupal.states';

    /** @var \Drupal\link\LinkItemInterface $item */
    $item = $items[$delta];
    $defaults = $this->parseURLForDefaults($item);

    // Add a type of feed.
    $element['_other']['type'] = [
      '#type' => 'select',
      '#title' => $this->t("Event Group Option"),
      '#empty_option' => $this->t("- Select Option -"),
      '#options' => [
        'organization' => $this->t("Organization"),
        'category' => $this->t("Category"),
        'featured' => $this->t('Featured'),
        'today' => $this->t('Today'),
      ],
      '#default_value' => $defaults['type'] ?? '',
    ];

    // The organization id (integer) as provided in the xml feed.
    $element['_other']['organization'] = [
      '#type' => 'select',
      '#title' => $this->t("Organization"),
      '#field_parents' => ['other'],
      '#empty_option' => $this->t("- Select Organization -"),
      '#options' => $this->getOrgOptions() ?: [],
      '#states' => [
        'visible' => [
          '#edit-su-event-xml-url-' . $delta . '-other-type' => ['value' => 'organization'],
        ],
      ],
      '#default_value' => $defaults['organization'] ?? '',
    ];

    $element['_other']['category'] = [
      '#type' => 'select',
      '#title' => $this->t("Category"),
      '#field_parents' => ['other'],
      '#empty_option' => $this->t("- Select Category -"),
      '#options' => $this->getCatOptions() ?: [],
      '#states' => [
        'visible' => [
          '#edit-su-event-xml-url-' . $delta . '-other-type' => ['value' => 'category'],
        ],
      ],
      '#default_value' => $defaults['category'] ?? '',
    ];

    $element['_other']['org_status'] = [
      '#type' => 'select',
      '#title' => $this->t("Event Status"),
      '#field_parents' => ['other'],
      '#options' => [
        'published' => $this->t("Published"),
        'unlisted' => $this->t("Unlisted"),
        'bookmarked' => $this->t("Bookmarked"),
      ],
      '#states' => [
        'visible' => [
          '#edit-su-event-xml-url-' . $delta . '-other-type' => ['value' => 'organization'],
        ],
      ],
      '#default_value' => $defaults['org_status'] ?? '',
    ];

    // Hide the uri.
    $element['uri']['#type'] = "hidden";
    $element['uri']['#required'] = FALSE;
    return $element;
  }

  /**
   * Get a key/value list of organizations from the API.
   *
   * @return array
   *   An array of select form options.
   */
  protected function getOrgOptions() {
    if ($cache = $this->cache->get(StanfordEventsImporter::CACHE_KEY_ORG)) {
      return $cache->data;
    }

    // If not in cache. Try to get it.
    stanford_events_importer_update_opts();
    return $this->cache->get(StanfordEventsImporter::CACHE_KEY_ORG)->data;
  }

  /**
   * Get a key/value list of categories from the API.
   *
   * @return array
   *   An array of select form options.
   */
  protected function getCatOptions() {
    if ($cache = $this->cache->get(StanfordEventsImporter::CACHE_KEY_CAT)) {
      return $cache->data;
    }

    // If not in cache. Try to get it.
    stanford_events_importer_update_opts();
    return $this->cache->get(StanfordEventsImporter::CACHE_KEY_CAT)->data;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {

    // Parse form options we added into a url for the events-legacy.stanford.edu feed.
    array_walk($values, 'self::walkMassagedFormValues');

    // Let the parent LinkWidget do its thing.
    $values = parent::massageFormValues($values, $form, $form_state);

    // Finish.
    return $values;
  }

  /**
   * Callback for array_walk in massageFormValues.
   *
   * @param array $value
   *   The value of the array item being walked through.
   */
  protected static function walkMassagedFormValues(array &$value) {
    // All our extra form fields are stored in _other.
    $type = $value['_other']['type'] ?? '';
    $val = $value['_other'][$type] ?? '';
    $extra = $value["_other"]['org_status'] ?? '';
    unset($value['_other']);

    // Empty value. Empty out uri and do not parse a url.
    // This is how you get rid of an option.
    if ($type === "") {
      $value['uri'] = "";
      return;
    }

    // Valid Data. Create a url for the uri column.
    if ($type == "featured" || $type == "today") {
      $url = static::XML_FEED . "?" . $type;
    }
    // Default to key = value.
    else {
      $url = static::XML_FEED . "?" . $type . "=" . $val;
    }

    // Organizations have extra options.
    if ($type == "organization") {
      $url .= "&" . $extra;
    }

    // Set the value.
    $value['uri'] = $url;
  }

  /**
   * Parse a link item for XML_FEED values.
   *
   * @param \Drupal\link\LinkItemInterface $item
   *   The link interface item from the field itself.
   *
   * @return array
   *   An array of key values.
   *
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  protected function parseURLForDefaults(LinkItemInterface $item) {
    $parsed = [];
    $uri = $item->get('uri')->getValue();

    // Nothing to do.
    if (empty($uri)) {
      return $parsed;
    }

    // Break up the URL to get at the query strings.
    $parts = UrlHelper::parse($uri);

    // Pull apart the query strings and set them to keys for easy use.
    if (isset($parts['query'])) {
      $parsed = $parts['query'];
      $keys = array_keys($parts['query']);
      $parsed['type'] = array_shift($keys);
      $parsed['org_status'] = array_pop($keys);
    }

    // Return back the broken down information.
    return $parsed;
  }

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(FieldDefinitionInterface $field_definition) {
    // By default, widgets are available for all fields. Limit to just one.
    $allow = ['su_event_xml_url'];
    $field_name = $field_definition->getFieldStorageDefinition()
      ->get('field_name');
    return in_array($field_name, $allow);
  }

}
