<?php

declare(strict_types=1);

namespace Drupal\Tests\stanford_basic\Unit\Hook;

use Drupal\stanford_basic\Hook\ImageHooks;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * Unit tests for ImageHooks.
 */
#[Group('stanford_basic')]
#[CoversClass(ImageHooks::class)]
class ImageHooksTest extends UnitTestCase {

  /**
   * The hook class under test.
   *
   * @var \Drupal\stanford_basic\Hook\ImageHooks
   */
  protected ImageHooks $hooks;

  /**
   * {@inheritDoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->hooks = new ImageHooks();
  }

  /**
   * Images without an alt attribute are marked as decorative.
   */
  public function testPreprocessImageAddsPresentationRoleWhenAltMissing(): void {
    $vars = ['attributes' => []];
    $this->hooks->preprocessImage($vars);

    $this->assertSame('presentation', $vars['attributes']['role']);
  }

  /**
   * Images with an alt attribute (even empty string) are left untouched.
   */
  public function testPreprocessImageLeavesRoleUntouchedWhenAltPresent(): void {
    $vars = ['attributes' => ['alt' => 'A description']];
    $this->hooks->preprocessImage($vars);

    $this->assertArrayNotHasKey('role', $vars['attributes']);
  }

  /**
   * An alt attribute set to an empty string still counts as "isset".
   */
  public function testPreprocessImageWithEmptyStringAlt(): void {
    $vars = ['attributes' => ['alt' => '']];
    $this->hooks->preprocessImage($vars);

    $this->assertArrayNotHasKey('role', $vars['attributes']);
  }

}
