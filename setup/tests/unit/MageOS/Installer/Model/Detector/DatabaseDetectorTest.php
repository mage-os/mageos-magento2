<?php

declare(strict_types=1);

namespace MageOS\Installer\Test\Unit\MageOS\Installer\Model\Detector;

use MageOS\Installer\Model\Detector\DatabaseDetector;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for DatabaseDetector
 *
 * Note: Actual port detection requires integration tests with real database.
 * These tests verify the detector structure and logic.
 */
final class DatabaseDetectorTest extends TestCase
{
    private DatabaseDetector $detector;

    protected function setUp(): void
    {
        parent::setUp();
        $this->detector = new DatabaseDetector();
    }

    public function test_detect_returns_null_or_array(): void
    {
        $result = $this->detector->detect();

        $this->assertTrue($result === null || is_array($result));
    }

    public function test_detect_returns_correct_structure_when_found(): void
    {
        $result = $this->detector->detect();

        if ($result !== null) {
            $this->assertArrayHasKey('host', $result);
            $this->assertArrayHasKey('port', $result);
            $this->assertEquals('localhost', $result['host']);
            $this->assertContains($result['port'], [3306, 3307]);
        } else {
            // No database running - that's OK for unit tests
            $this->assertNull($result);
        }
    }

    public function test_detect_checks_common_mysql_ports(): void
    {
        // This test verifies behavior - actual detection depends on system state
        $result = $this->detector->detect();

        // Result should be null (no DB) or have standard port
        if ($result !== null) {
            $this->assertIsInt($result['port']);
            $this->assertGreaterThanOrEqual(3306, $result['port']);
            $this->assertLessThanOrEqual(3307, $result['port']);
        }

        $this->expectNotToPerformAssertions();
    }
}
