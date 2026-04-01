<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Csp\Plugin;

use Magento\Deploy\Console\DeployStaticOptions;
use Magento\Deploy\Service\DeployStaticContent;
use Magento\Csp\Model\SubresourceIntegrityRepositoryPool;
use Magento\Csp\Model\SubresourceIntegrityCollector;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Psr\Log\LoggerInterface;

/**
 * Plugin that removes existing integrity hashes for all assets.
 */
class RemoveAllAssetIntegrityHashes
{
    /**
     * Constant for SRI Hashes filename
     */
    private const SRI_FILENAME = 'sri-hashes.json';

    /**
     * @var SubresourceIntegrityRepositoryPool
     * @deprecated
     * @see RemoveAllAssetIntegrityHashes::deleteAllSriFiles() - SRI hashes are now stored by area/vendor/theme/locale
     */
    private SubresourceIntegrityRepositoryPool $integrityRepositoryPool;

    /**
     * @var SubresourceIntegrityCollector
     */
    private SubresourceIntegrityCollector $integrityCollector;

    /**
     * @var Filesystem
     */
    private Filesystem $filesystem;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param SubresourceIntegrityRepositoryPool $integrityRepositoryPool
     * @param SubresourceIntegrityCollector $integrityCollector
     * @param Filesystem|null $filesystem
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        SubresourceIntegrityRepositoryPool $integrityRepositoryPool,
        SubresourceIntegrityCollector $integrityCollector,
        ?Filesystem $filesystem = null,
        ?LoggerInterface $logger = null
    ) {
        $this->integrityRepositoryPool = $integrityRepositoryPool;
        $this->integrityCollector = $integrityCollector;
        $this->filesystem = $filesystem ?? ObjectManager::getInstance()->get(Filesystem::class);
        $this->logger = $logger ?? ObjectManager::getInstance()->get(LoggerInterface::class);
    }

    /**
     * Removes existing integrity hashes before static content deploy
     *
     * @param DeployStaticContent $subject
     * @param array $options
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeDeploy(
        DeployStaticContent $subject,
        array $options
    ): void {
        if (PHP_SAPI == 'cli' && !$this->isRefreshContentVersionOnly($options)) {
            // Clear stored integrity hashes from all areas
            $this->deleteAllSriFiles();

            // Clear any leftover in-memory integrity hashes from previous runs
            $this->integrityCollector->clear();
        }
    }

    /**
     * Deletes all sri-hashes.json files from static directory
     *
     * @return void
     */
    private function deleteAllSriFiles(): void
    {
        try {
            /** @var WriteInterface $staticDir */
            $staticDir = $this->filesystem->getDirectoryWrite(DirectoryList::STATIC_VIEW);

            // Search recursively for all sri-hashes.json files using multiple patterns
            $patterns = [
                '*/*/*/*/' . self::SRI_FILENAME,  // frontend/Vendor/theme/locale/sri-hashes.json
                '*/' . self::SRI_FILENAME,         // frontend/sri-hashes.json (old style)
                self::SRI_FILENAME,                // sri-hashes.json in root (if any)
            ];

            foreach ($patterns as $pattern) {
                foreach ($staticDir->search($pattern) as $file) {
                    $staticDir->delete($file);
                }
            }
        } catch (\Exception $e) {
            // Log but don't fail - files will be overwritten during deploy
            $this->logger->warning('Failed to delete SRI files: ' . $e->getMessage());
        }
    }

    /**
     * Checks if only version refresh is requested.
     *
     * @param array $options
     *
     * @return bool
     */
    private function isRefreshContentVersionOnly(array $options): bool
    {
        return isset($options[DeployStaticOptions::REFRESH_CONTENT_VERSION_ONLY])
            && $options[DeployStaticOptions::REFRESH_CONTENT_VERSION_ONLY];
    }
}
