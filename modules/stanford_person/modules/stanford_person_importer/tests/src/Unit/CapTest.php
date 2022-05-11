<?php

namespace Drupal\Tests\stanford_person_importer\Unit;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\Core\Form\FormState;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\stanford_person_importer\Cap;
use Drupal\taxonomy\TermInterface;
use Drupal\Tests\UnitTestCase;
use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class CapTest.
 *
 * @group stanford_person_importer
 * @coversDefaultClass \Drupal\stanford_person_importer\Cap
 */
class CapTest extends UnitTestCase {

  /**
   * Cap service.
   *
   * @var \Drupal\stanford_person_importer\Cap
   */
  protected $service;

  /**
   * The response code mock guzzle will return.
   *
   * @var int
   */
  protected $guzzleStatusCode = 200;

  /**
   * Response body the mock guzzle will return.
   *
   * @var string
   */
  protected $guzzleBody = '';

  /**
   * The simulated cache data object.
   *
   * @var \stdClass
   */
  protected $cacheData;

  /**
   * {@inheritDoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $guzzle = $this->createMock(ClientInterface::class);
    $guzzle->method('request')
      ->will($this->returnCallback([$this, 'guzzleRequestCallback']));

    $entity_type_manager = $this->getEntityTypeManager();

    $cache = $this->createMock(CacheBackendInterface::class);
    $cache->method('get')->willReturnReference($this->cacheData);

    $database = $this->createMock(Connection::class);

    $logger = $this->createMock(LoggerChannelInterface::class);

    $logger_factory = $this->createMock(LoggerChannelFactoryInterface::class);
    $logger_factory->method('get')->wilLReturn($logger);

    $this->service = new Cap($guzzle, $entity_type_manager, $cache, $database, $logger_factory);

    $container = new ContainerBuilder();
    $container->set('stanford_person_importer.cap', $this->service);
    \Drupal::setContainer($container);
  }

  /**
   * Get a mock entity type manager service.
   */
  protected function getEntityTypeManager() {
    $entity_query = $this->createMock(QueryInterface::class);
    $entity_query->method('condition')->willReturnSelf();
    $entity_query->method('execute')->willReturn([]);

    $entity = $this->createMock(TermInterface::class);

    $entity_storage = $this->createMock(EntityStorageInterface::class);
    $entity_storage->method('create')->willReturn($entity);
    $entity_storage->method('getQuery')->willReturn($entity_query);

    $entity_type_manager = $this->createMock(EntityTypeManagerInterface::class);
    $entity_type_manager->method('getStorage')->willReturn($entity_storage);

    return $entity_type_manager;
  }

  /**
   * Test the service credentials will work.
   */
  public function testCredentials() {
    $this->guzzleBody = json_encode([
      'expires_in' => 100,
      'access_token' => 'foo-bar-baz',
    ]);
    $success = $this->service->setClientId('foo')
      ->setClientSecret('bar')
      ->testConnection();
    $this->assertTrue($success);

    $this->guzzleStatusCode = 403;
    $this->assertFalse($this->service->testConnection());

    $this->guzzleStatusCode = 0;
    $this->assertFalse($this->service->testConnection());
  }

  /**
   * Cached access token will return successful connection.
   */
  public function testCachedResponses() {
    $this->cacheData = new \stdClass();
    $this->cacheData->data = ['access_token' => 'foo'];
    $this->assertTrue($this->service->testConnection());
  }

  /**
   * Form field validation will give form errors.
   */
  public function testFormValidateCredentials() {
    $element = ['#parents' => []];
    $form_state = new FormState();
    $form = [];

    $form_state->setValues([
      'su_person_cap_username' => [['value' => 'foo']],
      'su_person_cap_password' => [['value' => 'bar']],
    ]);
    $this->service::validateCredentials($element, $form_state, $form);
    $this->assertTrue($form_state::hasAnyErrors());
    $form_state->clearErrors();

    $this->guzzleBody = json_encode([
      'expires_in' => 100,
      'access_token' => 'foo-bar-baz',
    ]);
    $this->service::validateCredentials($element, $form_state, $form);
    $this->assertFalse($form_state::hasAnyErrors());
  }

  /**
   * Returned urls from the service will be properly formatted.
   */
  public function testUrls() {
    $url = $this->service->getOrganizationUrl('foo,bar');
    $this->assertEquals('https://cap.stanford.edu/cap-api/api/profiles/v1?orgCodes=FOO,BAR', $url);

    $url = $this->service->getOrganizationUrl('foo,bar', TRUE);
    $this->assertEquals('https://cap.stanford.edu/cap-api/api/profiles/v1?orgCodes=FOO,BAR&includeChildren=true', $url);

    $url = $this->service->getWorkgroupUrl('foo:bar_-baz');
    $this->assertEquals('https://cap.stanford.edu/cap-api/api/profiles/v1?privGroups=FOO:BAR_-BAZ', $url);

    $url = $this->service->getSunetUrl('foobarbaz');
    $this->assertEquals('https://cap.stanford.edu/cap-api/api/profiles/v1?uids=foobarbaz', $url);

    $sunets = implode(',', array_fill(0, 20, 'foo'));
    $url = $this->service->getSunetUrl($sunets);
    $this->assertEquals("https://cap.stanford.edu/cap-api/api/profiles/v1?uids=$sunets&ps=20", $url);
  }

  /**
   * The api will return an appropriate count.
   */
  public function testProfileCount() {
    $this->assertEquals(0, $this->service->getTotalProfileCount('http://localhost'));

    $this->guzzleBody = json_encode([
      'totalCount' => 123,
      'expires_in' => 100,
      'access_token' => 'foo',
    ]);
    $this->assertEquals(123, $this->service->getTotalProfileCount('http://localhost'));
  }

  /**
   * The org codes will be stored in taxonomy terms.
   */
  public function testUpdateOrgs() {
    $this->assertNull($this->service->updateOrganizations());
    $body = [
      'expires_in' => 100,
      'access_token' => 'foo',
      'name' => 'foo',
      'orgCodes' => ['foo', 'bar'],
      'children' => [['name' => 'baz', 'orgCodes' => ['baz']]],
    ];
    $this->guzzleBody = json_encode($body);

    $this->assertNull($this->service->updateOrganizations());

    $this->cacheData = new \stdClass();
    $this->cacheData->data = $body;
    $this->assertNull($this->service->updateOrganizations());
  }

  /**
   * Mock guzzle client request callback.
   */
  public function guzzleRequestCallback() {
    if ($this->guzzleStatusCode == 0) {
      throw new \Exception('It failed');
    }
    $response = $this->createMock(ResponseInterface::class);
    $response->method('getStatusCode')
      ->willReturnReference($this->guzzleStatusCode);
    $response->method('getBody')->willReturnReference($this->guzzleBody);
    return $response;
  }

  /**
   * Retain the numbers in the workgroups and organizations.
   */
  public function testNumbers() {
    $this->assertStringContainsString('FOO:BAR123', $this->service->getWorkgroupUrl('foo:bar123'));
    $this->assertStringContainsString('FOOBAR123', $this->service->getOrganizationUrl('foo:bar123'));
  }

}
