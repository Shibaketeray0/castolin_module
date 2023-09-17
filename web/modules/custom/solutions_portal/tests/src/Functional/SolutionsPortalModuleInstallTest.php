<?php

namespace Drupal\Tests\solutions_portal\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests the Solutions Portal module installation.
 *
 * @group solutions_portal
 */
final class SolutionsPortalModuleInstallTest extends BrowserTestBase {

  /**
   * The name of the module to install.
   *
   * @var string
   */
  const MODULE_NAME = 'solutions_portal';

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'claro';

  /**
   * The module handler used in this test.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();

    $this->moduleHandler = $this->container->get('module_handler');
  }

  /**
   * {@inheritdoc}
   */
  protected static $modules = [self::MODULE_NAME];

  /**
   * Test callback.
   */
  public function testModuleInstallation(): void {
    $this->assertTrue($this->moduleHandler->moduleExists(self::MODULE_NAME), 'The module was installed successfully.');
  }

}
