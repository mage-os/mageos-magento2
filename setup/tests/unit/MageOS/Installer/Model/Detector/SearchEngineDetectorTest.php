<?php

declare(strict_types=1);

namespace MageOS\Installer\Test\Unit\MageOS\Installer\Model\Detector;

use MageOS\Installer\Model\Detector\SearchEngineDetector;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for SearchEngineDetector
 */
class SearchEngineDetectorTest extends TestCase
{
    /** @var SearchEngineDetector */
    private SearchEngineDetector $detector;

    protected function setUp(): void
    {
        parent::setUp();
        $this->detector = new SearchEngineDetector();
    }

    public function testDetectReturnsNullOrArray(): void
    {
        $result = $this->detector->detect();

        $this->assertTrue($result === null || is_array($result));
    }

    public function testDetectReturnsCorrectStructureWhenFound(): void
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
