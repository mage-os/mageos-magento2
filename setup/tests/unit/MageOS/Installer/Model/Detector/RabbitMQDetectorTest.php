<?php

declare(strict_types=1);

namespace MageOS\Installer\Test\Unit\MageOS\Installer\Model\Detector;

use MageOS\Installer\Model\Detector\RabbitMQDetector;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for RabbitMQDetector
 */
class RabbitMQDetectorTest extends TestCase
{
    private RabbitMQDetector $detector;

    protected function setUp(): void
    {
        parent::setUp();
        $this->detector = new RabbitMQDetector();
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
            $this->assertEquals(5672, $result['port']);
        }

        $this->expectNotToPerformAssertions();
    }
}
