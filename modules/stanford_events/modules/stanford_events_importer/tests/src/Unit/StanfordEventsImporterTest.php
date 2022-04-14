<?php

namespace Drupal\Tests\stanford_events_importer\Unit;

use Drupal\stanford_events_importer\StanfordEventsImporter;
use Drupal\Tests\UnitTestCase;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;


/**
 * Class StanfordEventsImporterTest
 *
 * @group stanford_events_importer
 * @coversDefaultClass \Drupal\stanford_events_importer\StanfordEventsImporter
 */
class StanfordEventsImporterTest extends UnitTestCase {

  /**
   * @var \Drupal\stanford_events_importer\StanfordEventsImporter
   */
  public $plugin;

  /**
   * @var \GuzzleHttp\ClientInterface
   */
  public $client;

  /**
   * @var string
   */
  public $xml;

  public function setup() {
    $this->client = $this->createMock(ClientInterface::class);
    $this->client->method('request')
      ->will($this->returnCallback([$this, 'getResponseCallback']));

    $this->plugin = new StanfordEventsImporter($this->client);
  }

  /**
   * Guzzle client get request callback.
   */
  public function getResponseCallback($method, $url, $options) {
    $category = '<CategoryList>
        <Category>
        <guid>0</guid>
        <categoryID>0</categoryID>
        <name>Arts</name>
        <type>3</type>
        <description>Arts</description>
        <tag>arts</tag>
        </Category>
        </CategoryList>';
    $orgs = '<OrganizationList>
        <Organization>
        <guid>279</guid>
        <organizationID>279</organizationID>
        <name>AASA</name>
        <type>2</type>
        <email>eshih@stanford.edu</email>
        <phone>510-366-6550</phone>
        <url>https://events-legacy.stanford.edu/byOrganization/279/</url>
        <rssUrl>https://events-legacy.stanford.edu/byOrganization/279/</rssUrl>
        </Organization>
        </OrganizationList>';

    $response = $this->createMock(Response::class);

    $body = $orgs;
    if (isset($options['query']['category-list'])) {
      $body = $category;
    }
    $response->method('getBody')
      ->willReturn($body);
    return $response;
  }

  /**
   * Test Fetch.
   */
  public function testFetchXML() {
    $this->xml = $this->plugin->fetchXML();
    $this->assertStringContainsString("Arts", $this->xml);

    $orgs = $this->plugin->fetchXML('organization-list');
    $this->assertStringContainsString("AASA", $orgs);
  }

  /**
   * Test Parse.
   */
  public function testParseXML() {

    $xml_string = <<<EOD
<CategoryList>
<Category>
<guid>0</guid>
<categoryID>0</categoryID>
<name>Arts</name>
<type>3</type>
<description>Arts</description>
<tag>arts</tag>
</Category>
<Category>
<guid>28</guid>
<categoryID>28</categoryID>
<name>Careers</name>
<type>2</type>
<description>Career Development</description>
<tag>careers</tag>
</Category>
<Category>
<guid>19</guid>
<categoryID>19</categoryID>
<name>Class</name>
<type>1</type>
<description>Classes</description>
<tag>class</tag>
</Category>
<Category>
<guid>3</guid>
<categoryID>3</categoryID>
<name>Conference / Symposium</name>
<type>1</type>
<description>Conferences / Symposia</description>
<tag>conference</tag>
</Category>
<Category>
<guid>5</guid>
<categoryID>5</categoryID>
<name>Dance</name>
<type>3</type>
<description>Dance</description>
<tag>dance</tag>
</Category>
</CategoryList>
EOD;

    $args = [
      'guids' => '/CategoryList/Category/guid',
      'label' => '/CategoryList/Category/name',
    ];
    $result = $this->plugin->parseXML($xml_string, $args);
    $this->assertIsArray($result);
    $this->assertEquals("Class", $result[19]);
  }

  /**
   * Make sure exception passes back false.
   *
   * @return [type] [description]
   */
  public function testFetchXMLException() {
    $client = $this->createMock(ClientInterface::class);
    $client->method('request')
      ->willThrowException(new RequestException('Failure', new Request('GET', 'test')));
    $plugin = new StanfordEventsImporter($client);
    $val = $plugin->fetchXML();
    $this->assertFalse($val);
  }

  /**
   * Make sure exception passes back false.
   *
   * @return [type] [description]
   */
  public function testParseXMLException() {
    $args = [
      'guids' => '/CategoryList/Category/guid',
      'label' => '/CategoryList/Category/name',
    ];
    $val = $this->plugin->parseXML('<root><item>stuff</item></root>', $args);
    $this->assertFalse($val);
  }

}
