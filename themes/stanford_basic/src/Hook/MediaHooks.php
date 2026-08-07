<?php

declare(strict_types=1);

namespace Drupal\stanford_basic\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\media\IFrameMarkup;

/**
 *
 */
class MediaHooks {

  #[Hook('preprocess_media_oembed_iframe')]
  public function oembedAlter(&$variables) {
    /** @var \Drupal\media\IFrameMarkup $media */
    $media = (string) $variables['media'];
    $media = preg_replace('/ (style|height|width)=".*?"/', '', $media);
    $variables['media'] = IFrameMarkup::create($media);
  }

}
