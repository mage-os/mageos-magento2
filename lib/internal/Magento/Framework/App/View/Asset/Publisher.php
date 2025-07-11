<?php
/**
 * Copyright 2011 Adobe
 * All rights reserved.
 */

namespace Magento\Framework\App\View\Asset;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Directory\WriteFactory;
use Magento\Framework\View\Asset;

/**
 * A publishing service for view assets
 *
 * @api
 * @since 100.0.2
 */
class Publisher
{
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var MaterializationStrategy\Factory
     */
    private $materializationStrategyFactory;

    /**
     * @var WriteFactory
     */
    private $writeFactory;

    /**
     * @var array
     */
    private static $fileHashes = [];

    /**
     * @param \Magento\Framework\Filesystem $filesystem
     * @param MaterializationStrategy\Factory $materializationStrategyFactory
     * @param WriteFactory $writeFactory
     */
    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        MaterializationStrategy\Factory $materializationStrategyFactory,
        WriteFactory $writeFactory
    ) {
        $this->filesystem = $filesystem;
        $this->materializationStrategyFactory = $materializationStrategyFactory;
        $this->writeFactory = $writeFactory;
    }

    /**
     * Publish the asset
     *
     * @param Asset\LocalInterface $asset
     * @return bool
     */
    public function publish(Asset\LocalInterface $asset)
    {
        $dir = $this->filesystem->getDirectoryRead(DirectoryList::STATIC_VIEW);
        $targetPath = $asset->getPath();

        // Check if target file exists and content hasn't changed
        if ($dir->isExist($targetPath) && !$this->hasSourceFileChanged($asset, $dir, $targetPath)) {
            return true;
        }

        return $this->publishAsset($asset);
    }

    /**
     * Check if source file content has changed compared to target file
     *
     * @param Asset\LocalInterface $asset
     * @param \Magento\Framework\Filesystem\Directory\ReadInterface $dir
     * @param string $targetPath
     * @return bool
     */
    private function hasSourceFileChanged(Asset\LocalInterface $asset, $dir, $targetPath)
    {
        $sourceFile = $asset->getSourceFile();
        // Get source file hash
        $sourceHash = $this->getFileHash($sourceFile);

        // Get target file hash
        $targetHash = $this->getTargetFileHash($dir, $targetPath);

        // Compare hashes
        return $sourceHash !== $targetHash;
    }

    /**
     * Get file hash with caching
     *
     * @param string $filePath
     * @return string|false
     */
    private function getFileHash($filePath)
    {
        if (!isset(self::$fileHashes[$filePath])) {
            $content = @file_get_contents($filePath);
            if ($content === false) {
                self::$fileHashes[$filePath] = false;
            } else {
                self::$fileHashes[$filePath] = md5($content);
            }
        }
        return self::$fileHashes[$filePath];
    }

    /**
     * Get target file hash
     *
     * @param \Magento\Framework\Filesystem\Directory\ReadInterface $dir
     * @param string $targetPath
     * @return string|false
     */
    private function getTargetFileHash($dir, $targetPath)
    {
        try {
            $content = $dir->readFile($targetPath);
            return md5($content);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Publish the asset
     *
     * @param Asset\LocalInterface $asset
     * @return bool
     */
    private function publishAsset(Asset\LocalInterface $asset)
    {
        $targetDir = $this->filesystem->getDirectoryWrite(DirectoryList::STATIC_VIEW);
        $fullSource = $asset->getSourceFile();
        $source = basename($fullSource);
        $sourceDir = $this->writeFactory->create(dirname($fullSource));
        $destination = $asset->getPath();
        $strategy = $this->materializationStrategyFactory->create($asset);
        return $strategy->publishFile($sourceDir, $targetDir, $source, $destination);
    }
}
