<?php

namespace Drupal\cardinal_service_blocks\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\RendererInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class NewsletterForm.
 *
 * @package Drupal\cardinal_service_blocks\Form
 */
class NewsletterForm extends FormBase {

  /**
   * Guzzle client service.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $guzzle;

  /**
   * Rendering service.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('http_client'), $container->get('renderer'));
  }

  /**
   * NewsletterForm constructor.
   *
   * @param \GuzzleHttp\ClientInterface $guzzle
   *   Guzzle http client service.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   Rendering service.
   */
  public function __construct(ClientInterface $guzzle, RendererInterface $renderer) {
    $this->guzzle = $guzzle;
    $this->renderer = $renderer;
  }

  /**
   * {@inheritDoc}
   */
  public function getFormId() {
    return 'cardinal_service_blocks_newsletter';
  }

  /**
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#action'] = $form_state->getBuildInfo()['action_url'];
    $form['inputs'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['centered-container', 'flex-container']],
      '#prefix' => '<div class="form-message"></div>',
    ];
    $form['inputs']['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email Address'),
      '#title_display' => 'invisible',
      '#attributes' => ['placeholder' => 'Email Address'],
    ];
    $form['inputs']['signup_submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Sign-Up'),
      '#name' => 'signup_submit',
      '#ajax' => [
        'callback' => '::ajaxSubmit',
      ],
    ];
    return $form;
  }

  /**
   * Ajax submit handler for the newsletter sign up.
   *
   * @param array $form
   *   Complete Form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Current form state.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   *   Ajax response commands.
   */
  public function ajaxSubmit(array &$form, FormStateInterface $form_state) {
    $selector = '.cardinal-service-blocks-newsletter';
    $response = new AjaxResponse();

    if (!$form_state->getValue('email')) {
      $message = $this->getFormMessage('error', $this->t('Email address is required'));
      $response->addCommand(new ReplaceCommand("$selector .form-message", $message));
      return $response;
    }

    try {
      $post_response = $this->guzzle->request('POST', $form_state->getBuildInfo()['action_url'], ['form_params' => ['MERGE0' => $form_state->getValue('email')]]);
      if (strpos((string) $post_response->getBody(), 'errors below') !== FALSE) {
        throw new \Exception($this->t('Errors occurred in in the form submission.'));
      }

      $message = $this->getFormMessage('success', $this->t('Thank you for signing up to receive the newsletter emails.'));
      return $response->addCommand(new ReplaceCommand($selector, $message));
    }
    catch (\Exception | GuzzleException $e) {
      $this->logger('cardinal_service')
        ->error($this->t('Error submitting newsletter form: @message', ['@message' => $e->getMessage()]));
      $message = $this->getFormMessage('error', $this->t('Unable to sign up for email newsletter at this time. Please try again later.'));
    }

    $response->addCommand(new ReplaceCommand("$selector .form-message", $message));
    return $response;
  }

  /**
   * Get the rendered message markup to return to the user.
   *
   * @param string $status
   *   Message status: success, error, or notice.
   * @param string|\Drupal\Core\StringTranslation\TranslatableMarkup $message
   *   Text message to tell the user.
   *
   * @return string
   *   Rendered html message.
   */
  protected function getFormMessage($status, $message) {
    $form_message = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'form-message',
          'su-alert',
          "su-alert--$status",
        ],
      ],
      'message' => [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#value' => $message,
        '#attributes' => ['class' => ['su-alert__body']],
      ],
    ];
    return (string) $this->renderer->renderPlain($form_message);
  }

  /**
   * {@inheritDoc}
   *
   * @codeCoverageIgnore
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Nothing to do since the form will submit off site or via ajax.
  }

}
