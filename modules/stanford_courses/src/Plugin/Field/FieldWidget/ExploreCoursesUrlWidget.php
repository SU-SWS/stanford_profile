<?php

namespace Drupal\stanford_courses\Plugin\Field\FieldWidget;

use Drupal\Component\Utility\NestedArray;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\link\Plugin\Field\FieldWidget\LinkWidget;
use GuzzleHttp\ClientInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'explore_courses_url' widget.
 *
 * @FieldWidget(
 *   id = "explore_courses_url",
 *   label = @Translation("ExploreCourses"),
 *   field_types = {
 *     "link"
 *   }
 * )
 */
class ExploreCoursesUrlWidget extends LinkWidget {

  /**
   * Http Client Service.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $client;

  /**
   * {@inheritDoc}.
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings'],
      $container->get('http_client')
    );
  }

  /**
   * {@inheritDoc}.
   */
  public static function defaultSettings() {
    $settings = ['api_version' => '20200810'];
    return $settings + parent::defaultSettings();
  }

  /**
   * {@inheritDoc}.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, array $third_party_settings, ClientInterface $client) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
    $this->client = $client;
  }

  /**
   * {@inheritDoc}.
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements['api_version'] = [
      '#type' => 'textfield',
      '#title' => $this->t('API version'),
      '#required' => TRUE,
      '#default_value' => $this->getSetting('api_version'),
      '#access' => TRUE,
      '#element_validate' => [
        [$this, 'validateApi'],
      ],
    ];
    return $elements;
  }

  /**
   * Validate we are specifying the newest api.
   *
   * @param array $element
   *   Url form element.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Current form state object.
   * @param array $complete_form
   *   Complete form.
   */
  public function validateApi(array &$element, FormStateInterface $form_state, array &$complete_form) {
    $input = NestedArray::getValue($form_state->getValues(), $element['#parents']);

    if ($form_state::hasAnyErrors()) {
      return;
    }
    try {
      $response = $this->client->request('GET', 'search?view=xml-' . $input, ['base_uri' => 'https://explorecourses.stanford.edu/']);
      $response = (string) $response->getBody();
      libxml_use_internal_errors();
      $xml = new \SimpleXMLElement($response);
      // Do this as a string, since SimpleXMLElement doesn't cast to bools.
      if ((string) $xml->deprecated == 'true') {
        $form_state->setError($element, $this->t("That API version is deprecated. Newest version is: $xml->latestVersion"));
      }
    }
    catch (\Throwable $e) {
      $form_state->setError($element, $this->t('There was a problem querying the ExploreCourses API.'));
    }
  }

  /**
   * {@inheritDoc}.
   */
  public function settingsSummary() {
    $summary = [];
    if (empty($this->getSetting('api_version'))) {
      $summary[] = $this->t('No API version Provided');
    }
    else {
      $summary[] = $this->t('API version: @api_version', ['@api_version' => $this->getSetting('api_version')]);
    }
    return $summary;
  }

  /**
   * {@inheritDoc}.
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    $element['uri']['#element_validate'][] = [$this, 'validateUrl'];
    $element['uri']['#description'] = $this->t('This must be a valid ExplorerCourses URL. See: @url', ['@url' => 'https://explorecourses.stanford.edu']);
    $element['title']['#access'] = FALSE;
    $element['attributes']['#access'] = FALSE;

    return $element;
  }

  /**
   * Validate we have a legit url.
   *
   * @param array $element
   *   Url form element.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Current form state object.
   * @param array $complete_form
   *   Complete form.
   */
  public function validateUrl(array &$element, FormStateInterface $form_state, array &$complete_form) {
    $url = UrlHelper::parse($element['#value']);
    if (!empty($url['path']) && !str_contains($url['path'], 'explorecourses')) {
      $form_state->setError($element, $this->t('The URL is not a valid ExploreCourses URL.'));
    }
  }

  /**
   * {@inheritDoc}.
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {

    // Something like `view=xml-20200810`, but it may change.
    $xml_querystring = 'xml-' . $this->getSetting('api_version');

    foreach ($values as $delta => &$value) {
      if (!empty($value['uri'])) {
        // Parse the existing URL.
        $url = UrlHelper::parse($value['uri']);

        // Ensure the view parameter is the proper value.
        $url['query']['view'] = $xml_querystring;

        $massaged_url = Url::fromUri($url['path'], ['query' => $url['query']]);
        $values[$delta]['uri'] = $massaged_url->toString();
      }
    }
    return parent::massageFormValues($values, $form, $form_state);
  }

}
