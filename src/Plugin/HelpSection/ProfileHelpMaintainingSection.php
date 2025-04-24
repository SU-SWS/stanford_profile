<?php

namespace Drupal\cardinal_service_profile\Plugin\HelpSection;

use Drupal\help\Plugin\HelpSection\HelpSectionPluginBase;

/**
 * Provides the module topics list section for the help page.
 *
 * @HelpSection(
 *   id = "profile_help_maintaining",
 *   title = @Translation("Maintaining your site"),
 *   description = @Translation("The following tools and resources will help you keep your site healthy."),
 *   weight = -99
 * )
 */
class ProfileHelpMaintainingSection extends HelpSectionPluginBase {

  use ProfileHelpTrait;

  /**
   * {@inheritdoc}
   */
  public function listTopics() {
    return [
      $this->getStanfordWebServicesSupport(),
      $this->getSiteHealth(),
      $this->getContent(),
    ];
  }

  /**
   * Get the user guide help text.
   *
   * @return array
   *   Markup render array.
   */
  protected function getStanfordWebServicesSupport() {
    $help = '<h3>' . self::getLinkString($this->t('Stanford Web Services Support'), 'https://getsws.stanford.edu') . '</h3>';
    $help .= '<p>' . $this->t('Need expert advice or just an extra pair of hands? Stanford Web Services has you covered, with professional services to meet your needs.') . '</p>';
    $help .= self::getLinkString($this->t('Request Consultation'), 'https://getsws.stanford.edu', TRUE);
    return ['#markup' => $help];
  }

  /**
   * Get the user guide help text.
   *
   * @return array
   *   Markup render array.
   */
  protected function getSiteHealth() {
    $help = '<h3>' . $this->t('Site Health') . '</h3>';
    $help .= '<p>' . $this->t('Use <a href="http://siteimprove.stanford.edu/">Siteimprove</a>, an accessibility and quality assurance platform to scan and monitor your website that is active for all launched sites on Stanford Sites. Siteimprove will help you identify accessibility issues, broken links, typos and more! You can learn more about this service here: <a href="https://uit.stanford.edu/accessibility/testing/siteimprove">https://uit.stanford.edu/accessibility/testing/siteimprove</a>.') . '</p>';
    return ['#markup' => $help];
  }

  /**
   * Get the user guide help text.
   *
   * @return array
   *   Markup render array.
   */
  protected function getContent() {
    $help = '<h3>' . $this->t('Content') . '</h3>';
    $help .= '<p>' . $this->t('The <a href="https://communicators.stanford.edu/">Communicators</a> community is a great place to learn more about common practices, style guides, and policies.') . '</p>';
    $help .= '<p>' . $this->t('<a href="https://sallie.stanford.edu/">SALLIE</a> is the university’s image database. Find images to meet your communication goals.') . '</p>';
    return ['#markup' => $help];
  }

}
