<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\NewRelicReporting\Console\Command;

use Magento\Framework\App\Config\MutableScopeConfigInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\NewRelicReporting\Console\Command\DeployMarker;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Integration test for DeployMarker console command.
 * Covers framework integration (DI, Config, CLI) for both v2_rest and NerdGraph modes.
 *
 * @magentoAppIsolation enabled
 */
class DeployMarkerCommandTest extends TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var DeployMarker
     */
    private $command;

    /**
     * @var CommandTester
     */
    private $commandTester;

    /**
     * @var MutableScopeConfigInterface
     */
    private $mutableScopeConfig;

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->mutableScopeConfig = $this->objectManager->get(MutableScopeConfigInterface::class);
        $this->command = $this->objectManager->get(DeployMarker::class);
        $this->commandTester = new CommandTester($this->command);
    }

    protected function tearDown(): void
    {
        if ($this->mutableScopeConfig) {
            $this->mutableScopeConfig->clean();
        }
        parent::tearDown();
    }

    /**
     * Test command with missing required argument
     */
    public function testCommandWithMissingArgument()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "message")');

        $this->commandTester->execute([]);
    }

    /**
     * Test command with valid arguments but disabled NewRelic
     */
    public function testCommandWithDisabledNewRelic()
    {
        $this->mutableScopeConfig->setValue('newrelicreporting/general/enable', '0');

        $exitCode = $this->commandTester->execute([
            'message' => 'Test deployment'
        ]);

        $this->assertEquals(1, $exitCode);
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('✗ New Relic is not enabled. Please check your configuration.', $output);
    }

    /**
     * Test command with minimal arguments (v2 REST mode)
     */
    public function testCommandWithMinimalArgumentsV2Rest()
    {
        // Test: Magento config system integration
        $this->mutableScopeConfig->setValue('newrelicreporting/general/enable', '1');
        $this->mutableScopeConfig->setValue('newrelicreporting/general/api_mode', 'v2_rest');
        $this->mutableScopeConfig->setValue('newrelicreporting/general/app_id', '12345');
        $this->mutableScopeConfig->setValue('newrelicreporting/general/api', 'fake_api_key_for_testing');

        $exitCode = $this->commandTester->execute([
            'message' => 'Test deployment message'
        ]);

        $this->assertTrue(in_array($exitCode, [0, 1], true), 'Command should exit with 0 (success) or 1 (graceful failure)');
        $output = $this->commandTester->getDisplay();

        // No fatal framework errors
        $this->assertStringNotContainsString('Fatal error', $output);
        $this->assertStringNotContainsString('Call to a member function', $output);

        $this->assertMatchesRegularExpression('/(✓|✗)/', $output, 'Output should contain success (✓) or error (✗) indicator');
    }

    /**
     * Test command configuration, help, and CLI framework integration
     */
    public function testCommandConfiguration()
    {
        // Test command identity
        $this->assertEquals('newrelic:create:deploy-marker', $this->command->getName());

        // Test description and help
        $description = $this->command->getDescription();
        $this->assertIsString($description);
        $this->assertStringContainsString('deployment marker', $description);

        $help = $this->command->getHelp();
        $this->assertIsString($help);

        // Test Symfony CLI framework integration (arguments, options registration)
        $definition = $this->command->getDefinition();

        // Verify required argument
        $this->assertTrue($definition->hasArgument('message'));
        $messageArg = $definition->getArgument('message');
        $this->assertTrue($messageArg->isRequired());

        // Verify optional arguments
        $this->assertTrue($definition->hasArgument('change_log'));
        $this->assertTrue($definition->hasArgument('user'));
        $this->assertTrue($definition->hasArgument('revision'));

        // Verify options
        $this->assertTrue($definition->hasOption('commit'));
        $this->assertTrue($definition->hasOption('deep-link'));
        $this->assertTrue($definition->hasOption('group-id'));
    }

    /**
     * Test command with all arguments and options (NerdGraph mode)
     */
    public function testCommandWithAllParametersNerdGraph()
    {
        $this->mutableScopeConfig->setValue('newrelicreporting/general/enable', '1');
        $this->mutableScopeConfig->setValue('newrelicreporting/general/api_mode', 'nerdgraph');
        $this->mutableScopeConfig->setValue('newrelicreporting/general/entity_guid', 'fake-guid-for-testing');
        $this->mutableScopeConfig->setValue('newrelicreporting/general/api', 'fake_api_key_for_testing');

        $exitCode = $this->commandTester->execute([
            'message' => 'Full deployment test',
            'change_log' => 'Added new features',
            'user' => 'deploy-user',
            'revision' => 'v2.0.0',
            '--commit' => 'abc123',
            '--deep-link' => 'https://github.com/test/releases/v2.0.0',
            '--group-id' => 'production'
        ]);

        // Framework integration: Should handle gracefully (success or graceful failure)
        $this->assertTrue(in_array($exitCode, [0, 1], true), 'Command should exit with 0 (success) or 1 (graceful failure)');
        $output = $this->commandTester->getDisplay();

        $this->assertStringNotContainsString('Fatal error', $output);
        $this->assertStringNotContainsString('Call to a member function', $output);
        $this->assertMatchesRegularExpression('/(✓|✗)/', $output, 'Output should contain success (✓) or error (✗) indicator');
    }

    /**
     * Test command with empty message
     */
    public function testCommandWithEmptyMessage()
    {
        $this->mutableScopeConfig->setValue('newrelicreporting/general/enable', '1');

        $exitCode = $this->commandTester->execute([
            'message' => ''
        ]);

        $this->assertIsInt($exitCode);
        $output = $this->commandTester->getDisplay();
        $this->assertStringNotContainsString('Fatal error', $output);

        $this->assertMatchesRegularExpression('/(✓|✗)/', $output, 'Output should contain success (✓) or error (✗) indicator');
    }

    /**
     * Test --help option execution (end-to-end CLI wiring)
     */
    public function testCommandHelpOption()
    {
        // Create a standalone application to test help functionality
        $application = new Application();
        $application->setAutoExit(false);
        $application->add($this->command);

        $input = new ArrayInput(['command' => 'newrelic:create:deploy-marker', '--help' => true]);
        $output = new BufferedOutput();

        $exitCode = $application->run($input, $output);

        $this->assertEquals(0, $exitCode);
        $displayOutput = $output->fetch();
        $this->assertStringContainsString('newrelic:create:deploy-marker', $displayOutput);
        $this->assertStringContainsString('deployment marker', $displayOutput);
        $this->assertStringContainsString('Usage:', $displayOutput);
    }
}
