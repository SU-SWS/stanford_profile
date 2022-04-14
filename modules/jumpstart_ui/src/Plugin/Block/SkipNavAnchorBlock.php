<?php

namespace Drupal\jumpstart_ui\Plugin\Block;

use Drupal\Component\Utility\Html;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a block with a hidden anchor.
 *
 * @Block(
 *   id = "jumpstart_ui_skipnav_main_anchor",
 *   admin_label = @Translation("Main content anchor target"),
 * )
 */
class SkipNavAnchorBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $id = Html::getUniqueId('main-content');
    return [
      'anchor' => [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#value' => $this->t('Main content start'),
        '#attributes' => [
          'id' => $id,
          'tabindex' => '-1',
          'class' => ['visually-hidden', 'focusable'],
        ],
      ],
    ];
  }

}
