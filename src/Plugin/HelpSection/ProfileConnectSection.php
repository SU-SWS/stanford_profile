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
    $help = '<p>' . $this->t('The main way the Stanford Drupal community communicates is through the mailing list. You can join this list to participate in the community discussion.') . '</p>';
    $help .= self::getLinkString($this->t('Join the Drupallers community of practice'), 'https://mailman.stanford.edu/mailman/listinfo/drupallers');
    return [['#markup' => $help]];
  }

}
