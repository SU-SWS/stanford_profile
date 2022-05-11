<?php

namespace Drupal\Tests\stanford_intranet\Kernel;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\file\Entity\File;
use Drupal\file\FileInterface;
use Drupal\image\Entity\ImageStyle;

/**
 * Test intranet manager service.
 *
 * @coversDefaultClass \Drupal\stanford_intranet\StanfordIntranetManager
 * @group stanford_intranet
 */
class StanfordIntranetManagerTest extends IntranetKernelTestBase {

  protected function setUp(): void {
    parent::setUp();
    $this->setSetting('file_private_path', $this->container->getParameter('site.path') . '/private');
    mkdir($this->container->getParameter('site.path') . '/private', 0777, TRUE);

    ImageStyle::create(['name' => 'foo', 'label' => 'foo'])->save();
  }

  /**
   * {@inheritdoc}
   */
  public function register(ContainerBuilder $container) {
    parent::register($container);
    $container->register('stream_wrapper.private', 'Drupal\Core\StreamWrapper\PrivateStream')
      ->addTag('stream_wrapper', ['scheme' => 'private']);
  }

  /**
   * Service moves the public files.
   */
  public function testFileMoves() {
    $path = 'public://testfile.txt';
    file_put_contents($path, 'Foo Bar');

    $file = File::create([
      'uri' => $path,
      'filename' => 'testfile.txt',
      'status' => FileInterface::STATUS_PERMANENT,
    ]);
    $file->save();

    \Drupal::service('stanford_intranet.manager')->moveIntranetFiles();
    $moved_file = File::load($file->id());
    $this->assertEquals($path, $moved_file->getFileUri());

    \Drupal::state()->set('stanford_intranet', 1);
    \Drupal::service('stanford_intranet.manager')->moveIntranetFiles();
    $moved_file = File::load($file->id());
    $this->assertEquals('private://testfile.txt', $moved_file->getFileUri());
  }

}
