<?php

namespace Drupal\cardinal_service_migrate\Plugin\migrate\process;

use Drupal\Component\Transliteration\TransliterationInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a custom_skip_empty_divs plugin.
 *
 * Usage:
 *
 * @code
 * process:
 *   bar:
 *     plugin: custom_skip_empty_divs
 *     source: foo
 * @endcode
 *
 * @MigrateProcessPlugin(
 *   id = "custom_skip_empty_divs"
 * )
 *
 * @DCG
 * ContainerFactoryPluginInterface is optional here. If you have no need for
 * external services just remove it and all other stuff except transform()
 * method.
 */
class CustomSkipEmptyDivs extends ProcessPluginBase implements ContainerFactoryPluginInterface {

  /**
   * The transliteration service.
   *
   * @var \Drupal\Component\Transliteration\TransliterationInterface
   */
  protected $transliteration;

  /**
   * Constructs a CustomSkipEmptyDivs plugin.
   *
   * @param array $configuration
   *   The plugin configuration.
   * @param string $plugin_id
   *   The plugin ID.
   * @param mixed $plugin_definition
   *   The plugin definition.
   * @param \Drupal\Component\Transliteration\TransliterationInterface $transliteration
   *   The transliteration service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, TransliterationInterface $transliteration) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->transliteration = $transliteration;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('transliteration')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // Check if the source value is HTML.
    if (is_string($value) && ($dom = new \DOMDocument()) && @$dom->loadHTML($value)) {
      // Check for empty divs with a class attribute and skip processing if found.
      $xpath = new \DOMXPath($dom);
      $divs = $xpath->query('//div[@class and not(node())]');
      if ($divs->length == 0) {
        // Skip processing if empty divs with a class are found.
        return NULL;
      }
    }

    // Return the original value if no empty divs are detected.}.
    return $this->transliteration->transliterate($value, LanguageInterface::LANGCODE_DEFAULT);
  }

}
