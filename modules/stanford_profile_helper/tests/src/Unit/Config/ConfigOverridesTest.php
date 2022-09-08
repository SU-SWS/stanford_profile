<?php

namespace Drupal\Tests\stanford_profile_helper\Unit\Config;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\State\StateInterface;
use Drupal\file\FileInterface;
use Drupal\stanford_profile_helper\Config\ConfigOverrides;
use Drupal\Tests\UnitTestCase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\config_pages\ConfigPagesLoaderServiceInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\StreamWrapper\StreamWrapperManagerInterface;
use Drupal\Core\StreamWrapper\StreamWrapperInterface;

/**
 * Class ConfigOverridesTest
 *
 * @group stanford_profile_helper
 * @coversDefaultClass \Drupal\stanford_profile_helper\Config\ConfigOverrides
 */
class ConfigOverridesTest extends UnitTestCase {

  protected $logoFile;

  /**
   * Custom roles can be assigned by the site managers.
   */
  public function testConfigRolesOverrides() {
    $overridder = $this->getOverrideService();
    $overrides = $overridder->loadOverrides(['user.role.site_manager']);
    $expected['user.role.site_manager']['permissions'][500] = 'assign custm_foo_bar role';
    $expected['user.role.site_manager']['permissions'][501] = 'assign custm_bar_foo role';
    $this->assertEquals($expected, $overrides);
  }

  /**
   * Lockup custom overrides through config form.
   */
  public function testConfigLockupOverrides() {

    $overridder = $this->getOverrideService();
    $this->assertEmpty($overridder->loadOverrides(['barfoo.settings']));
    $this->assertEmpty($overridder->loadOverrides(['foobar.settings']));
    $this->configPageValues['lockup_settings'] = [
      'su_lockup_enabled' => 1,
      'su_lockup_options' => 'a',
      'su_line_1' => 'Line 1',
      'su_line_2' => 'Line 2',
      'su_line_4' => 'Line 4',
      'su_line_5' => 'Line 5',
      'su_use_theme_logo' => 1,
      'su_upload_logo_image' => NULL,
    ];

    $expected = [
      'foobar.settings' =>
        [
          'lockup' => [
            'option' => 'a',
            'line1' => 'Line 1',
            'line2' => 'Line 2',
            'line4' => 'Line 4',
            'line5' => 'Line 5',
          ],
          'logo' => [
            'use_default' => TRUE,
          ],
        ],
    ];
    $this->assertEquals($expected, $overridder->loadOverrides(['foobar.settings']));

    $this->configPageValues['lockup_settings']['su_upload_logo_image'] = 1;
    $this->assertEquals($expected, $overridder->loadOverrides(['foobar.settings']));

    $this->logoFile = $this->createMock(FileInterface::class);
    $this->logoFile->method('getFileuri')->wilLReturn('public://foobar.jpg');

    $expected['foobar.settings']['logo']['path'] = '/sites/default/files/logo.jpg';
    $this->assertEquals($expected, $overridder->loadOverrides(['foobar.settings']));
  }

  public function testMainMenuOverrides() {
    $configs = [
      'block.block.foobar_main_menu' => [
        'plugin' => 'system_menu_block:main',
        'region' => 'menu',
      ],
    ];
    $overridder = $this->getOverrideService($configs);

    $expected = [];
    $this->assertEquals($expected, $overridder->loadOverrides(['block.block.foobar_main_menu']));

    $this->configPageValues['stanford_basic_site_settings'] = ['su_site_menu_levels' => 2];
    $expected['block.block.foobar_main_menu'] = ['settings' => ['depth' => 2]];
    $this->assertEquals($expected, $overridder->loadOverrides(['block.block.foobar_main_menu']));
  }

  public function getConfigPageValue($config_name, $field, $delta = [], $key = NULL) {
    return $this->configPageValues[$config_name][$field] ?? NULL;
  }

