<?php

namespace Drupal\Tests\stanford_notifications\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\user\Entity\Role;
use Drupal\user\Entity\User;
use Drupal\user\RoleInterface;

/**
 * Base test class for kernel tests.
 */
abstract class StanfordNotificationTestBase extends KernelTestBase {

  /**
   * {@inheritDoc}
   */
  protected static $modules = [
    'system',
    'stanford_notifications',
    'toolbar',
    'user',
  ];

  /**
   * {@inheritDoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('notification');
    $this->installEntitySchema('user');
    $this->installEntitySchema('user_role');
    $this->installSchema('system', ['sequences']);

    Role::create(['id' => RoleInterface::ANONYMOUS_ID, 'label' => 'anonymous'])->save();
    Role::create(['id' => 'foo', 'label' => 'foo'])->save();
    Role::create(['id' => 'bar', 'label' => 'bar'])->save();
  }

  /**
   * Create a testable user with a give role.
   */
  protected function createUser($role = NULL) {
    $user = User::create([
      'name' => $this->randomString(),
      'mail' => $this->randomMachineName() . '@localhost',
      'status' => 1,
      'roles' => [$role],
    ]);
    $user->save();
    return $user;
  }

}
