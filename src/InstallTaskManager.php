<?php

namespace Drupal\stanford_profile;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Install task plugin manager.
 *
 * @package Drupal\stanford_profile
 * @codeCoverageIgnore
 *   We can't test a service in profile due to some limitations of the Kernel.
 */
class InstallTaskManager extends DefaultPluginManager {

  /**
   * Array of completed plugin ids.
   *
   * @var array
   */
  protected $completedTasks = [];

  /**
   * Constructs a ArchiverManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct(
      'Plugin/InstallTask',
      $namespaces,
      $module_handler,
      'Drupal\stanford_profile\InstallTaskInterface',
      'Drupal\stanford_profile\Annotation\InstallTask'
    );
    $this->alterInfo('install_task_plugins');
    $this->setCacheBackend($cache_backend, 'install_task_plugins');
  }

  /**
   * Run all install task plugins.
   *
   * @param array $install_state
   *   Current install state.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  public function runTasks(array &$install_state) {
    foreach ($this->getDefinitions() as $definition) {
      $this->runTask($definition, $install_state);
    }
  }

  /**
   * Run the given task after any dependencies.
   *
   * @param array $task_definition
   *   Plugin definition.
   * @param array $install_state
   *   Current install state.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  protected function runTask(array $task_definition, array &$install_state) {
    foreach ($task_definition['dependencies'] as $dependency) {
      $dependency_definition = $this->getDefinition($dependency);
      $this->runTask($dependency_definition, $install_state);
    }

    if (!in_array($task_definition['id'], $this->completedTasks)) {
      /** @var \Drupal\stanford_profile\InstallTaskInterface $plugin */
      $plugin = $this->createInstance($task_definition['id']);
      $plugin->runTask($install_state);
      $this->completedTasks[] = $task_definition['id'];
    }
  }

}
