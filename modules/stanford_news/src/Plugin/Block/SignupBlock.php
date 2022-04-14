<?php

namespace Drupal\stanford_news\Plugin\Block;

use Drupal\Core\Access\AccessResultForbidden;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides a 'Newsletter Signup' Block.
 *
 * @Block(
 *   id = "signup_block",
 *   admin_label = @Translation("Newsletter Signup"),
 *   category = @Translation("Stanford News"),
 * )
 */
class SignupBlock extends BlockBase implements BlockPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function access(AccountInterface $account, $return_as_object = FALSE) {
    if (empty($this->configuration['form_action'])) {
      $access = new AccessResultForbidden();
      return $return_as_object ? $access : $access->isAllowed();
    }
    return parent::access($account, $return_as_object);
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);
    $config = $this->getConfiguration();

    $form['form_action'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Stanford Mailing List Subscribe URL'),
      '#description' => $this->t('Example: Get a mailchimp url to be placed here'),
      '#default_value' => isset($config['form_action']) ? $config['form_action'] : '',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $values = $form_state->getValues();
    $this->configuration['form_action'] = $values['form_action'];
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();
    return [
      '#theme' => 'signup_block',
      '#form_action' => $config['form_action'],
    ];
  }

}
