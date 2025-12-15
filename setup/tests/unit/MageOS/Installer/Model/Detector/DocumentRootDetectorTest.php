<?php

declare(strict_types=1);

namespace MageOS\Installer\Test\Unit\MageOS\Installer\Model\Detector;

use MageOS\Installer\Model\Detector\DocumentRootDetector;
use MageOS\Installer\Test\TestCase\FileSystemTestCase;

/**
 * Unit tests for DocumentRootDetector
 */
class DocumentRootDetectorTest extends FileSystemTestCase
{
    private DocumentRootDetector $detector;

    protected function setUp(): void
    {
        parent::setUp();
        $this->detector = new DocumentRootDetector();
    }

    public function test_detect_returns_expected_structure(): void
    {
        $baseDir = $this->getVirtualFilePath('');

        $result = $this->detector->detect($baseDir);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('isPub', $result);
        $this->assertArrayHasKey('recommendation', $result);
        $this->assertIsBool($result['isPub']);
        $this->assertIsString($result['recommendation']);
    }

    public function test_detect_recommends_pub_for_security(): void
    {
        $baseDir = $this->getVirtualFilePath('');

        $result = $this->detector->detect($baseDir);

        $this->assertStringContainsString('security', $result['recommendation']);
    }

    public function test_detect_handles_missing_directory(): void
    {
        $baseDir = $this->getVirtualFilePath('nonexistent');

        $result = $this->detector->detect($baseDir);

        $this->assertIsArray($result);
        $this->assertFalse($result['isPub']);
    }
}
