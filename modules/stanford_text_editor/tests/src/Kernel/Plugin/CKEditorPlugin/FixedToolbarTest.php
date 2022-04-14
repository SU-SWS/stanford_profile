<?php

namespace Drupal\Tests\stanford_text_editor\Kernel\Plugin\CkeditorPlugin;

use Drupal\editor\Entity\Editor;
use Drupal\KernelTests\KernelTestBase;

/**
 * Class FixedToolbarTest
 *
 * @group stanford_text_editor
 */
class FixedToolbarTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['system', 'ckeditor', 'stanford_text_editor'];

  /**
   * Fixed toolbar plugin exists.
   */
  public function testFixedToolbarPlugin() {
    /** @var \Drupal\ckeditor\CKEditorPluginManager $ckeditor_plugin_manager */
    $ckeditor_plugin_manager = \Drupal::service('plugin.manager.ckeditor.plugin');
    $this->assertArrayHasKey('fixed_toolbar', $ckeditor_plugin_manager->getDefinitions());
    /** @var \Drupal\stanford_text_editor\Plugin\CKEditorPlugin\FixedToolbar $plugin */
    $plugin = $ckeditor_plugin_manager->createInstance('fixed_toolbar');
    $this->assertStringContainsString('plugin.js', $plugin->getFile());

    $editor = $this->createMock(Editor::class);

    $this->assertEmpty($plugin->getLibraries($editor));
    $this->assertEmpty($plugin->getConfig($editor));
    $this->assertEmpty($plugin->getButtons($editor));
    $this->assertTrue($plugin->isEnabled($editor));
  }

}
