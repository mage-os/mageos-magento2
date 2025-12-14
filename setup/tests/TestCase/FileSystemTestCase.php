<?php

declare(strict_types=1);

namespace MageOS\Installer\Test\TestCase;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

/**
 * Abstract base test for file system operations
 *
 * Provides vfsStream setup for testing file I/O without touching real filesystem
 */
abstract class FileSystemTestCase extends TestCase
{
    /**
     * Virtual filesystem root
     */
    protected vfsStreamDirectory $vfs;

    /**
     * Set up virtual filesystem before each test
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->vfs = vfsStream::setup('root');
    }

    /**
     * Get virtual file path
     *
     * @param string $filename Filename within virtual filesystem
     * @return string Full vfsStream URL path
     */
    protected function getVirtualFilePath(string $filename): string
    {
        return vfsStream::url("root/{$filename}");
    }

    /**
     * Create a virtual file with content
     *
     * @param string $filename
     * @param string $content
     * @return string Full path to created file
     */
    protected function createVirtualFile(string $filename, string $content): string
    {
        $path = $this->getVirtualFilePath($filename);
        file_put_contents($path, $content);
        return $path;
    }

    /**
     * Create a virtual directory
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
     * Assert that a virtual file exists
     */
    protected function assertVirtualFileExists(string $filename, string $message = ''): void
    {
        $path = $this->getVirtualFilePath($filename);
        $this->assertFileExists($path, $message);
    }

    /**
     * Assert that a virtual file does not exist
     */
    protected function assertVirtualFileDoesNotExist(string $filename, string $message = ''): void
    {
        $path = $this->getVirtualFilePath($filename);
        $this->assertFileDoesNotExist($path, $message);
    }

    /**
     * Get content of a virtual file
     */
    protected function getVirtualFileContent(string $filename): string
    {
        $path = $this->getVirtualFilePath($filename);
        return file_get_contents($path);
    }
}
