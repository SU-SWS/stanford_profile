<?php

namespace Drupal\Tests\jumpstart_ui\Kernel\Layout;

use Drupal\KernelTests\KernelTestBase;

/**
 * Class OneColLayoutOverlayTest.
 *
 * @group jumpstart_ui
 */
class OneColOverlayLayoutTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'system',
    'jumpstart_ui',
    'components',
  ];

  /**
   * Layout should render when values are passed..
   */
  public function testOverlayLayout() {
    $template = \Drupal::service('twig')->load('@jumpstart_ui/layouts/one-column-overlay.html.twig');
    $rendering = $template->render([
      'content' => [
        'main' => '<img src="foo-bar.jpg"/>',
        'overlay' => '<div>Overlay Text</div>',
      ],
    ]);

    $this->assertStringContainsString('foo-bar.jpg', $rendering);
    $this->assertStringContainsString('Overlay Text', $rendering);
    $this->assertNotEmpty(preg_grep('/class="su-hero/', explode("\n", $rendering)));
    $this->assertNotEmpty(preg_grep('/class="su-hero__media/', explode("\n", $rendering)));
    $this->assertNotEmpty(preg_grep('/class="su-card/', explode("\n", $rendering)));
    $this->assertNotEmpty(preg_grep('/class="su-card__contents/', explode("\n", $rendering)));
    preg_match_all('/<\//', $rendering, $closing_tags);
    $this->assertCount(4, $closing_tags[0]);
  }

}
