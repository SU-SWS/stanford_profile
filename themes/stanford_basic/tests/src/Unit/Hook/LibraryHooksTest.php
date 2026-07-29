<?php

declare(strict_types=1);

namespace Drupal\Tests\stanford_basic\Unit\Hook;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\config_pages\ConfigPagesLoaderServiceInterface;
use Drupal\file\FileInterface;
use Drupal\stanford_basic\Hook\LibraryHooks;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * Unit tests for LibraryHooks.
 */
#[Group('stanford_basic')]
#[CoversClass(LibraryHooks::class)]
class LibraryHooksTest extends UnitTestCase {

  /**
   * The hook class under test.
   *
   * @var \Drupal\stanford_basic\Hook\LibraryHooks
   */
  protected LibraryHooks $hooks;

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
    $this->hooks = new LibraryHooks($this->entityTypeManager);
  }

  /**
   * When the extension is not stanford_basic, nothing happens and the
   * config_pages.loader service is never consulted.
   */
  public function testLibraryInfoAlterWrongExtension(): void {
    $container = new ContainerBuilder();
    \Drupal::setContainer($container);

    $this->entityTypeManager->expects($this->never())->method('getStorage');

    $libraries = ['algolia-search' => []];
    $this->hooks->libraryInfoAlter($libraries, 'some_other_extension');

    $this->assertSame(['algolia-search' => []], $libraries);
  }

  /**
   * When the config_pages.loader service is unavailable, nothing happens.
   */
  public function testLibraryInfoAlterServiceUnavailable(): void {
    $container = new ContainerBuilder();
    \Drupal::setContainer($container);

    $this->entityTypeManager->expects($this->never())->method('getStorage');

    $libraries = ['algolia-search' => []];
    $this->hooks->libraryInfoAlter($libraries, 'stanford_basic');

    $this->assertSame(['algolia-search' => []], $libraries);
  }

  /**
   * When there is no file id configured, the method returns early.
   */
  public function testLibraryInfoAlterNoFileId(): void {
    $configPages = $this->createMock(ConfigPagesLoaderServiceInterface::class);
    $configPages->method('getValue')
      ->with('stanford_basic_site_settings', 'su_site_algolia_file', 0, 'target_id')
      ->willReturn(0);

    $container = new ContainerBuilder();
    $container->set('config_pages.loader', $configPages);
    \Drupal::setContainer($container);

    $this->entityTypeManager->expects($this->never())->method('getStorage');

    $libraries = ['algolia-search' => []];
    $this->hooks->libraryInfoAlter($libraries, 'stanford_basic');

    $this->assertSame(['algolia-search' => []], $libraries);
  }

  /**
   * When the configured file cannot be loaded, no library is modified.
   */
  public function testLibraryInfoAlterFileNotFound(): void {
    $configPages = $this->createMock(ConfigPagesLoaderServiceInterface::class);
    $configPages->method('getValue')
      ->with('stanford_basic_site_settings', 'su_site_algolia_file', 0, 'target_id')
      ->willReturn(42);

    $container = new ContainerBuilder();
    $container->set('config_pages.loader', $configPages);
    \Drupal::setContainer($container);

    $fileStorage = $this->createMock(EntityStorageInterface::class);
    $fileStorage->method('load')->with(42)->willReturn(NULL);
    $this->entityTypeManager->method('getStorage')->with('file')->willReturn($fileStorage);

    $libraries = ['algolia-search' => []];
    $this->hooks->libraryInfoAlter($libraries, 'stanford_basic');

    $this->assertSame(['algolia-search' => []], $libraries);
  }

  /**
   * Happy path: the configured file is found and the library js is set.
   */
  public function testLibraryInfoAlterHappyPath(): void {
    $configPages = $this->createMock(ConfigPagesLoaderServiceInterface::class);
    $configPages->method('getValue')
      ->with('stanford_basic_site_settings', 'su_site_algolia_file', 0, 'target_id')
      ->willReturn(42);

    $container = new ContainerBuilder();
    $container->set('config_pages.loader', $configPages);
    \Drupal::setContainer($container);

    $file = $this->createMock(FileInterface::class);
    $file->method('createFileUrl')->willReturn('/sites/default/files/algolia.js');

    $fileStorage = $this->createMock(EntityStorageInterface::class);
    $fileStorage->method('load')->with(42)->willReturn($file);
    $this->entityTypeManager->method('getStorage')->with('file')->willReturn($fileStorage);

    $libraries = ['algolia-search' => []];
    $this->hooks->libraryInfoAlter($libraries, 'stanford_basic');

    $this->assertSame(
      ['/sites/default/files/algolia.js' => ['minified' => TRUE]],
      $libraries['algolia-search']['js']
    );
  }

}