  /**
   * [testCreateConfigObject description]
   *
   * @return [type] [description]
   */
  public function testDefaultFunctions() {
    $overridder = $this->getOverrideService();
    $this->assertNull($overridder->createConfigObject('name'));
    $this->assertEquals($overridder->getCacheSuffix(), 'StanfordProfileHelperConfigOverride');
    $this->assertInstanceOf(CacheableMetadata::class, $overridder->getCacheableMetadata('name'));
  }

  /**
   * Mock state service callback.
   */
  public function getStateCallback($state_name, $default_value = NULL) {
    switch ($state_name) {
      case 'stanford_intranet.rids':
        return array_flip([
          'custm_foo_bar',
          'site_manager',
          'contributor',
          'custm_bar_foo',
        ]);
    }
    return $default_value;
  }

  protected function getOverrideService(array $configs = []) {
    if (!isset($configs['system.theme']['default'])) {
      $configs['system.theme']['default'] = 'foobar';
    }
    $state = $this->createMock(StateInterface::class);
    $state->method('get')->will($this->returnCallback([
      $this,
      'getStateCallback',
    ]));

    $config_pages = $this->createMock(ConfigPagesLoaderServiceInterface::class);
    $config_pages->method('getValue')->will(
      $this->returnCallback([$this, 'getConfigPageValue'])
    );

    $file_storage = $this->createMock(EntityStorageInterface::class);
    $file_storage->method('load')->willReturnReference($this->logoFile);

    $entity_manager = $this->createMock(EntityTypeManagerInterface::class);
    $entity_manager->method('getStorage')->wilLReturn($file_storage);

    $stream_wrapper = $this->createMock(StreamWrapperInterface::class);
    $stream_wrapper->method('getExternalUrl')
      ->willReturn('/sites/default/files/logo.jpg');

    $stream_wrapper_manager = $this->createMock(StreamWrapperManagerInterface::class);
    $stream_wrapper_manager->method('getViaUri')->willReturn($stream_wrapper);

    $this->configFactory = $this->getConfigFactoryStub([
      'system.them' => ['stanford_basic' => 0],
    ]);
    return new ConfigOverrides($state, $config_pages, $this->getConfigFactoryStub($configs), $entity_manager, $stream_wrapper_manager);
  }

  public function getConfigFactoryStub(array $configs = []) {
    $config_get_map = [];
    $config_editable_map = [];
    // Construct the desired configuration object stubs, each with its own
    // desired return map.
    foreach ($configs as $config_name => $config_values) {
      // Define a closure over the $config_values, which will be used as a
      // returnCallback below. This function will mimic
      // \Drupal\Core\Config\Config::get and allow using dotted keys.
      $config_get = function ($key = '') use ($config_values) {
        // Allow to pass in no argument.
        if (empty($key)) {
          return $config_values;
        }
        // See if we have the key as is.
        if (isset($config_values[$key])) {
          return $config_values[$key];
        }
        $parts = explode('.', $key);
        $value = NestedArray::getValue($config_values, $parts, $key_exists);
        return $key_exists ? $value : NULL;
      };

      $immutable_config_object = $this->getMockBuilder('Drupal\Core\Config\ImmutableConfig')
        ->disableOriginalConstructor()
        ->getMock();
      $immutable_config_object->expects($this->any())
        ->method('get')
        ->willReturnCallback($config_get);
      $config_get_map[] = [$config_name, $immutable_config_object];

      $mutable_config_object = $this->getMockBuilder('Drupal\Core\Config\Config')
        ->disableOriginalConstructor()
        ->getMock();
      $mutable_config_object->expects($this->any())
        ->method('getOriginal')
        ->willReturnCallback($config_get);
      $config_editable_map[] = [$config_name, $mutable_config_object];
    }
    // Construct a config factory with the array of configuration object stubs
    // as its return map.
    $config_factory = $this->createMock('Drupal\Core\Config\ConfigFactoryInterface');
    $config_factory->expects($this->any())
      ->method('get')
      ->willReturnMap($config_get_map);
    $config_factory->expects($this->any())
      ->method('getEditable')
      ->willReturnMap($config_editable_map);
    return $config_factory;
  }

}
