<?php

namespace Drupal\stanford_text_editor\Plugin\CKEditorPlugin;

use Drupal\ckeditor\CKEditorPluginBase;
use Drupal\ckeditor\CKEditorPluginContextualInterface;
use Drupal\Core\Extension\ExtensionList;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\editor\Entity\Editor;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the "fixed_toolbar" plugin.
 *
 * @CKEditorPlugin(
 *   id = "fixed_toolbar",
 *   label = @Translation("Fixed Toolbar"),
 *   module = "ckeditor_fixed_toolbar"
 * )
 */
class FixedToolbar extends CKEditorPluginBase implements CKEditorPluginContextualInterface {

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    $module_path = $this->getModuleList()->getPath('stanford_text_editor');
    return $module_path . '/js/plugins/fixed_toolbar/plugin.js';
  }

  /**
   * {@inheritdoc}
   */
  public function getLibraries(Editor $editor) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(Editor $editor) {
    return [];
  }

  /**
   * Returns the buttons that this plugin provides, along with metadata.
   *
   * The metadata is used by the CKEditor module to generate a visual CKEditor
   * toolbar builder UI.
   *
   * @return array
   *   An array of buttons that are provided by this plugin. This will
   *   only be used in the administrative section for assembling the toolbar.
   *   Each button should be keyed by its CKEditor button name (you can look up
   *   the button name up in the plugin.js file), and should contain an array of
   *   button properties, including:
   *   - label: A human-readable, translated button name.
   *   - image: An image for the button to be used in the toolbar.
   *   - image_rtl: If the image needs to have a right-to-left version, specify
   *     an alternative file that will be used in RTL editors.
   *   - image_alternative: If this button does not render as an image, specify
   *     an HTML string representing the contents of this button.
   *   - image_alternative_rtl: Similar to image_alternative, but a
   *     right-to-left version.
   *   - attributes: An array of HTML attributes which should be added to this
   *     button when rendering the button in the administrative section for
   *     assembling the toolbar.
   *   - multiple: Boolean value indicating if this button may be added multiple
   *     times to the toolbar. This typically is only applicable for dividers
   *     and group indicators.
   */
  public function getButtons() {
    return [];
  }

  /**
   * Checks if this plugin should be enabled based on the editor configuration.
   *
   * The editor's settings can be retrieved via $editor->getSettings().
   *
   * @param \Drupal\editor\Entity\Editor $editor
   *   A configured text editor object.
   *
   * @return bool
   *   Return TRUE when this plugin is enabled.
   */
  public function isEnabled(Editor $editor) {
    return TRUE;
  }

}
