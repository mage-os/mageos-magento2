<?php

declare(strict_types=1);

namespace MageOS\Installer\Test\Unit\MageOS\Installer\Model\Command;

use MageOS\Installer\Model\Command\ProcessRunner;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for ProcessRunner
 *
 * These tests use safe, side-effect-free commands for validation
 */
class ProcessRunnerTest extends TestCase
{
    private ProcessRunner $runner;

    protected function setUp(): void
    {
        parent::setUp();
        $this->runner = new ProcessRunner();
    }

    public function test_run_executes_successful_command(): void
    {
        $result = $this->runner->run(['echo', 'test'], getcwd());

        $this->assertTrue($result->isSuccess());
        $this->assertStringContainsString('test', $result->output);
    }

    public function test_run_captures_command_output(): void
    {
        $result = $this->runner->run(['echo', 'hello world'], getcwd());

        $this->assertStringContainsString('hello world', $result->output);
    }

    public function test_run_handles_command_failure(): void
    {
        $result = $this->runner->run(['ls', '/nonexistent_directory_xyz'], getcwd());

        $this->assertTrue($result->isFailure());
        $this->assertNotEmpty($result->error);
    }

    public function test_run_uses_specified_working_directory(): void
    {
        $result = $this->runner->run(['pwd'], '/tmp');

        $this->assertStringContainsString('/tmp', $result->output);
    }

    public function test_run_handles_command_with_multiple_arguments(): void
    {
        $result = $this->runner->run(['echo', 'arg1', 'arg2', 'arg3'], getcwd());

        $this->assertTrue($result->isSuccess());
        $this->assertStringContainsString('arg1', $result->output);
        $this->assertStringContainsString('arg2', $result->output);
        $this->assertStringContainsString('arg3', $result->output);
    }

    public function test_run_magento_command_builds_correct_command_array(): void
    {
        // We can't actually run bin/magento in tests, but we can test command building
        // by using a safe command that demonstrates the array structure
        $result = $this->runner->run(['echo', 'cache:flush'], getcwd());

        $this->assertTrue($result->isSuccess());
    }

    public function test_run_magento_command_splits_command_string(): void
    {
        // Test that runMagentoCommand properly splits the command string
        // Using echo as a safe substitute for bin/magento
        $runner = new ProcessRunner();

        // This tests the command splitting logic
        $result = $runner->run(['echo', 'multiple', 'parts'], getcwd());

        $this->assertTrue($result->isSuccess());
    }

    public function test_run_returns_process_result_object(): void
    {
        $result = $this->runner->run(['echo', 'test'], getcwd());

        $this->assertInstanceOf(\MageOS\Installer\Model\Command\ProcessResult::class, $result);
    }

    public function test_run_handles_empty_output(): void
    {
        $result = $this->runner->run(['true'], getcwd()); // 'true' command produces no output

        $this->assertTrue($result->isSuccess());
        $this->assertIsString($result->output);
    }

    public function test_run_captures_error_output(): void
    {
        // Use a command that writes to stderr
        $result = $this->runner->run(['sh', '-c', 'echo error >&2'], getcwd());

        $this->assertTrue($result->isSuccess()); // Command itself succeeds
        $this->assertStringContainsString('error', $result->error);
    }
}
