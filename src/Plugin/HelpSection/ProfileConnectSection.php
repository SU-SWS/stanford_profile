<?php

namespace Drupal\stanford_profile\Plugin\HelpSection;

use Drupal\help\Plugin\HelpSection\HelpSectionPluginBase;

/**
 * Provides the module topics list section for the help page.
 *
 * @HelpSection(
 *   id = "profile_connect",
 *   title = @Translation("Other resources at Stanford"),
 *   description =  @Translation(""),
 *   weight = -20
 * )
 */
class ProfileConnectSection extends HelpSectionPluginBase {

  use ProfileHelpTrait;

  /**
   * {@inheritdoc}
   */
  public function listTopics() {
    $help = '<p>' . $this->t('The main way the Stanford Drupal community communicates is through Slack. To participate in the community discussion, join the #drupal Slack channel in the Stanford Community of Practice workspace.') . '</p>';
    $help .= self::getLinkString($this->t('Join the Drupallers community of practice'), 'https://stanford.enterprise.slack.com/archives/C0ETL2M47');
    return [['#markup' => $help]];
  }

}
