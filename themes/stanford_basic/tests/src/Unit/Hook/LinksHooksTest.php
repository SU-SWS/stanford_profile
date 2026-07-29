<?php

declare(strict_types=1);

namespace Drupal\Tests\stanford_basic\Unit\Hook;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;
use Drupal\stanford_basic\Hook\LinksHooks;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * Unit tests for LinksHooks.
 */
#[Group('stanford_basic')]
#[CoversClass(LinksHooks::class)]
class LinksHooksTest extends UnitTestCase {

  /**
   * The hook class under test.
   *
   * @var \Drupal\stanford_basic\Hook\LinksHooks
   */
  protected LinksHooks $hooks;

  /**
   * Mocked entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * {@inheritDoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
    $this->hooks = new LinksHooks($this->entityTypeManager);
    $this->hooks->setStringTranslation($this->getStringTranslationStub());
  }

  /**
   * When there is no print link at all, nothing happens and no services
   * are queried.
   */
  public function testPreprocessLinksEntityPrintableSkipsWhenNoPrintLink(): void {
    $this->entityTypeManager->expects($this->never())->method('getStorage');

    $variables = ['links' => []];
    $this->hooks->preprocessLinksEntityPrintable($variables);

    $this->assertSame(['links' => []], $variables);
  }

  /**
   * The print link gets rel="nofollow" and, for a stanford_media node, the
   * title is changed to "Read Transcript".
   */
  public function testPreprocessLinksEntityPrintableChangesTitleForStanfordMedia(): void {
    $url = $this->createMock(Url::class);
    $url->method('getRouteParameters')->willReturn(['entity' => 42]);

    $node = $this->createMock(NodeInterface::class);
    $node->method('bundle')->willReturn('stanford_media');

    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->method('load')->with(42)->willReturn($node);
    $this->entityTypeManager->method('getStorage')->with('node')->willReturn($storage);

    $variables = [
      'links' => [
        'print' => [
          'link' => [
            '#url' => $url,
            '#title' => 'Print',
          ],
        ],
      ],
    ];
    $this->hooks->preprocessLinksEntityPrintable($variables);

    $this->assertSame('nofollow', $variables['links']['print']['link']['#attributes']['rel']);
    $this->assertSame('Read Transcript', (string) $variables['links']['print']['link']['#title']);
  }

  /**
   * Nodes of other bundles keep their original title, but the print link
   * still gets rel="nofollow".
   */
  public function testPreprocessLinksEntityPrintableLeavesTitleForOtherBundles(): void {
    $url = $this->createMock(Url::class);
    $url->method('getRouteParameters')->willReturn(['entity' => 7]);

    $node = $this->createMock(NodeInterface::class);
    $node->method('bundle')->willReturn('stanford_page');

    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->method('load')->with(7)->willReturn($node);
    $this->entityTypeManager->method('getStorage')->with('node')->willReturn($storage);

    $variables = [
      'links' => [
        'print' => [
          'link' => [
            '#url' => $url,
            '#title' => 'Print',
          ],
        ],
      ],
    ];
    $this->hooks->preprocessLinksEntityPrintable($variables);

    $this->assertSame('nofollow', $variables['links']['print']['link']['#attributes']['rel']);
    $this->assertSame('Print', $variables['links']['print']['link']['#title']);
  }

  /**
   * When the referenced entity no longer exists, the null-safe operator
   * prevents a bundle() call and the title is left untouched.
   */
  public function testPreprocessLinksEntityPrintableHandlesMissingNode(): void {
    $url = $this->createMock(Url::class);
    $url->method('getRouteParameters')->willReturn(['entity' => 999]);

    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->method('load')->with(999)->willReturn(NULL);
    $this->entityTypeManager->method('getStorage')->with('node')->willReturn($storage);

    $variables = [
      'links' => [
        'print' => [
          'link' => [
            '#url' => $url,
            '#title' => 'Print',
          ],
        ],
      ],
    ];
    $this->hooks->preprocessLinksEntityPrintable($variables);

    $this->assertSame('nofollow', $variables['links']['print']['link']['#attributes']['rel']);
    $this->assertSame('Print', $variables['links']['print']['link']['#title']);
  }

}
