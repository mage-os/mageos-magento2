<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Csp\Model\SubresourceIntegrity\HashResolver;

use Magento\Deploy\Package\Package;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\State;
use Magento\Framework\Filesystem;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\DesignInterface;
use Magento\Csp\Model\SubresourceIntegrityRepositoryPool;
use Psr\Log\LoggerInterface;

/**
 * Hash resolver for SRI (Subresource Integrity) hashes.
 *
 * This class resolves SRI hashes for JavaScript files by checking multiple locations
 * in a specific order. It supports all three static content deployment strategies:
 *
 * COMPACT STRATEGY (default):
 * - Creates 'default' directories containing shared assets for all locales
 * - Example structure:
 *   - base/Magento/base/default/sri-hashes.json (global shared)
 *   - frontend/Magento/luma/default/sri-hashes.json (Luma shared)
 *   - frontend/Magento/luma/en_US/sri-hashes.json (Luma en_US specific)
 *
 * STANDARD STRATEGY:
 * - Each locale gets its own complete copy of all assets
 * - No 'default' directories created (we still check them for graceful handling)
 * - Example: frontend/Magento/luma/en_US/sri-hashes.json (all Luma en_US files)
 *
 * QUICK STRATEGY:
 * - Similar to compact but with symlinks for faster deployment
 * - Hash resolution logic is identical to compact
 *
 * THEME INHERITANCE:
 * The resolver traverses the theme hierarchy (e.g., Luma → Blank → Base),
 * checking each theme's hash files. This ensures child themes can override
 * parent theme hashes while falling back to parent hashes when needed.
 *
 * SEARCH ORDER (most specific to most general):
 * 1. base/Magento/base/default/ (true base - shared across ALL areas)
 * 2. {area}/Magento/base/default/ (area-specific base - e.g., frontend base)
 * 3. {area}/{theme}/default/ (theme shared - compact strategy only)
 * 4. {area}/{theme}/{locale}/ (theme locale-specific - always present)
 * 5. Repeat steps 3-4 for each parent theme in the hierarchy
 */
class HashResolver implements HashResolverInterface
{
    /**
     * Path to the merged assets SRI hashes file
     */
    private const MERGED_HASHES_FILE = '_cache/merged/sri-hashes.json';

    /**
     * @var SubresourceIntegrityRepositoryPool
     */
    private SubresourceIntegrityRepositoryPool $repositoryPool;

    /**
     * @var State
     */
    private State $appState;

    /**
     * @var DesignInterface
     */
    private DesignInterface $design;

    /**
     * @var UrlInterface
     */
    private UrlInterface $urlBuilder;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var Filesystem
     */
    private Filesystem $filesystem;

    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * @param SubresourceIntegrityRepositoryPool $repositoryPool
     * @param State $appState
     * @param DesignInterface $design
     * @param UrlInterface $urlBuilder
     * @param LoggerInterface $logger
     * @param Filesystem $filesystem
     * @param SerializerInterface $serializer
     */
    public function __construct(
        SubresourceIntegrityRepositoryPool $repositoryPool,
        State $appState,
        DesignInterface $design,
        UrlInterface $urlBuilder,
        LoggerInterface $logger,
        Filesystem $filesystem,
        SerializerInterface $serializer
    ) {
        $this->repositoryPool = $repositoryPool;
        $this->appState = $appState;
        $this->design = $design;
        $this->urlBuilder = $urlBuilder;
        $this->logger = $logger;
        $this->filesystem = $filesystem;
        $this->serializer = $serializer;
    }

    /**
     * @inheritdoc
     */
    public function getAllHashes(): array
    {
        $result = [];

        try {
            $baseUrl = $this->urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_STATIC]);
            $contexts = $this->getContextsToLoad();

            foreach ($contexts as $context) {
                foreach ($this->loadHashesFromContext($context, $baseUrl) as $key => $value) {
                    $result[$key] = $value;
                }
            }

