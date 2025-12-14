<?php

declare(strict_types=1);

namespace MageOS\Installer\Test\Unit\MageOS\Installer\Model\Detector;

use MageOS\Installer\Model\Detector\RedisDetector;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for RedisDetector
 */
final class RedisDetectorTest extends TestCase
{
    private RedisDetector $detector;

    protected function setUp(): void
    {
        parent::setUp();
        $this->detector = new RedisDetector();
    }

    public function test_detect_returns_array(): void
    {
        $result = $this->detector->detect();

        $this->assertIsArray($result);
    }

    public function test_detect_returns_array_of_instances(): void
    {
        $result = $this->detector->detect();

        // Should be array of instances (could be empty if no Redis running)
        foreach ($result as $instance) {
            $this->assertArrayHasKey('host', $instance);
            $this->assertArrayHasKey('port', $instance);
            $this->assertArrayHasKey('name', $instance);
        }
    }

    public function test_detected_instances_have_valid_ports(): void
    {
        $result = $this->detector->detect();

        foreach ($result as $instance) {
            $this->assertGreaterThan(0, $instance['port']);
            $this->assertLessThanOrEqual(65535, $instance['port']);
        }
    }
}
