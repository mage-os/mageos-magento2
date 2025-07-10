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

        // Check if target file exists and is newer than source file
        if ($dir->isExist($targetPath) && !$this->isSourceFileNewer($asset, $dir, $targetPath)) {
            return true;
        }

        return $this->publishAsset($asset);
    }

    /**
     * Check if source file is newer than target file
     *
     * @param Asset\LocalInterface $asset
     * @param \Magento\Framework\Filesystem\Directory\ReadInterface $dir
     * @param string $targetPath
     * @return bool
     */
    private function isSourceFileNewer(Asset\LocalInterface $asset, $dir, $targetPath)
    {
        $sourceFile = $asset->getSourceFile();

        $sourceMtime = $this->getFileModificationTime($sourceFile);
        $targetStat = $dir->stat($targetPath);
        $targetMtime = $targetStat['mtime'] ?? 0;

        return ($sourceMtime > $targetMtime) || ($sourceMtime === 0 && $targetMtime > 0);
    }

    /**
     * Get file modification time
     *
     * @param string $filePath
     * @return int
     */
    private function getFileModificationTime($filePath)
    {
        $mtime = @filemtime($filePath);
        return $mtime !== false ? $mtime : 0;
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
