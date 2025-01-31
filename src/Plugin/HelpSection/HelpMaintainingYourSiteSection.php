<?php

namespace Drupal\stanford_profile\Plugin\HelpSection;

use Drupal\help\Plugin\HelpSection\HelpSectionPluginBase;

/**
 * Provides the module topics list section for the help page.
 *
 * @HelpSection(
 *   id = "profile_help",
 *   title = @Translation("Maintaining your site"),
 *   description = @Translation("What do you need help with today?"),
 *   weight = -9
 * )
 */
class HelpMaintainingYourSiteSection extends HelpSectionPluginBase {
  /**
   * {@inheritdoc}
   */
  public function listTopics() {
    return [
      $this->getMaintainingYourSite(),
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
  protected function getMaintainingYourSite() {
    $help = '<h3>' . $this->t('Maintaining your site') . '</h3>';
    $help .= '<p>' . $this->t('The following tools and resources will help you keep your site healthy. You can also join our active community of Stanford Sites users on Slack. This is a great place to ask questions, learn about the newest features on Stanford Sites, and more!') . '</p>';
    return ['#markup' => $help];
  }

  /**
   * Get the user guide help text.
   *
   * @return array
   *   Markup render array.
   */
  protected function getStanfordWebServicesSupport() {
    $help = '<h3>' . $this->t('Stanford Web Services Support') . '</h3>';
    $help .= '<p>' . $this->t('The following tools and resources will help you keep your site healthy. You can also join our active community of Stanford Sites users on Slack. This is a great place to ask questions, learn about the newest features on Stanford Sites, and more!') . '</p>';
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
    $help .= '<p>' . $this->t('Use Siteimprove, an accessibility and quality assurance platform to scan and monitor your website that is active for all launched sites on Stanford Sites. Siteimprove will help you identify accessibility issues, broken links, typos and more! You can learn more about this service here: https://uit.stanford.edu/accessibility/testing/siteimprove.') . '</p>';
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
    $help .= '<p>' . $this->t('There are many resources available to help communicators.') . '</p>';
    $help .= '<p>' . $this->t('The <a href="https://communicators.stanford.edu/">Communicators</a> community is a great place to start to learn more about common practices, style guides, and policies.') . '</p>';
    $help .= '<p>' . $this->t('<a href="https://sallie.stanford.edu/">Sallie</a> is the university’s image database. Find images to meet your communication goals.') . '</p>';
    return ['#markup' => $help];
  }
}
