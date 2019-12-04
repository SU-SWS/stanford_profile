<?php

namespace Drupal\stanford_profile;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Install task plugin manager.
 *
 * @package Drupal\stanford_profile
 * @codeCoverageIgnore
 *   We can't test a service in profile due to some limitations of the Kernel.
 */
class InstallTaskManager extends DefaultPluginManager {

  use StringTranslationTrait;

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
   * @param array $requesting_plugins
   *   The path of plugins that depend on the current task.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  protected function runTask(array $task_definition, array &$install_state, array $requesting_plugins = []) {
    foreach ($task_definition['dependencies'] as $dependency) {
      $dependency_definition = $this->getDefinition($dependency);

      // If task1 depends on task2 which depends on task1, it will create
      // circular dependencies. Blow up the install if this happens because of
      // bad development work.
      if (in_array($task_definition['id'], $requesting_plugins)) {
        $requesting_plugins[] = $task_definition['id'];
        throw new \Exception($this->t('Circular dependencies detected. %path', ['%path' => implode(' -> ', $requesting_plugins)]));
      }

      $requesting_plugins[] = $task_definition['id'];
      $this->runTask($dependency_definition, $install_state, $requesting_plugins);
    }

    if (!in_array($task_definition['id'], $this->completedTasks)) {
      /** @var \Drupal\stanford_profile\InstallTaskInterface $plugin */
      $plugin = $this->createInstance($task_definition['id']);
      $plugin->runTask($install_state);
      $this->completedTasks[] = $task_definition['id'];
    }
  }

}
