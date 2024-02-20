<?php

namespace Drupal\stanford_profile\Plugin\HelpSection;

use Drupal\help\Plugin\HelpSection\HelpSectionPluginBase;

/**
 * Provides the module topics list section for the help page.
 *
 * @HelpSection(
 *   id = "profile_resources",
 *   title = @Translation("Drupal resources at Stanford"),
 *   description = @Translation("Stanford has a very active and engaged Drupal community, and many centrally offered and community created resources that might help you."),
 *   weight = -50
 * )
 */
class ProfileResourceSection extends HelpSectionPluginBase {

  use ProfileHelpTrait;

  /**
   * {@inheritdoc}
   */
  public function listTopics() {
    return [
      $this->getSupport(),
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
    $help = '<h3>' . self::getLinkString($this->t('University IT Technology Training'), 'https://itservices.stanford.edu/service/techtraining/schedule') . '</h3>';
    $help .= '<p>' . $this->t('See upcoming courses on site editing offered to Stanford faculty and staff.') . '</p>';
    return ['#markup' => $help];
  }

  /**
   * Get the user guide help text.
   *
   * @return array
   *   Markup render array.
   */
  protected function getSoda() {
    $help = '<h3>' . self::getLinkString($this->t('Stanford Office of Digital Accessibility'), 'https://uit.stanford.edu/accessibility') . '</h3>';
    $help .= '<p>' . $this->t('Get assistance with identifying issues and improving the accessibility of your site.') . '</p>';
    return ['#markup' => $help];
  }

  /**
   * Get the user guide help text.
   *
   * @return array
   *   Markup render array.
   */
  protected function getSupport() {
    $help = '<h3>' . self::getLinkString($this->t("Stanford Web Services Support"), 'https://sitesuserguide.stanford.edu/support') . '</h3>';
    $help .= '<p>' . $this->t("See upcoming onboarding sessions for editors or book office hours to get hands-on assistance with your site.") . '</p>';
    return ['#markup' => $help];
  }

}
