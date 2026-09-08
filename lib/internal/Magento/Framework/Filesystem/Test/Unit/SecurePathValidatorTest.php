<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Framework\Filesystem\Test\Unit;

use Magento\Framework\Filesystem\SecurePathValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SecurePathValidatorTest extends TestCase
{
    private const ROOT = '/srv/site';

    #[DataProvider('unsafeProvider')]
    public function testUnsafePaths(string $path, ?string $root = self::ROOT): void
    {
        $this->assertTrue(SecurePathValidator::isUnsafeIncludePathForRoot($path, $root), $path);
    }

    /**
     * @return array
     */
    public static function unsafeProvider(): array
    {
        return [
            'empty' => [''],
            'null byte' => ["/srv/site/app/design/x.phtml\0.png"],
            'phar wrapper' => ['phar:///tmp/x.phar/y.phtml'],
            'php filter wrapper' => ['php://filter/resource=/etc/passwd'],
            'var report' => ['/srv/site/var/report/deadbeef'],
            'var log' => ['/srv/site/var/log/system.log'],
            'var tmp' => ['/srv/site/var/tmp/x.phtml'],
            'var cache' => ['/srv/site/var/cache/x.php'],
            'var page_cache' => ['/srv/site/var/page_cache/x.php'],
            'pub media' => ['/srv/site/pub/media/evil.phtml'],
            'pub static' => ['/srv/site/pub/static/evil.phtml'],
            'repeated separators' => ['/srv/site//var///report/x'],
            'dot segments' => ['/srv/site/./var/./report/x'],
            'mixed case' => ['/srv/site/VAR/Report/x'],
            'relative to root' => ['var/report/x'],
            'windows root' => ['C:\site\var\report\x', 'C:\site'],
            'windows root trailing separator' => ['C:/site/var/report/x', 'C:\site\\'],
            'root with trailing slash' => ['/srv/site/var/report/x', '/srv/site/'],
            'unanchored fallback' => ['/srv/site/var/report/x', null],
            'unanchored fallback nested install path' => ['/var/tmp/ci/app/design/frontend/V/t/x.phtml', null],
        ];
    }

    #[DataProvider('safeProvider')]
    public function testSafePaths(string $path, ?string $root = self::ROOT): void
    {
        $this->assertFalse(SecurePathValidator::isUnsafeIncludePathForRoot($path, $root), $path);
    }

    /**
     * @return array
     */
    public static function safeProvider(): array
    {
        return [
            'module view' => ['/srv/site/app/code/Vendor/Mod/view/frontend/templates/x.phtml'],
            'theme view' => ['/srv/site/app/design/frontend/Vendor/theme/Module/templates/x.phtml'],
            'vendor view' => ['/srv/site/vendor/magento/module-catalog/view/frontend/templates/x.phtml'],
            'generated code' => ['/srv/site/generated/code/Magento/Cms/Block/Block/Interceptor.php'],
            'report substring not a path segment' => ['/srv/site/app/code/Vendor/Reporting/view/x.phtml'],
            'other var directory' => ['/srv/site/var/view_preprocessed/x.less'],
            'blocked segment outside the root' => ['/other/var/report/x'],
            'install rooted under /var/tmp' => [
                '/var/tmp/ci/app/design/frontend/V/t/Magento_Theme/templates/html/header.phtml',
                '/var/tmp/ci',
            ],
            'install rooted under /var/cache' => [
                '/var/cache/build/vendor/magento/module-theme/view/frontend/templates/x.phtml',
                '/var/cache/build',
            ],
            'blocked segment inside a module path under such an install' => [
                '/var/tmp/ci/app/code/Vendor/Mod/var/report/x.phtml',
                '/var/tmp/ci',
            ],
        ];
    }

    public function testDefaultRootIsApplicationRoot(): void
    {
        $this->assertTrue(defined('BP'), 'The unit bootstrap must define BP');
        $this->assertTrue(SecurePathValidator::isUnsafeIncludePath(BP . '/var/report/deadbeef'));
        $this->assertTrue(SecurePathValidator::isUnsafeIncludePath(BP . '/var/page_cache/x.php'));
        $this->assertFalse(SecurePathValidator::isUnsafeIncludePath(BP . '/app/code/Vendor/Mod/view/x.phtml'));
        $this->assertFalse(SecurePathValidator::isUnsafeIncludePath('/other/var/report/deadbeef'));
    }

    public function testTraversalIntoBlockedDirectoryIsResolved(): void
    {
        $base = sys_get_temp_dir() . '/mageos_spv_' . uniqid();
        mkdir($base . '/var/report', 0777, true);
        mkdir($base . '/app/code', 0777, true);
        $target = $base . '/var/report/deadbeef';
        file_put_contents($target, 'x');

        try {
            $traversal = $base . '/app/code/../../var/report/deadbeef';
            $this->assertTrue(SecurePathValidator::isUnsafeIncludePathForRoot($traversal, $base));
            $this->assertFalse(
                SecurePathValidator::isUnsafeIncludePathForRoot($base . '/app/code/../code/x.phtml', $base)
            );
        } finally {
            $this->removeFile($target);
            $this->removeDir($base . '/var/report');
            $this->removeDir($base . '/var');
            $this->removeDir($base . '/app/code');
            $this->removeDir($base . '/app');
            $this->removeDir($base);
        }
    }

    /**
     * @param string $file
     * @return void
     */
    private function removeFile(string $file): void
    {
        if (is_file($file)) {
            unlink($file);
        }
    }

    /**
     * @param string $dir
     * @return void
     */
    private function removeDir(string $dir): void
    {
        if (is_dir($dir)) {
            rmdir($dir);
        }
    }
}
