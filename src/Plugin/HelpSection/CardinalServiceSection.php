<?php

namespace Drupal\cardinal_service_profile\Plugin\HelpSection;

use Drupal\Core\Link;
use Drupal\help\Plugin\HelpSection\HelpSectionPluginBase;
use Michelf\MarkdownExtra;

/**
 * Provides the module topics list section for the help page.
 *
 * @HelpSection(
 *   id = "cardinal_service",
 *   title = @Translation("Cardinal Service"),
 *   description =  @Translation(""),
 *   weight = -1000
 * )
 */
class CardinalServiceSection extends HelpSectionPluginBase {

  /**
   * {@inheritdoc}
   */
  public function listTopics() {
    $links = [];
    $profile_path = $this::getProfilePath();

    foreach (glob("$profile_path/help/*.md") as $help_file) {
      $help_html = MarkdownExtra::defaultTransform(file_get_contents($help_file));
      $h1 = preg_grep('/<h1>.*?<\/h1>/', explode("\n", $help_html));
      $document = substr(basename($help_file), 0, -3);
      $links[] = Link::createFromRoute(strip_tags($h1[0]), 'cardinal_service.help_document', ['document' => $document])
        ->toRenderable();
    }
    return $links;
  }

  /**
   * Get the path to the CS profile.
   *
   * @codeCoverageIgnore Unit tests wont work with the function call.
   *
   * @return string
   *   Path to the CS profile
   */
  protected static function getProfilePath() {
    return \Drupal::service('extension.list.profile')->getPath('cardinal_service_profile');
  }

}