            // AC-16113 introduces new way of storing hashes
            foreach ($this->loadMergedHashes($baseUrl) as $key => $value) {
                $result[$key] = $value;
            }
        } catch (\Exception $e) {
            $this->logger->warning(
                'SRI: Failed to load all hashes',
                ['exception' => $e->getMessage()]
            );
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getHashByPath(string $assetPath): ?string
    {
        try {
            foreach ($this->getContextsToLoad() as $context) {
                $hash = $this->findHashInContext($context, $assetPath);
                if ($hash !== null) {
                    return $hash;
                }
            }
        } catch (\Exception $e) {
            $this->logger->warning(
                'SRI: Failed to get hash by path',
                [
                    'asset_path' => $assetPath,
                    'exception' => $e->getMessage()
                ]
            );
        }

        return null;
    }

    /**
     * Read merged-asset hashes directly from disk, bypassing the repository in-memory cache.
     *
     * Merged files (_cache/merged/*.js) are created at request time by
     * GenerateMergedAssetIntegrity::afterMerge(). Because SubresourceIntegrityRepository
     * caches loaded data in memory, a repository instance that was populated before the
     * merged file existed will not see the new hash. Reading the merged hashes file
     * directly guarantees freshness on every call.
     *
     * @param string $baseUrl
     * @return array<string, string>  URL => sha256 hash
     */
    private function loadMergedHashes(string $baseUrl): array
    {
        $result = [];

        try {
            $staticDir = $this->filesystem->getDirectoryRead(DirectoryList::STATIC_VIEW);

            if (!$staticDir->isFile(self::MERGED_HASHES_FILE)) {
                return $result;
            }

            $data = $this->serializer->unserialize($staticDir->readFile(self::MERGED_HASHES_FILE));

            if (!is_array($data)) {
                return $result;
            }

            foreach ($data as $path => $hash) {
                $result[$baseUrl . $path] = $hash;
            }
        } catch (\Exception $e) {
            $this->logger->debug(
                'SRI: Failed to load merged hashes from disk',
                ['exception' => $e->getMessage()]
            );
        }

        return $result;
    }

    /**
     * Load hashes from a specific context.
     *
     * @param string $context
     * @param string $baseUrl
     * @return array
     */
    private function loadHashesFromContext(string $context, string $baseUrl): array
    {
        $result = [];

        try {
            $repository = $this->repositoryPool->get($context);
            foreach ($repository->getAll() as $integrity) {
                $result[$baseUrl . $integrity->getPath()] = $integrity->getHash();
            }
        } catch (\Exception $e) {
            $this->logger->debug(
                'SRI: Failed to load hashes from context',
                [
                    'context' => $context,
                    'exception' => $e->getMessage()
                ]
            );
        }

        return $result;
    }

    /**
     * Find hash for an asset path in a specific context.
     *
     * @param string $context
     * @param string $assetPath
     * @return string|null
     */
    private function findHashInContext(string $context, string $assetPath): ?string
    {
        try {
            $repository = $this->repositoryPool->get($context);
            $integrity = $repository->getByPath($assetPath);

            if ($integrity) {
                return $integrity->getHash();
            }
        } catch (\Exception $e) {
            $this->logger->debug(
                'SRI: Failed to find hash in context',
                [
                    'context' => $context,
                    'asset_path' => $assetPath,
                    'exception' => $e->getMessage()
                ]
            );
        }

        return null;
    }

    /**
     * Get all contexts to load hashes from.
     *
     * Builds a prioritized list of SRI hash file locations to check, ordered from most shared
     * to most specific. This supports both compact strategy (with 'default' locale directories)
     * and standard strategy (locale-specific only), as well as theme inheritance.
     *
     * Example for frontend/Luma/en_US:
     * - base/Magento/base/default (global shared files)
     * - frontend/Magento/base/default (frontend base shared files)
     * - frontend/Magento/luma/default (Luma shared files - compact only)
     * - frontend/Magento/luma/en_US (Luma en_US specific files)
     * - frontend/Magento/blank/default (Blank parent shared - compact only)
     * - frontend/Magento/blank/en_US (Blank parent en_US specific)
     *
     * @return array<string> Array of context paths (e.g., 'frontend/Magento/luma/en_US')
     */
    private function getContextsToLoad(): array
    {
        $contexts = [];

        // 1. True base area (base/Magento/base/default)
        // Contains JavaScript files shared across ALL areas (frontend, adminhtml, etc.)
        // This is always checked first regardless of current area or theme
        $contexts[] = Package::BASE_AREA . '/' . Package::BASE_THEME . '/' . Package::BASE_LOCALE;

        try {
            $area = $this->appState->getAreaCode();
            $locale = $this->design->getLocale();

            // 2. Area-specific base (e.g., frontend/Magento/base/default or adminhtml/Magento/base/default)
            // Contains JavaScript files shared across all themes within the current area
            // Only created in compact strategy deployments
            $contexts[] = $area . '/' . Package::BASE_THEME . '/' . Package::BASE_LOCALE;

            // 3. Theme hierarchy - traverse from current theme up to parent themes
            // For each theme, check both 'default' locale (shared) and specific locale directories
            //
            // Example: Luma theme (inherits from Blank)
            // - First iteration: Luma theme (current)
            // - Second iteration: Blank theme (parent of Luma)
            // - Loop ends when no more parent themes exist
            $theme = $this->design->getDesignTheme();
            while ($theme) {
                $themePath = $this->design->getThemePath($theme);
                if ($themePath) {
                    // Add 'default' locale context (e.g., frontend/Magento/luma/default)
                    // In compact strategy: Contains assets used by ALL locales of this theme
                    // In standard strategy: This directory may not exist, but we still check it
                    $contexts[] = $area . '/' . $themePath . '/' . Package::BASE_LOCALE;

                    // Add locale-specific context (e.g., frontend/Magento/luma/en_US)
                    // Contains assets specific to this locale, or in standard strategy, ALL assets
                    // Skip if current locale IS 'default' to avoid duplicate entries
                    if ($locale !== Package::BASE_LOCALE) {
                        $contexts[] = $area . '/' . $themePath . '/' . $locale;
                    }
                }

                // Move up the theme inheritance chain
                // Example: Luma -> Blank -> (null, loop exits)
                $theme = $theme->getParentTheme();
            }
        } catch (\Exception $e) {
            $this->logger->debug(
                'SRI: Failed to determine theme/area contexts',
                ['exception' => $e->getMessage()]
            );
        }

        return $contexts;
    }
}
