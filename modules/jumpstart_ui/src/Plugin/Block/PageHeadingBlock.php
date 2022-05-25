<?php

namespace Drupal\jumpstart_ui\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a block that outputs an h1 tag.
 *
 * @Block(
 *   id = "jumpstart_ui_page_heading",
 *   admin_label = @Translation("Heading Block"),
 * )
 */
class PageHeadingBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    $config = parent::defaultConfiguration();
    $config['heading_text'] = "";
    $config['wrapper'] = "h1";
    return $config;
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);
    $config = $this->getConfiguration();

    $form['heading_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('The heading text'),
      '#description' => $this->t('Plain text only in this field as it will be wrapped with a header tag.'),
      '#default_value' => $config['heading_text'] ?? '',
    ];

    $form['wrapper'] = [
      '#type' => 'select',
      '#title' => $this->t('Heading level'),
      '#description' => $this->t('Select the level of heading you wish to render'),
      '#options' => [
        'h1' => $this->t('H1'),
        'h2' => $this->t('H2'),
        'h3' => $this->t('H3'),
        'h4' => $this->t('H4'),
        'h5' => $this->t('H5'),
        'h6' => $this->t('H6'),
      ],
      '#default_value' => $config['wrapper'] ?? "h1",
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $values = $form_state->getValues();
    $this->configuration['heading_text'] = $values['heading_text'];
    $this->configuration['wrapper'] = $values['wrapper'];
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();
    return [
      'heading' => [
        '#type' => 'html_tag',
        '#tag' => $config['wrapper'],
        '#value' => $config['heading_text'] ?? $this->t("No text provided"),
        '#attributes' => [
          'class' => 'heading-' . $config['wrapper'],
        ],
      ],
    ];
  }

}
