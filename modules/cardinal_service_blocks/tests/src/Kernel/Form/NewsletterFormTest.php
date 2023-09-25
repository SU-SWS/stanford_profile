<?php

namespace Drupal\Tests\cardinal_service_blocks\Kernel\Form;

use Drupal\cardinal_service_blocks\Form\NewsletterForm;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Form\FormState;
use Drupal\KernelTests\KernelTestBase;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Stream;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class NewsletterFormTest
 *
 * @group cardinal_service_profile
 * @coversDefaultClass \Drupal\cardinal_service_blocks\Form\NewsletterForm
 */
class NewsletterFormTest extends KernelTestBase {

  /**
   * If the guzzle client should fail with an error.
   *
   * @var bool
   */
  protected $failGuzzle = FALSE;

  /**
   * The guzzle response body.
   */
  protected $responseBody;

  /**
   * {@inheritDoc}
   */
  protected static $modules = [
    'cardinal_service_blocks',
    'system',
    'block',
  ];

  public function setup(): void {
    parent::setUp();
    $resource = fopen('php://memory','r+');
    $this->responseBody = new Stream($resource);

    $client = $this->createMock(ClientInterface::class);
    $client->method('request')
      ->will($this->returnCallback([$this, 'guzzleRequestCallback']));

    $container = \Drupal::getContainer();
    $container->set('http_client', $client);
    \Drupal::setContainer($container);
  }

  public function testSubmit() {
    $form_state = new FormState();
    $form_state->addBuildInfo('action_url', 'http://localhost');
    $form = \Drupal::formBuilder()
      ->buildForm(NewsletterForm::class, $form_state);
    $form_object = $form_state->getBuildInfo()['callback_object'];

    // No email entered.
    $commands = $form_object->ajaxSubmit($form, $form_state)->getCommands();
    $this->assertStringContainsString('su-alert--error', $commands[0]['data']);
    $this->assertStringContainsString('Email address is required', $commands[0]['data']);

    $form_state->setValue('email', 'foo@bar.com');

    // Successful ajax.
    $commands = $form_object->ajaxSubmit($form, $form_state)->getCommands();
    $this->assertStringContainsString('su-alert--success', $commands[0]['data']);
    $this->assertStringContainsString('Thank you', $commands[0]['data']);

    // Form Submission Failure
    $resource = fopen('php://memory','r+');
    fwrite($resource, '<div>There are errors below</div>');
    rewind($resource);

    $this->responseBody = new Stream($resource);
    $commands = $form_object->ajaxSubmit($form, $form_state)->getCommands();
    $this->assertStringContainsString('su-alert--error', $commands[0]['data']);
    $this->assertStringContainsString('Unable to sign up', $commands[0]['data']);

    // Failing guzzle.
    $this->failGuzzle = TRUE;
    $commands = $form_object->ajaxSubmit($form, $form_state)->getCommands();
    $this->assertStringContainsString('su-alert--error', $commands[0]['data']);
    $this->assertStringContainsString('Unable to sign up', $commands[0]['data']);
  }

  /**
   * Guzzle client request callback.
   */
  public function guzzleRequestCallback() {
    if ($this->failGuzzle) {
      $request = $this->createMock(RequestInterface::class);
      $response = $this->createMock(ResponseInterface::class);
      throw new ClientException('Failed', $request, $response);
    }

    $response = $this->createMock(ResponseInterface::class);
    $response->method('getBody')->willReturnReference($this->responseBody);
    return $response;
  }

}
