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
      $this->getTechTraining(),
      $this->getBlog(),
      $this->getMorningOfCode(),
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
    $help .= '<p>' . $this->t('Check the upcoming courses schedule for training courses offered to Stanford faculty and staff.') . '</p>';
    return ['#markup' => $help];
  }

  /**
   * Get the user guide help text.
   *
   * @return array
   *   Markup render array.
   */
  protected function getBlog() {
    $help = '<h3>' . self::getLinkString($this->t('Stanford Web Services Blog'), 'https://swsblog.stanford.edu/') . '</h3>';
    $help .= '<p>' . $this->t('The Stanford Web Services team blogs about all things related to Stanford Sites, Drupal, design, site building, and many other topics. This is a great resource for SWS clients.') . '</p>';
    return ['#markup' => $help];
  }

  /**
   * Get the user guide help text.
   *
   * @return array
   *   Markup render array.
   */
  protected function getMorningOfCode() {
    $help = '<h3>' . self::getLinkString($this->t("Mornings o' Code, Drupallers Drop-in Help, Drupallers Co-Working Sessions"), 'https://opensource.stanford.edu/moc') . '</h3>';
    $help .= '<p>' . $this->t("Stanford Drupallers (new and experienced) meet regularly to collaborate and troubleshoot issues. Check the schedule for upcoming co-working sessions.") . '</p>';
    return ['#markup' => $help];
  }

}
