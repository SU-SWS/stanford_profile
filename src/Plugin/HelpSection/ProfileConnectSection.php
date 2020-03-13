<?php

namespace Drupal\cardinal_service_profile\Plugin\HelpSection;

use Drupal\help\Plugin\HelpSection\HelpSectionPluginBase;

/**
 * Provides the module topics list section for the help page.
 *
 * @HelpSection(
 *   id = "profile_connect",
 *   title = @Translation("Drupal resources at Stanford"),
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
    $help = '<p>' . $this->t('The main way the Stanford Drupal community communicates is through the Drupallers Mailing List. You can join this list to participate in the community discussion. Feel free to post questions to the list, or post responses to help others.') . '</p>';
    $help .= self::getLinkString($this->t('Join the Drupallers Mailing List'), 'https://mailman.stanford.edu/mailman/listinfo/drupallers');
    return [['#markup' => $help]];
  }

}
