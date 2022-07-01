<?php

namespace Drupal\Tests\jumpstart_ui\Kernel\Layout;

use Drupal\KernelTests\KernelTestBase;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Drupal\Core\Template\Attribute;
use Twig\Loader\FilesystemLoader;

/**
 * Class OneColLayoutTest.
 *
 * @group jumpstart_ui
 */
class OneColLayoutTest extends KernelTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['system'];


  /**
   * {@inheritdoc}
   */
  public function register(ContainerBuilder $container) {
    parent::register($container);

    $container->setDefinition('twig_loader__file_system', new Definition(FilesystemLoader::class, [[dirname(__FILE__, 5) . '/templates/layouts/']]))
      ->addTag('twig.loader');

    require_once DRUPAL_ROOT . '/core/themes/engines/twig/twig.engine';
  }

  /**
   * Layout should render when values are passed..
   */
  public function testOneColLayoutFullProps() {
    // Boot twig environment.
    $twig = \Drupal::service('twig');
    $template = \Drupal::service('extension.list.module')
        ->getPath('jumpstart_ui') . '/templates/layouts/one-column.html.twig';
    $props = $this->getProps();
    $this->setRawContent((string) twig_render_template($template, $props));
    $this->assertText("Somebody once told me php unit is gonna rule me");
    $this->assertStringContainsString("boy-is-this-a-neat-class", $this->getRawContent());
    $this->assertStringContainsString("flex-12-of-12", $this->getRawContent());
    $this->assertStringContainsString('jumpstart-ui--one-column', $this->getRawContent());
  }

  /**
   * Layout should render when values are passed..
   */
  public function testOneColLayoutColumnProps() {
    // Boot twig environment.
    $twig = \Drupal::service('twig');
    $template = \Drupal::service('extension.list.module')
        ->getPath('jumpstart_ui') . '/templates/layouts/one-column.html.twig';
    $props = $this->getProps();
    $props['settings']['columns'] = 'flex-6-of-12';
    $this->setRawContent((string) twig_render_template($template, $props));
    $this->assertText("Somebody once told me php unit is gonna rule me");
    $this->assertStringContainsString("boy-is-this-a-neat-class", $this->getRawContent());
    $this->assertStringContainsString("flex-6-of-12", $this->getRawContent());
    $this->assertStringContainsString('jumpstart-ui--one-column', $this->getRawContent());
  }

  /**
   * Layout should render when values are passed..
   */
  public function testOneColLayoutNoProps() {
    // Boot twig environment.
    $twig = \Drupal::service('twig');
    $template = \Drupal::service('extension.list.module')
        ->getPath('jumpstart_ui') . '/templates/layouts/one-column.html.twig';
    $this->setRawContent((string) twig_render_template($template, []));
    $this->assertNotEmpty($this->getRawContent());
    $this->assertStringNotContainsString("boy-is-this-a-neat-class", $this->getRawContent());
    $this->assertStringNotContainsString("flex-12-of-12", $this->getRawContent());
    $this->assertStringNotContainsString('jumpstart-ui--one-column', $this->getRawContent());
  }

  /**
   * Layout should render when values are passed..
   */
  public function testOneColLayoutBadProps() {
    // Boot twig environment.
    $twig = \Drupal::service('twig');
    $template = \Drupal::service('extension.list.module')
        ->getPath('jumpstart_ui') . '/templates/layouts/one-column.html.twig';
    $props = $this->getProps();
    unset($props['region_attributes']);
    $this->setRawContent((string) twig_render_template($template, $props));
    $this->assertText("Somebody once told me php unit is gonna rule me");
    $this->assertStringContainsString("boy-is-this-a-neat-class", $this->getRawContent());
    $this->assertStringNotContainsString("flex-12-of-12", $this->getRawContent());
    $this->assertStringContainsString('jumpstart-ui--one-column', $this->getRawContent());
  }

  /**
   * @return array
   */
  protected function getProps() {
    return [
      'content' => ['main' => "Somebody once told me php unit is gonna rule me"],
      'region_attributes' => [
        'main' => new Attribute(['class' => 'main-region-attribute']),
      ],
      'settings' => [
        'extra_classes' => "boy-is-this-a-neat-class",
        'centered' => 'centered-container',
        'columns' => 'default',
      ],
      'attributes' => new Attribute(['class' => 'wrapper-test']),
    ];
  }

}
