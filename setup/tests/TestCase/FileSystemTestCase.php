<?php

declare(strict_types=1);

namespace MageOS\Installer\Test\TestCase;

use PHPUnit\Framework\TestCase;

/**
 * Abstract base test for file system operations
 *
 * Uses native PHP temp directory for testing file I/O
 */
abstract class FileSystemTestCase extends TestCase
{
    /**
     * Temporary directory for this test
     */
    protected string $tempDir;

    /**
     * Set up temporary directory before each test
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->tempDir = sys_get_temp_dir() . '/mageos-test-' . uniqid();
        mkdir($this->tempDir, 0777, true);
    }

    /**
     * Clean up temporary directory after each test
     */
    protected function tearDown(): void
    {
        if (is_dir($this->tempDir)) {
            $this->recursiveRemove($this->tempDir);
        }
        parent::tearDown();
    }

    /**
     * Get temp file path
     *
     * @param string $filename Filename within temp directory
     * @return string Full path
     */
    protected function getVirtualFilePath(string $filename): string
    {
        return $this->tempDir . '/' . ltrim($filename, '/');
    }

    /**
     * Create a temp file with content
     *
     * @param string $filename
     * @param string $content
     * @return string Full path to created file
     */
    protected function createVirtualFile(string $filename, string $content): string
    {
        $path = $this->getVirtualFilePath($filename);
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        file_put_contents($path, $content);
        return $path;
    }

    /**
     * Create a temp directory
     *
     * @param string $dirname
     * @return string Full path to created directory
     */
    protected function createVirtualDirectory(string $dirname): string
    {
        $path = $this->getVirtualFilePath($dirname);
        mkdir($path, 0777, true);
        return $path;
    }

    /**
     * Assert that a temp file exists
     */
    protected function assertVirtualFileExists(string $filename, string $message = ''): void
    {
        $path = $this->getVirtualFilePath($filename);
        $this->assertFileExists($path, $message);
    }

    /**
     * Assert that a temp file does not exist
     */
    protected function assertVirtualFileDoesNotExist(string $filename, string $message = ''): void
    {
        $path = $this->getVirtualFilePath($filename);
        $this->assertFileDoesNotExist($path, $message);
    }

    /**
     * Get content of a temp file
     */
    protected function getVirtualFileContent(string $filename): string
    {
        $path = $this->getVirtualFilePath($filename);
        return file_get_contents($path);
    }

    /**
     * Recursively remove directory
     */
    private function recursiveRemove(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->recursiveRemove($path) : unlink($path);
        }
        rmdir($dir);
    }
}
