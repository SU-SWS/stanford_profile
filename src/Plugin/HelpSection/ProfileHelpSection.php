<?php

namespace Drupal\stanford_profile\Plugin\HelpSection;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\help\Attribute\HelpSection;
use Drupal\help\Plugin\HelpSection\HelpSectionPluginBase;

/**
 * Provides the module topics list section for the help page.
 */
#[HelpSection(
  id: 'profile_help',
  title: new TranslatableMarkup('Getting Started'),
  description: new TranslatableMarkup('What do you need help with today?'),
  weight: -999
)]
class ProfileHelpSection extends HelpSectionPluginBase {

  use ProfileHelpTrait;

  /**
   * {@inheritdoc}
   */
  public function listTopics() {
    return [
      $this->getUserGuide(),
      $this->getAssistance(),
      $this->getSupport(),
      $this->getPolicies(),
      $this->getLaunchWebsite(),
    ];
  }

  /**
   * Get the user guide help text.
   *
   * @return array
   *   Markup render array.
   */
  protected function getUserGuide() {
    $help = '<h3>' . self::getLinkString($this->t('Stanford Sites User Guide'), 'https://sitesuserguide.stanford.edu') . '</h3>';
    $help .= '<p>' . $this->t('Everything you need to know about how to use, maintain, and launch your Website.') . '</p>';
    return ['#markup' => $help];
  }

  /**
   * Get the Launch website help text.
   *
   * @return array
   *   Markup render array.
   */
  protected function getLaunchWebsite() {
    $help = '<h3>' . $this->t('Ready to Launch?') . '</h3>';
    // @TODO: Update link when launch process guide is available.
    $help .= '<p>' . $this->t('Learn about the launch process, review the final checklist, and submit a request to launch.') . '</p>';
    $help .= self::getLinkString($this->t('Website launch process'), 'https://sitesuserguide.stanford.edu/support/site-launch-checklist', TRUE);

    return ['#markup' => $help];
  }

  /**
   * Get the policies help text.
   *
   * @return array
   *   Markup render array.
   */
  protected function getPolicies() {
    $help = '<h3>' . $this->t('University Policies') . '</h3>';
    $help .= '<p>' . $this->t('All site content must comply with the University Policies.') . '</p>';

    $help .= self::getLinkString($this->t('University Trademark and Images'), 'https://adminguide.stanford.edu/chapter-1/subchapter-5/policy-1-5-4') . '</br>';
    $help .= self::getLinkString($this->t('Copyright'), 'https://uit.stanford.edu/security/copyright-infringement') . '</br>';
    $help .= self::getLinkString($this->t('Online Privacy'), 'https://www.stanford.edu/site/privacy/') . '</br>';
    $help .= self::getLinkString($this->t('Accessibility'), 'https://www.stanford.edu/site/accessibility/') . '</br>';
    $help .= self::getLinkString($this->t('Terms of use for Sites'), 'https://www.stanford.edu/site/terms/') . '</br>';
    $help .= self::getLinkString($this->t('Branding'), 'https://identity.stanford.edu/') . '</br>';

    return ['#markup' => $help];
  }

  /**
   * Get the assistance help text.
   *
   * @return array
   *   Markup render array.
   */
  protected function getAssistance() {
    $help = '<h3>' . $this->t('Need Assistance?') . '</h3>';
    $help .= '<p>' . $this->t('Submit a ServiceNow request to Stanford Web Services to request assistance with your website.') . '</p>';
    $help .= self::getLinkString($this->t('Stanford Web Services ServiceNow Form'), 'https://stanford.service-now.com/it_services?id=sc_cat_item&sys_id=83daed294f4143009a9a97411310c70a', TRUE);
    return ['#markup' => $help];
  }

  /**
   * Get the user guide help text.
   *
   * @return array
   *   Markup render array.
   */
  protected function getSupport() {
    $help = '<h3>' . self::getLinkString($this->t("Training & Support for Site Editors"), 'https://sitesuserguide.stanford.edu/support') . '</h3>';
    $help .= '<p>' . $this->t("See upcoming onboarding sessions for editors or book office hours to get hands-on assistance with your site.") . '</p>';
    $help .= '<p>' . $this->t("You can also join our active community of Stanford Sites users on Slack. This is a great place to ask questions, learn about the newest features on Stanford Sites, and more!") . '</p>';
    $help .= self::getLinkString('Slack', 'https://stanford.enterprise.slack.com/archives/C01NR8WC6AX', TRUE);
    return ['#markup' => $help];
  }

}
