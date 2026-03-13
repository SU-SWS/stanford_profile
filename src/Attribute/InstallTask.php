<?php

declare(strict_types=1);

namespace Drupal\stanford_profile\Attribute;

use Drupal\Component\Plugin\Attribute\AttributeBase;

/**
 * The foo_bar attribute.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class InstallTask extends AttributeBase {

  /**
   * Constructs a new FooBar instance.
   *
   * @codeCoverageIgnore
   *
   * @param string $id
   *   The plugin ID.
   * @param string[] $dependencies
   *   List of dependent install task ids.
   */
  public function __construct(
    public readonly string $id,
    public readonly array $dependencies = [],
  ) {}

}
