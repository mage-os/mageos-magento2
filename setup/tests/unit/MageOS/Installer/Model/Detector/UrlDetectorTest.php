<?php

declare(strict_types=1);

namespace MageOS\Installer\Test\Unit\MageOS\Installer\Model\Detector;

use MageOS\Installer\Model\Detector\UrlDetector;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for UrlDetector
 */
final class UrlDetectorTest extends TestCase
{
    private UrlDetector $detector;

    protected function setUp(): void
    {
        parent::setUp();
        $this->detector = new UrlDetector();
    }

    public function test_detect_returns_string(): void
    {
        $result = $this->detector->detect('/var/www/magento');

        $this->assertIsString($result);
    }

    public function test_detect_returns_url_with_trailing_slash(): void
    {
        $result = $this->detector->detect('/var/www/magento');

        $this->assertStringEndsWith('/', $result);
    }

    public function test_detect_uses_directory_name_as_base(): void
    {
        $result = $this->detector->detect('/var/www/myshop');

        $this->assertStringContainsString('myshop', $result);
    }

    public function test_detect_defaults_to_test_domain(): void
    {
        $result = $this->detector->detect('/var/www/magento');

        $this->assertStringContainsString('.test', $result);
    }

    public function test_detect_returns_http_url(): void
    {
        $result = $this->detector->detect('/var/www/magento');

        $this->assertStringStartsWith('http://', $result);
    }

    public function test_detect_handles_various_directory_names(): void
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
