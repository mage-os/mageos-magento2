<?php

declare(strict_types=1);

namespace MageOS\Installer\Test\Unit\MageOS\Installer\Model\Command;

use MageOS\Installer\Model\Command\ProcessResult;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for ProcessResult
 */
class ProcessResultTest extends TestCase
{
    public function testItConstructsWithSuccess(): void
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

    public function testItConstructsWithFailure(): void
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

    public function testIsSuccessReturnsTrueForSuccessfulResult(): void
    {
        $result = new ProcessResult(true, 'output');

        $this->assertTrue($result->isSuccess());
        $this->assertFalse($result->isFailure());
    }

    public function testIsFailureReturnsTrueForFailedResult(): void
    {
        $result = new ProcessResult(false, '', 'error');

        $this->assertTrue($result->isFailure());
        $this->assertFalse($result->isSuccess());
    }

    public function testGetCombinedOutputWithOutputOnly(): void
    {
        $result = new ProcessResult(true, 'Command output', '');

        $this->assertEquals('Command output', $result->getCombinedOutput());
    }

    public function testGetCombinedOutputWithErrorOnly(): void
    {
        $result = new ProcessResult(false, '', 'Error message');

        $combined = $result->getCombinedOutput();

        $this->assertStringContainsString('Error message', $combined);
    }

    public function testGetCombinedOutputWithBoth(): void
    {
        $result = new ProcessResult(false, 'Output line', 'Error line');

        $combined = $result->getCombinedOutput();

        $this->assertStringContainsString('Output line', $combined);
        $this->assertStringContainsString('Error line', $combined);
        $this->assertStringContainsString(PHP_EOL, $combined);
    }

    public function testPropertiesAreReadonly(): void
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

    public function testErrorDefaultsToEmptyString(): void
    {
        $result = new ProcessResult(success: true, output: 'test');

        $this->assertEquals('', $result->error);
    }
}
