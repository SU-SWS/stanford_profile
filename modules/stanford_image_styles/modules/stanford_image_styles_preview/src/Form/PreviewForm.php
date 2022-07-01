<?php

namespace Drupal\stanford_image_styles_preview\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ExtensionList;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Render\RendererInterface;
use Drupal\image\ImageStyleInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class PreviewForm.
 */
class PreviewForm extends FormBase {

  /**
   * Entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Render service.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * File Url Generator service.
   *
   * @var \Drupal\Core\File\FileUrlGeneratorInterface
   */
  protected $fileUrlGenerator;

  /**
   * Module extension list service.
   *
   * @var \Drupal\Core\Extension\ExtensionList
   */
  protected $moduleList;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('renderer'),
      $container->get('file_url_generator'),
      $container->get('extension.list.module')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, RendererInterface $renderer, FileUrlGeneratorInterface $file_url_generator, ExtensionList $module_list) {
    $this->entityTypeManager = $entity_type_manager;
    $this->renderer = $renderer;
    $this->fileUrlGenerator = $file_url_generator;
    $this->moduleList = $module_list;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'stanford_image_styles_preview_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['image'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Image'),
      '#description' => $this->t('Upload an image to see how the image styles react'),
      '#required' => TRUE,
      '#upload_validators' => [
        'file_validate_extensions' => ['gif png jpg jpeg'],
      ],
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];
    $this->buildPreview($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Just rebuild the form with the new previews.
    $form_state->setRebuild();
  }

  /**
   * Builds the preview portions of the form using a an image.
   *
   * @param array $form
   *   Simple form api array.
   * @param FormStateInterface $form_state
   *   Form State from building the form.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  private function buildPreview(array &$form, FormStateInterface &$form_state) {
    /** @var \Drupal\image\ImageStyleInterface[] $styles */
    $styles = $this->entityTypeManager->getStorage('image_style')
      ->loadMultiple();

    $file_uri = $this->moduleList->getPath('stanford_image_styles_preview') . '/img/preview_image.jpg';

    // If the form is being rebuild, we can grab the image and load it.
    if ($image = $form_state->getValue('image')) {
      $file = $this->entityTypeManager->getStorage('file')
        ->load(is_array($image) ? reset($image) : $image);
      if ($file) {
        $file_uri = $file->getFileUri();
      }
    }

    $form['styles']['original'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Original'),
    ];
    $form['styles']['original']['preview'] = [
      '#markup' => '<img src="' . $this->fileUrlGenerator->generateAbsoluteString($file_uri) . '" />',
    ];

    foreach ($styles as $style) {
      $form['preview'][$style->id()] = $this->buildStylePreview($style, $file_uri);
    }

  }

  /**
   * Builds the information about the style including all effects.
   *
   * @param \Drupal\image\ImageStyleInterface $style
   *   Image style to build preview.
   * @param string $file_uri
   *   Original file uri.
   *
   * @return array
   *   Form api array of the summary of effects on the style and the preview.
   *
   * @throws \Drupal\Core\Entity\EntityMalformedException
   */
  private function buildStylePreview(ImageStyleInterface $style, $file_uri) {
    $element = [
      '#type' => 'fieldset',
      '#title' => $style->label(),
    ];

    if ($style->getEffects()->getInstanceIds()) {
      $element['effects'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('Effects'),
      ];

      $element['effects']['summary'] = $this->buildEffectSummary($style);;
    }

    $element['edit'] = [
      '#prefix' => '<div class="clearfix">',
      '#suffix' => '</div>',
      '#markup' => Link::fromTextAndUrl($this->t('Edit Style'), $style->toUrl())->toString(),
    ];

    $element['image'] = [
      '#theme' => 'image',
      '#uri' => $this->getStyleDerivative($style, $file_uri),
    ];

    return $element;
  }

  /**
   * Create the image derivative and get the derivative url.
   *
   * @param \Drupal\image\ImageStyleInterface $style
   *   The Image style to create a derivative and return that uri.
   * @param string $file_uri
   *   The original URI of the file.
   *
   * @return string
   *   THe style derivative URI.
   */
  private function getStyleDerivative(ImageStyleInterface $style, $file_uri) {
    $derivative = explode('/', $style->buildUri($file_uri));
    $file_name = array_pop($derivative);
    $extension = substr($file_name, strrpos($file_name, '.') + 1);
    // Change the file name.
    $derivative[] = 'stanford_image_styles_preview.' . $extension;
    // Temporary image styles break so we create it in public.
    $derivative = str_replace('temporary://', 'public://', implode('/', $derivative));
    $style->createDerivative($file_uri, $derivative);
    return $derivative;
  }

  /**
   * Build a list of all effects on a image style.
   *
   * @param \Drupal\image\ImageStyleInterface $style
   *   Image style to build list of effects.
   *
   * @return array
   *   Render array of a list of effect summaries.
   */
  private function buildEffectSummary(ImageStyleInterface $style) {
    $effects = $style->getEffects();
    $list = [];

    foreach ($effects->getInstanceIds() as $effect_id) {
      $effect = $effects->get($effect_id);

      $effect_info = $effect->label();
      $summary = $effect->getSummary();

      if (!empty($summary)) {
        $effect_info .= ': ' . $this->renderer->renderPlain($summary);
      }
      $list[] = $effect_info;
    }

    return [
      '#theme' => 'item_list',
      '#items' => $list,
    ];
  }

}
