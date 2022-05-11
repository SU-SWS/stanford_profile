<?php

namespace Drupal\stanford_events_importer;

use GuzzleHttp\ClientInterface;

/**
 * A class to do some importery stuff.
 */
class StanfordEventsImporter {

  /**
   * URL Endpoint for getting categories.
   */
  const STANFORD_EVENTS_IMPORTER_XML = "https://events-legacy.stanford.edu/xml/drupal/v2.php";

  /**
   * Bin used to get/set cache for organization information.
   */
  const CACHE_KEY_ORG = "stanford_events_importer_orgs";

  /**
   * Bin used to get/set cache for category information.
   */
  const CACHE_KEY_CAT = "stanford_events_importer_cats";

  /**
   * Guzzle client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  private $client;

  /**
   * StanfordEventsImporter constructor.
   *
   * @param \GuzzleHttp\ClientInterface $http_client
   *   A Guzzle Like HTTP client.
   */
  public function __construct(ClientInterface $http_client) {
    $this->client = $http_client;
  }

  /**
   * Get xml from the API.
   *
   * @param string $query
   *   The argument to pass to query.
   *
   * @return string|bool
   *   Full string of raw xml.
   */
  public function fetchXML($query = "category-list") {

    $options = [
      'query' => [
        $query => '',
      ],
      'headers' => [
        'Accept' => 'application/xml',
      ],
    ];

    try {
      $request = $this->client->request('GET', self::STANFORD_EVENTS_IMPORTER_XML, $options);
      $xml_raw = (string) $request->getBody();
      return $xml_raw;
    }
    catch (\Exception $e) {
      return FALSE;
    }

  }

  /**
   * Parses the raw xml return value from the API.
   *
   * Turns the raw xml return value into a key value pair and saves it into the
   * state for the site.
   *
   * @param string $raw
   *   Raw XML string.
   * @param array $options
   *   A keyed array of options. Expects:
   *   - guids: An xpath string to the key
   *   - label: An xpath string to the label.
   *
   * @return array|bool
   *   Success or not.
   */
  public function parseXML($raw, array $options) {
    $xml = new \SimpleXMLElement($raw);
    $guids = $xml->xpath($options['guids']);
    $labels = $xml->xpath($options['label']);

    array_walk($guids, function (&$val) {
      $val = $val->__toString();
    });

    array_walk($labels, function (&$val) {
      $val = $val->__toString();
    });

    // Do nothing if nothing was found.
    if (empty($guids) || empty($labels)) {
      return FALSE;
    }

    // Create an associative array.
    return array_combine($guids, $labels);
  }

}
