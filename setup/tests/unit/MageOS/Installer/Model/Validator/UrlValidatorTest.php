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
    /** @var UrlValidator */
    private UrlValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new UrlValidator();
    }

    /**
     * @dataProvider validUrlProvider
     */
    public function testAcceptsValidUrls(string $url): void
    {
        $result = $this->validator->validate($url);

        $this->assertTrue($result['valid'], "URL '{$url}' should be valid");
        $this->assertNull($result['error']);
    }

    /**
     * @dataProvider invalidUrlProvider
     */
    public function testRejectsInvalidUrls(string $url, string $expectedError): void
    {
        $result = $this->validator->validate($url);

        $this->assertFalse($result['valid']);
        $this->assertEquals($expectedError, $result['error']);
    }

    public function testWarnsAboutHttpUsage(): void
    {
        $result = $this->validator->validate('http://example.com');

        $this->assertTrue($result['valid']);
        $this->assertNull($result['error']);
        $this->assertStringContainsString('HTTPS', $result['warning']);
    }

    public function testNoWarningForHttps(): void
    {
        $result = $this->validator->validate('https://example.com');

        $this->assertTrue($result['valid']);
        $this->assertNull($result['warning']);
    }

    public function testRejectsEmptyUrl(): void
    {
        $result = $this->validator->validate('');

        $this->assertFalse($result['valid']);
        $this->assertEquals('URL cannot be empty', $result['error']);
    }

    public function testNormalizeAddsSchemeWhenMissing(): void
    {
        $result = $this->validator->normalize('example.com');

        $this->assertEquals('http://example.com/', $result['normalized']);
        $this->assertTrue($result['changed']);
        $this->assertContains('Added http:// prefix', $result['changes']);
    }

    public function testNormalizeAddsTrailingSlash(): void
    {
        $result = $this->validator->normalize('https://example.com');

        $this->assertEquals('https://example.com/', $result['normalized']);
        $this->assertTrue($result['changed']);
        $this->assertContains('Added trailing /', $result['changes']);
    }

    public function testNormalizeNoChangesForCompleteUrl(): void
    {
        $result = $this->validator->normalize('https://example.com/');

        $this->assertEquals('https://example.com/', $result['normalized']);
        $this->assertFalse($result['changed']);
        $this->assertEmpty($result['changes']);
    }

    public function testNormalizeAddsBothSchemeAndSlash(): void
    {
        $result = $this->validator->normalize('example.com');

        $this->assertEquals('http://example.com/', $result['normalized']);
        $this->assertTrue($result['changed']);
        $this->assertCount(2, $result['changes']);
    }

    public function testValidateAdminPathAcceptsValidPaths(): void
    {
        $validPaths = ['backend', 'admin-panel', 'secure_admin', 'admin123', 'my-backend'];

        foreach ($validPaths as $path) {
            $result = $this->validator->validateAdminPath($path);
            $this->assertTrue($result['valid'], "Path '{$path}' should be valid");
        }
    }

    public function testValidateAdminPathRejectsEmpty(): void
    {
        $result = $this->validator->validateAdminPath('');

        $this->assertFalse($result['valid']);
        $this->assertEquals('Admin path cannot be empty', $result['error']);
    }

    public function testValidateAdminPathRejectsSpecialCharacters(): void
    {
        $result = $this->validator->validateAdminPath('admin/panel');

        $this->assertFalse($result['valid']);
        $this->assertStringContainsString('letters, numbers, underscores, and hyphens', $result['error']);
    }

    public function testValidateAdminPathWarnsAboutDefaultAdmin(): void
    {
        $result = $this->validator->validateAdminPath('admin');

        $this->assertTrue($result['valid']);
        $this->assertStringContainsString('not recommended', $result['warning']);
    }

    public function testValidateAdminPathNoWarningForCustomPath(): void
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
