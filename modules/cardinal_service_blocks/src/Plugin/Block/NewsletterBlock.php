<?php

namespace Drupal\cardinal_service_blocks\Plugin\Block;

use Drupal\cardinal_service_blocks\Form\NewsletterForm;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormState;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'NewsletterBlock' block.
 *
 * @Block(
 *  id = "newsletter_block",
 *  admin_label = @Translation("Newsletter block"),
 * )
 */
class NewsletterBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Form builder service.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  protected $formBuilder;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('form_builder')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, FormBuilderInterface $form_builder) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->formBuilder = $form_builder;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'url' => 'https://stanford.us1.list-manage.com/subscribe/post?u=a77525a849b0888cf8d90460f&id=807864fbe3',
      'intro' => ['value' => NULL, 'format' => NULL],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['url'] = [
      '#type' => 'url',
      '#title' => $this->t('Form Action URL'),
      '#default_value' => $this->configuration['url'],
      '#required' => TRUE,
    ];
    $form['intro'] = [
      '#type' => 'text_format',
      '#title' => $this->t('Intro Text'),
      '#default_value' => $this->configuration['intro']['value'],
      '#format' => $this->configuration['intro']['format'],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['url'] = $form_state->getValue('url');
    $this->configuration['intro'] = $form_state->getValue('intro');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    if (!empty($this->configuration['intro']['value'])) {
      $build['intro'] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['centered-container']],
        'text' => [
          '#type' => 'processed_text',
          '#text' => $this->configuration['intro']['value'],
          '#format' => $this->configuration['intro']['format'],
        ],
      ];
    }
    $form_state = new FormState();
    $form_state->addBuildInfo('action_url', $this->configuration['url']);
    $build['form'] = $this->formBuilder->buildForm(NewsletterForm::class, $form_state);
    return $build;
  }

}
