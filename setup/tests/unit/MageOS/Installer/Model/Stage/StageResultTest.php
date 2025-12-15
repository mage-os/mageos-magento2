<?php

declare(strict_types=1);

namespace MageOS\Installer\Test\Unit\MageOS\Installer\Model\Stage;

use MageOS\Installer\Model\Stage\StageResult;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for StageResult
 */
class StageResultTest extends TestCase
{
    public function test_continue_factory_creates_continue_result(): void
    {
        $result = StageResult::continue();

        $this->assertEquals(StageResult::CONTINUE, $result->status);
        $this->assertTrue($result->shouldContinue());
        $this->assertFalse($result->shouldGoBack());
        $this->assertFalse($result->shouldRetry());
        $this->assertFalse($result->shouldAbort());
    }

    public function test_back_factory_creates_back_result(): void
    {
        $result = StageResult::back();

        $this->assertEquals(StageResult::GO_BACK, $result->status);
        $this->assertFalse($result->shouldContinue());
        $this->assertTrue($result->shouldGoBack());
        $this->assertFalse($result->shouldRetry());
        $this->assertFalse($result->shouldAbort());
    }

    public function test_retry_factory_creates_retry_result(): void
    {
        $result = StageResult::retry();

        $this->assertEquals(StageResult::RETRY, $result->status);
        $this->assertFalse($result->shouldContinue());
        $this->assertFalse($result->shouldGoBack());
        $this->assertTrue($result->shouldRetry());
        $this->assertFalse($result->shouldAbort());
    }

    public function test_abort_factory_creates_abort_result(): void
    {
        $result = StageResult::abort();

        $this->assertEquals(StageResult::ABORT, $result->status);
        $this->assertFalse($result->shouldContinue());
        $this->assertFalse($result->shouldGoBack());
        $this->assertFalse($result->shouldRetry());
        $this->assertTrue($result->shouldAbort());
    }

    public function test_factories_accept_optional_message(): void
    {
        $continueResult = StageResult::continue('Moving forward');
        $backResult = StageResult::back('Going back');
        $retryResult = StageResult::retry('Try again');
        $abortResult = StageResult::abort('Installation cancelled');

        $this->assertEquals('Moving forward', $continueResult->message);
        $this->assertEquals('Going back', $backResult->message);
        $this->assertEquals('Try again', $retryResult->message);
        $this->assertEquals('Installation cancelled', $abortResult->message);
    }

    public function test_factories_create_null_message_by_default(): void
    {
        $result = StageResult::continue();

        $this->assertNull($result->message);
    }

    public function test_constructor_validates_status(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid stage result status: invalid');

        new StageResult('invalid');
    }

    public function test_constructor_accepts_valid_statuses(): void
    {
        $validStatuses = [
            StageResult::CONTINUE,
            StageResult::GO_BACK,
            StageResult::RETRY,
            StageResult::ABORT
        ];

        foreach ($validStatuses as $status) {
            $result = new StageResult($status);
            $this->assertEquals($status, $result->status);
        }
    }

    public function test_properties_are_readonly(): void
    {
        $result = StageResult::continue('test');

        $reflection = new \ReflectionClass($result);
        $statusProperty = $reflection->getProperty('status');
        $messageProperty = $reflection->getProperty('message');

        $this->assertTrue($statusProperty->isReadOnly());
        $this->assertTrue($messageProperty->isReadOnly());
    }
}
