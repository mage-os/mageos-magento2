<?php

declare(strict_types=1);

namespace MageOS\Installer\Test\Unit\MageOS\Installer\Model\Validator;

use MageOS\Installer\Model\Validator\UrlValidator;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for UrlValidator
 */
class UrlValidatorTest extends TestCase
{
    private UrlValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new UrlValidator();
    }

    /**
     * @dataProvider validUrlProvider
     */
    public function test_accepts_valid_urls(string $url): void
    {
        $result = $this->validator->validate($url);

        $this->assertTrue($result['valid'], "URL '{$url}' should be valid");
        $this->assertNull($result['error']);
    }

    /**
     * @dataProvider invalidUrlProvider
     */
    public function test_rejects_invalid_urls(string $url, string $expectedError): void
    {
        $result = $this->validator->validate($url);

        $this->assertFalse($result['valid']);
        $this->assertEquals($expectedError, $result['error']);
    }

    public function test_warns_about_http_usage(): void
    {
        $result = $this->validator->validate('http://example.com');

        $this->assertTrue($result['valid']);
        $this->assertNull($result['error']);
        $this->assertStringContainsString('HTTPS', $result['warning']);
    }

    public function test_no_warning_for_https(): void
    {
        $result = $this->validator->validate('https://example.com');

        $this->assertTrue($result['valid']);
        $this->assertNull($result['warning']);
    }

    public function test_rejects_empty_url(): void
    {
        $result = $this->validator->validate('');

        $this->assertFalse($result['valid']);
        $this->assertEquals('URL cannot be empty', $result['error']);
    }

    public function test_normalize_adds_scheme_when_missing(): void
    {
        $result = $this->validator->normalize('example.com');

        $this->assertEquals('http://example.com/', $result['normalized']);
        $this->assertTrue($result['changed']);
        $this->assertContains('Added http:// prefix', $result['changes']);
    }

    public function test_normalize_adds_trailing_slash(): void
    {
        $result = $this->validator->normalize('https://example.com');

        $this->assertEquals('https://example.com/', $result['normalized']);
        $this->assertTrue($result['changed']);
        $this->assertContains('Added trailing /', $result['changes']);
    }

    public function test_normalize_no_changes_for_complete_url(): void
    {
        $result = $this->validator->normalize('https://example.com/');

        $this->assertEquals('https://example.com/', $result['normalized']);
        $this->assertFalse($result['changed']);
        $this->assertEmpty($result['changes']);
    }

    public function test_normalize_adds_both_scheme_and_slash(): void
    {
        $result = $this->validator->normalize('example.com');

        $this->assertEquals('http://example.com/', $result['normalized']);
        $this->assertTrue($result['changed']);
        $this->assertCount(2, $result['changes']);
    }

    public function test_validate_admin_path_accepts_valid_paths(): void
    {
        $validPaths = ['backend', 'admin-panel', 'secure_admin', 'admin123', 'my-backend'];

        foreach ($validPaths as $path) {
            $result = $this->validator->validateAdminPath($path);
            $this->assertTrue($result['valid'], "Path '{$path}' should be valid");
        }
    }

    public function test_validate_admin_path_rejects_empty(): void
    {
        $result = $this->validator->validateAdminPath('');

        $this->assertFalse($result['valid']);
        $this->assertEquals('Admin path cannot be empty', $result['error']);
    }

    public function test_validate_admin_path_rejects_special_characters(): void
    {
        $result = $this->validator->validateAdminPath('admin/panel');

        $this->assertFalse($result['valid']);
        $this->assertStringContainsString('letters, numbers, underscores, and hyphens', $result['error']);
    }

    public function test_validate_admin_path_warns_about_default_admin(): void
    {
        $result = $this->validator->validateAdminPath('admin');

        $this->assertTrue($result['valid']);
        $this->assertStringContainsString('not recommended', $result['warning']);
    }

    public function test_validate_admin_path_no_warning_for_custom_path(): void
    {
        $result = $this->validator->validateAdminPath('backend');

        $this->assertTrue($result['valid']);
        $this->assertNull($result['warning']);
    }

    public static function validUrlProvider(): array
    {
        return [
            'http url' => ['http://example.com'],
            'https url' => ['https://example.com'],
            'with port' => ['http://example.com:8080'],
            'with path' => ['https://example.com/magento'],
            'localhost' => ['http://localhost'],
            'ip address' => ['http://192.168.1.1'],
            'subdomain' => ['https://shop.example.com'],
            'no scheme normalized' => ['example.com'],
        ];
    }

    public static function invalidUrlProvider(): array
    {
        return [
            'empty' => ['', 'URL cannot be empty'],
            'spaces' => ['not a url', 'Invalid URL format'],
        ];
    }
}
