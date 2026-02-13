<?php

declare(strict_types=1);

namespace MageOS\Installer\Test\Unit\MageOS\Installer\Model\Detector;

use MageOS\Installer\Model\Detector\UrlDetector;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for UrlDetector
 */
class UrlDetectorTest extends TestCase
{
    /** @var UrlDetector */
    private UrlDetector $detector;

    protected function setUp(): void
    {
        parent::setUp();
        $this->detector = new UrlDetector();
    }

    public function testDetectReturnsString(): void
    {
        $result = $this->detector->detect('/var/www/magento');

        $this->assertIsString($result);
    }

    public function testDetectReturnsUrlWithTrailingSlash(): void
    {
        $result = $this->detector->detect('/var/www/magento');

        $this->assertStringEndsWith('/', $result);
    }

    public function testDetectUsesDirectoryNameAsBase(): void
    {
        $result = $this->detector->detect('/var/www/myshop');

        $this->assertStringContainsString('myshop', $result);
    }

    public function testDetectDefaultsToTestDomain(): void
    {
        $result = $this->detector->detect('/var/www/magento');

        $this->assertStringContainsString('.test', $result);
    }

    public function testDetectReturnsHttpUrl(): void
    {
        $result = $this->detector->detect('/var/www/magento');

        $this->assertStringStartsWith('http://', $result);
    }

    public function testDetectHandlesVariousDirectoryNames(): void
    {
        $directories = [
            '/var/www/shop' => 'shop',
            '/var/www/store' => 'store',
            '/var/www/magento-dev' => 'magento-dev'
        ];

        foreach ($directories as $dir => $expected) {
            $result = $this->detector->detect($dir);
            $this->assertStringContainsString($expected, $result);
        }
    }
}
