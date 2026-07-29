<?php

declare(strict_types=1);

namespace Drupal\stanford_basic\Hook;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\StringTranslationTrait;

class LinksHooks {

  use StringTranslationTrait;

  public function __construct(protected EntityTypeManagerInterface $entityTypeManager) {}

  /**
   * Implements hook_preprocess_HOOK().
   */
  #[Hook('preprocess_links__entity__printable')]
  public function preprocessLinksEntityPrintable(&$variables): void {
    if (isset($variables['links']['print']['link'])) {
      $variables['links']['print']['link']['#attributes']['rel'] = 'nofollow';

      $node_id = $variables['links']['print']['link']['#url']->getRouteParameters()['entity'];
      $node_bundle = $this->entityTypeManager
        ->getStorage('node')
        ->load($node_id)
        ?->bundle();

      if ($node_bundle == 'stanford_media') {
        $variables['links']['print']['link']['#title'] = $this->t('Read Transcript');
      }
    }
  }

}
