<?php

namespace Drupal\Tests\jumpstart_ui\Kernel\Layout;

use Drupal\KernelTests\KernelTestBase;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Drupal\Core\Template\Attribute;
use Twig\Loader\FilesystemLoader;


/**
 * Class ThreeColLayoutTest.
 *
 * @group jumpstart_ui
 */
class ThreeColLayoutTest extends KernelTestBase {

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
  public function testThreeColLayoutFullProps() {
    // Boot twig environment.
    $twig =  \Drupal::service('twig');
    $template = \Drupal::service('extension.list.module')->getPath('jumpstart_ui') . '/templates/layouts/three-column.html.twig';
    $props = $this->getProps();
    $this->setRawContent((string) twig_render_template($template, $props));
    $this->assertText("Somebody once told me php unit is gonna rule me");
    $this->assertText("This aint the most fun that Ive had");
    $this->assertText("Im looking for the right class to base my tests off of and I wanna go straight to bed");
    $this->assertStringContainsString("boy-is-this-a-neat-class", $this->getRawContent());
    $this->assertStringContainsString("flex-lg-6-of-12", $this->getRawContent());
    $this->assertStringContainsString('jumpstart-ui--three-column', $this->getRawContent());
  }

  /**
   * @return array
   */
  protected function getProps() {
    return [
      'content' => [
        'left' => "Somebody once told me php unit is gonna rule me",
        'main' => "This aint the most fun that Ive had",
        'right' => "Im looking for the right class to base my tests off of and I wanna go straight to bed.",
      ],
      'region_attributes' => [
        'main' =>  new Attribute(['class' => 'main-region-attribute']),
      ],
      'settings' => [
        'extra_classes' => "boy-is-this-a-neat-class",
        'centered' => 'centered-container',
      ],
      'attributes' => new Attribute(['class' => 'wrapper-test']),
    ];
  }

}
