<?php

namespace Drupal\stanford_profile\Plugin\HelpSection;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\help\Attribute\HelpSection;
use Drupal\help\Plugin\HelpSection\HelpSectionPluginBase;

/**
 * Provides the module topics list section for the help page.
 */
#[HelpSection(
  id: 'profile_resources',
  title: new TranslatableMarkup('Other resources at Stanford'),
  description: new TranslatableMarkup('Stanford has other centrally offered resources for web editors.'),
  weight: -50
)]
class ProfileResourceSection extends HelpSectionPluginBase {

  use ProfileHelpTrait;

  /**
   * {@inheritdoc}
   */
  public function listTopics() {
    return [
      $this->getSoda(),
      $this->getTechTraining(),
    ];
  }

  /**
   * Get the user guide help text.
   *
   * @return array
   *   Markup render array.
   */
  protected function getTechTraining() {
    $help = '<h3>' . self::getLinkString($this->t('Tech Training:'), 'https://itservices.stanford.edu/service/techtraining/schedule') . '</h3>';
    $help .= '<p>' . $this->t('See upcoming courses on site editing.') . '</p>';
    return ['#markup' => $help];
  }

  /**
   * Get the user guide help text.
   *
   * @return array
   *   Markup render array.
   */
  protected function getSoda() {
    $help = '<h3>' . self::getLinkString($this->t('Office of Digital Accessibility'), 'https://uit.stanford.edu/accessibility') . '</h3>';
    $help .= '<p>' . $this->t('Get assistance with identifying issues and improving the accessibility of your site.') . '</p>';
    return ['#markup' => $help];
  }

}
