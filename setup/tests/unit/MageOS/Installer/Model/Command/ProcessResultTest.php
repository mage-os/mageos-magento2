<?php

declare(strict_types=1);

namespace MageOS\Installer\Test\Unit\MageOS\Installer\Model\Command;

use MageOS\Installer\Model\Command\ProcessResult;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for ProcessResult
 */
final class ProcessResultTest extends TestCase
{
    public function test_it_constructs_with_success(): void
    {
        $result = new ProcessResult(
            success: true,
            output: 'Command output',
            error: ''
        );

        $this->assertTrue($result->success);
        $this->assertEquals('Command output', $result->output);
        $this->assertEquals('', $result->error);
    }

    public function test_it_constructs_with_failure(): void
    {
        $result = new ProcessResult(
            success: false,
            output: '',
            error: 'Error message'
        );

        $this->assertFalse($result->success);
        $this->assertEquals('', $result->output);
        $this->assertEquals('Error message', $result->error);
    }

    public function test_is_success_returns_true_for_successful_result(): void
    {
        $result = new ProcessResult(true, 'output');

        $this->assertTrue($result->isSuccess());
        $this->assertFalse($result->isFailure());
    }

    public function test_is_failure_returns_true_for_failed_result(): void
    {
        $result = new ProcessResult(false, '', 'error');

        $this->assertTrue($result->isFailure());
        $this->assertFalse($result->isSuccess());
    }

    public function test_get_combined_output_with_output_only(): void
    {
        $result = new ProcessResult(true, 'Command output', '');

        $this->assertEquals('Command output', $result->getCombinedOutput());
    }

    public function test_get_combined_output_with_error_only(): void
    {
        $result = new ProcessResult(false, '', 'Error message');

        $combined = $result->getCombinedOutput();

        $this->assertStringContainsString('Error message', $combined);
    }

    public function test_get_combined_output_with_both(): void
    {
        $result = new ProcessResult(false, 'Output line', 'Error line');

        $combined = $result->getCombinedOutput();

        $this->assertStringContainsString('Output line', $combined);
        $this->assertStringContainsString('Error line', $combined);
        $this->assertStringContainsString(PHP_EOL, $combined);
    }

    public function test_properties_are_readonly(): void
    {
        $result = new ProcessResult(true, 'test');

        $reflection = new \ReflectionClass($result);
        $successProperty = $reflection->getProperty('success');
        $outputProperty = $reflection->getProperty('output');
        $errorProperty = $reflection->getProperty('error');

        $this->assertTrue($successProperty->isReadOnly());
        $this->assertTrue($outputProperty->isReadOnly());
        $this->assertTrue($errorProperty->isReadOnly());
    }

    public function test_error_defaults_to_empty_string(): void
    {
        $result = new ProcessResult(success: true, output: 'test');

        $this->assertEquals('', $result->error);
    }
}
