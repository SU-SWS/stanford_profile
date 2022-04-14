<?php

namespace Drupal\stanford_profile_helper\Plugin\Filter;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\filter\FilterProcessResult;
use Drupal\mathjax\Plugin\Filter\MathjaxFilter;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Overrides the Mathjax filter to prevent an unwanted div from the markup.
 */
class Mathjax extends MathjaxFilter implements ContainerFactoryPluginInterface {

  /**
   * Mathajax settings config.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $config;

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritDoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactoryInterface $config_factory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->config = $config_factory->get('mathjax.settings');
  }

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode) {
    $result = new FilterProcessResult($text);
    $config_type = $this->config->get('config_type');
    if ($config_type == 0 && preg_match('/((\$\$.*\$\$)|(\\\(.*\\\))|(\\\[.*\\\]))/', $text)) {
      $result->setAttachments(['library' => ['mathjax/source']]);
    }
    return $result;
  }

}
