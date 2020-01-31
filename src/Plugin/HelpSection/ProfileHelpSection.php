<?php

namespace Drupal\stanford_profile\Plugin\HelpSection;

use Drupal\help\Plugin\HelpSection\HelpSectionPluginBase;

/**
 * Provides the module topics list section for the help page.
 *
 * @HelpSection(
 *   id = "profile_help",
 *   title = @Translation("Getting Started"),
 *   description = @Translation("What do you need help with today?"),
 *   weight = -999
 * )
 */
class ProfileHelpSection extends HelpSectionPluginBase {

  use ProfileHelpTrait;

  /**
   * {@inheritdoc}
   */
  public function listTopics() {
    return [
      $this->getUserGuide(),
      $this->getAssistance(),
      $this->getLaunchWebsite(),
      $this->getFeedback(),
      $this->getPolicies(),
      $this->getContact(),
    ];
  }

  /**
   * Get the user guide help text.
   *
   * @return array
   *   Markup render array.
   */
  protected function getUserGuide() {
    $help = '<h3>' . $this->t('User Guide') . '</h3>';
    $help .= '<p>' . $this->t('Everything you need to know about how to use, maintain, and launch your D8 Website.') . '</p>';
    $help .= self::getLinkString($this->t('D8 User Guide'), 'https://userguide.sites.stanford.edu', TRUE);
    return ['#markup' => $help];
  }

  /**
   * Get the Launch website help text.
   *
   * @return array
   *   Markup render array.
   */
  protected function getLaunchWebsite() {
    $help = '<h3>' . $this->t('Launch your website') . '</h3>';
    // @TODO: Update link when launch process guide is available.
    $help .= '<p>' . $this->t('Learn about the launch process, make use of the launch checklist, and request a site launch Website Launch Process') . '</p>';
    $help .= self::getLinkString($this->t('Website launch process'), 'https://stanford.edu', TRUE);
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
    $help .= '<p>' . $this->t('Submit a ServiceNow request to Stanford Web Services to request personal assistance with your site.') . '</p>';
    $help .= self::getLinkString($this->t('Stanford Web Services ServiceNow Form'), 'https://stanford.service-now.com/it_services?id=sc_cat_item&sys_id=83daed294f4143009a9a97411310c70a', TRUE);
    return ['#markup' => $help];
  }

  /**
   * Get the feedback help text.
   *
   * @return array
   *   Markup render array.
   */
  protected function getFeedback() {
    $help = '<h3>' . $this->t('Do you have feedback for us?') . '</h3>';
    $help .= '<p>' . $this->t('Submit Feedback to our Drupal 8 service through our Google form.') . '</p>';
    $help .= self::getLinkString($this->t('Feedback form'), 'https://forms.gle/TTWDavR3CVMxrUX48', TRUE);
    return ['#markup' => $help];
  }

  /**
   * Get the contact info help text.
   *
   * @return array
   *   Markup render array.
   */
  protected function getContact() {
    $help = '<h3>' . $this->t("Can't find what you're looking for?") . '</h3>';
    $help .= '<p>' . $this->t("We'll help you find the answer or connect you with an advisor.") . '</p>';
    $help .= self::getLinkString($this->t('Stanford Web Services ServiceNow Form'), 'https://stanford.service-now.com/it_services?id=sc_cat_item&sys_id=83daed294f4143009a9a97411310c70a', TRUE);
    return ['#markup' => $help];
  }

}
