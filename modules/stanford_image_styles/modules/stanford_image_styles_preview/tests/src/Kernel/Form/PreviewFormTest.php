<?php

namespace Drupal\Tests\stanford_image_styles_preview\Kernel\Form;

use Drupal\Core\Form\FormState;
use Drupal\file\Entity\File;
use Drupal\image\Entity\ImageStyle;
use Drupal\KernelTests\KernelTestBase;

/**
 * Class PreviewFormTest.
 *
 * @coversDefaultClass \Drupal\stanford_image_styles_preview\Form\PreviewForm
 * @group stanford_image_styles
 */
class PreviewFormTest extends KernelTestBase {

  /**
   * The form namespace to test.
   *
   * @var string
   */
  protected $formId = '\Drupal\stanford_image_styles_preview\Form\PreviewForm';

  /**
   * Testable file entity.
   *
   * @var \Drupal\Core\Entity\EntityInterface
   */
  protected $file;

  /**
   * One of the generated image styles.
   *
   * @var \Drupal\image\ImageStyleInterface
   */
  protected $imageStyle;

  /**
   * {@inheritDoc}
   */
  protected static $modules = [
    'system',
    'file',
    'image',
    'user',
    'stanford_image_styles_preview',
  ];

  /**
   * {@inheritDoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('file');
    $this->installEntitySchema('user');
    $this->installEntitySchema('image_style');

    /** @var \Drupal\Core\File\FileSystemInterface $file_system */
    $file_system = \Drupal::service('file_system');
    $source_file = dirname(__FILE__, 3) . '/assets/logo.png';
    $destination = 'public://logo.png';
    $file_system->copy($source_file, $destination, TRUE);

    $this->file = File::create(['uri' => $destination]);
    $this->file->save();

    $style = ImageStyle::create(['name' => $this->randomMachineName()]);
    $style->save();

    $this->imageStyle = ImageStyle::create([
      'name' => $this->randomMachineName(),
      'effects' => [
        'ddd73aa7-4bd6-4c85-b600-bdf2b1628d1d' => [
          'uuid' => 'ddd73aa7-4bd6-4c85-b600-bdf2b1628d1d',
          'weight' => 0,
          'id' => 'image_scale',
          'data' => ['width' => 200, 'height' => 200, 'upscale' => FALSE],
        ],
      ],
    ]);
    $this->imageStyle->save();
  }

  /**
   * Test the structure and display of the form.
   */
  public function testForm() {
    $form_state = new FormState();
    $form = \Drupal::formBuilder()->buildForm($this->formId, $form_state);
    $form_object = $form_state->getFormObject();
    $this->assertCount(29, $form);
    $this->assertEquals('stanford_image_styles_preview_form', $form_object->getFormId());

    $form_state->setValue('image', [$this->file->id()]);
    $form_object->submitForm($form, $form_state);
    $this->assertTrue($form_state->isRebuilding());

    $form = \Drupal::formBuilder()->buildForm($this->formId, $form_state);

    $expected_url = "public://styles/{$this->imageStyle->id()}/public/stanford_image_styles_preview.png";
    $this->assertEquals($expected_url, $form['preview'][$this->imageStyle->id()]['image']['#uri']);
  }

}
