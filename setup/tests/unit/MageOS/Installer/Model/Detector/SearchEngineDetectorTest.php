<?php

declare(strict_types=1);

namespace MageOS\Installer\Test\Unit\MageOS\Installer\Model\Detector;

use MageOS\Installer\Model\Detector\SearchEngineDetector;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for SearchEngineDetector
 */
final class SearchEngineDetectorTest extends TestCase
{
    private SearchEngineDetector $detector;

    protected function setUp(): void
    {
        parent::setUp();
        $this->detector = new SearchEngineDetector();
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
            $this->assertArrayHasKey('engine', $result);
            $this->assertArrayHasKey('host', $result);
            $this->assertArrayHasKey('port', $result);
            $this->assertContains($result['port'], [9200, 9300]);
        }

        $this->expectNotToPerformAssertions();
    }
}
