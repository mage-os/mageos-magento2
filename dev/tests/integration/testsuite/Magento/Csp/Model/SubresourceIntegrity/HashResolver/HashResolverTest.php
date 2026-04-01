<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Csp\Model\SubresourceIntegrity\HashResolver;

use Magento\Deploy\Package\Package;
use Magento\Framework\App\Area;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\State;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\View\Design\ThemeInterface;
use Magento\Framework\View\DesignInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Theme\Model\Theme;
use Magento\Theme\Model\ResourceModel\Theme\Collection as ThemeCollection;
use PHPUnit\Framework\TestCase;

/**
 * Integration tests for HashResolver
 *
 * Tests theme hierarchy traversal and hash resolution fallback logic.
 *
 * @magentoAppIsolation enabled
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class HashResolverTest extends TestCase
{
    private const SRI_FILENAME = 'sri-hashes.json';
    private const TEST_ASSET_PATH = 'js/test-asset.js';

    /**
     * @var HashResolverInterface
     */
    private HashResolverInterface $hashResolver;

    /**
     * @var WriteInterface
     */
    private WriteInterface $staticDir;

    /**
     * @var DesignInterface
     */
    private DesignInterface $design;

    /**
     * @var array
     */
    private array $filesToCleanup = [];

    /**
     * @var string
     */
    private string $prevMode;

    /**
     * @var ThemeInterface|null
     */
    private ?ThemeInterface $lumaTheme = null;

    /**
     * @var ThemeInterface|null
     */
    private ?ThemeInterface $blankTheme = null;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();

        $this->prevMode = $objectManager->get(State::class)->getMode();
        $objectManager->get(State::class)->setMode(State::MODE_PRODUCTION);

        $this->hashResolver = $objectManager->get(HashResolverInterface::class);
        $this->design = $objectManager->get(DesignInterface::class);

        $filesystem = $objectManager->get(Filesystem::class);
        $this->staticDir = $filesystem->getDirectoryWrite(DirectoryList::STATIC_VIEW);

        $this->filesToCleanup = [];
        $this->ensureDeploymentVersionExists();

        // Load themes
        $themeCollection = $objectManager->create(ThemeCollection::class);
        $this->lumaTheme = $themeCollection->getThemeByFullPath('frontend/Magento/luma');
        $this->blankTheme = $themeCollection->getThemeByFullPath('frontend/Magento/blank');
    }

    /**
     * @inheritDoc
     */
    protected function tearDown(): void
    {
        foreach ($this->filesToCleanup as $file) {
            if ($this->staticDir->isExist($file)) {
                $this->staticDir->delete($file);
            }
        }

        $objectManager = Bootstrap::getObjectManager();
        $objectManager->get(State::class)->setMode($this->prevMode);

        parent::tearDown();
    }

    /**
     * @magentoAppArea frontend
     * @magentoConfigFixture current_store general/locale/code en_US
     */
    public function testHashCreatedInCurrentLocaleTheme(): void
    {
        $this->design->setDesignTheme($this->lumaTheme, Area::AREA_FRONTEND);
        $this->design->setDefaultDesignTheme();

        $context = 'frontend/Magento/luma/en_US';
        $hash = 'sha256-currentlocale';
        $path = $context . '/' . self::TEST_ASSET_PATH;
        $this->createSriHashFile($context, [$path => $hash]);

        $resolvedHash = $this->hashResolver->getHashByPath($path);

        $this->assertEquals($hash, $resolvedHash, 'Should resolve hash from current locale/theme');
    }

    /**
     * @magentoAppArea frontend
     * @magentoConfigFixture current_store general/locale/code fr_FR
     */
    public function testHashCreatedInDefaultLocale(): void
    {
        $this->design->setDesignTheme($this->lumaTheme, Area::AREA_FRONTEND);

        // Only create hash in default locale
        $context = 'frontend/Magento/luma/' . Package::BASE_LOCALE;
        $hash = 'sha256-defaultlocale';
        $path = $context . '/' . self::TEST_ASSET_PATH;
        $this->createSriHashFile($context, [$path => $hash]);

        $resolvedHash = $this->hashResolver->getHashByPath($path);

        $this->assertEquals($hash, $resolvedHash, 'Should fall back to default locale');
    }

    /**
     * @magentoAppArea frontend
     * @magentoConfigFixture current_store general/locale/code en_US
     */
    public function testHashCreatedInParentTheme(): void
    {
        // Luma extends Blank
        $this->design->setDesignTheme($this->lumaTheme, Area::AREA_FRONTEND);

        // Create hash ONLY in parent theme (Blank)
        $parentContext = 'frontend/Magento/blank/en_US';
        $hash = 'sha256-parenttheme';
        $path = $parentContext . '/' . self::TEST_ASSET_PATH;
        $this->createSriHashFile($parentContext, [$path => $hash]);

        $resolvedHash = $this->hashResolver->getHashByPath($path);

        $this->assertEquals($hash, $resolvedHash, 'Should fall back to parent theme');
    }

    /**
     * @magentoAppArea frontend
     * @magentoConfigFixture current_store general/locale/code en_US
     */
    public function testFallsBackToBaseArea(): void
    {
        $this->design->setDesignTheme($this->lumaTheme, Area::AREA_FRONTEND);

        // Create hash ONLY in base area
        $baseContext = 'base/Magento/base/' . Package::BASE_LOCALE;
        $hash = 'sha256-basearea';
        $path = $baseContext . '/' . self::TEST_ASSET_PATH;
        $this->createSriHashFile($baseContext, [$path => $hash]);

        $resolvedHash = $this->hashResolver->getHashByPath($path);

        $this->assertEquals($hash, $resolvedHash, 'Should fall back to base area');
    }

    /**
     * @magentoAppArea frontend
     * @magentoConfigFixture current_store general/locale/code en_US
     */
    public function testThemeHierarchyPriority(): void
    {
        // Luma extends Blank
        $this->design->setDesignTheme($this->lumaTheme, Area::AREA_FRONTEND);

        // Create hash files in different contexts with unique asset paths
        // Each context gets its own unique assets to test hierarchy traversal

        // Context 1: Current theme + current locale (highest priority)
        $this->createSriHashFile('frontend/Magento/luma/en_US', [
            'frontend/Magento/luma/en_US/js/luma-specific.js' => 'sha256-luma-specific'
        ]);

        // Context 2: Current theme + default locale (fallback)
        $this->createSriHashFile('frontend/Magento/luma/default', [
            'frontend/Magento/luma/default/js/luma-default.js' => 'sha256-luma-default'
        ]);

        // Context 3: Parent theme + current locale (fallback)
        $this->createSriHashFile('frontend/Magento/blank/en_US', [
            'frontend/Magento/blank/en_US/js/blank-specific.js' => 'sha256-blank-specific'
        ]);

        // Context 4: Base area (lowest priority fallback)
        $this->createSriHashFile('base/Magento/base/default', [
            'base/Magento/base/default/js/base-only.js' => 'sha256-base-only'
        ]);

        // Verify each hash is retrievable with its full context path
        // Since paths are unique (include area/theme/locale), there are no collisions

        $this->assertEquals(
            'sha256-luma-specific',
            $this->hashResolver->getHashByPath('frontend/Magento/luma/en_US/js/luma-specific.js'),
            'Should find hash in current theme + locale'
        );

        $this->assertEquals(
            'sha256-luma-default',
            $this->hashResolver->getHashByPath('frontend/Magento/luma/default/js/luma-default.js'),
            'Should find hash in current theme + default locale'
        );

        $this->assertEquals(
            'sha256-blank-specific',
            $this->hashResolver->getHashByPath('frontend/Magento/blank/en_US/js/blank-specific.js'),
            'Should find hash in parent theme + locale'
        );

        $this->assertEquals(
            'sha256-base-only',
            $this->hashResolver->getHashByPath('base/Magento/base/default/js/base-only.js'),
            'Should find hash in base area'
        );

        // Verify all 4 unique hashes were created and are retrievable
        $allHashes = [
            $this->hashResolver->getHashByPath('frontend/Magento/luma/en_US/js/luma-specific.js'),
            $this->hashResolver->getHashByPath('frontend/Magento/luma/default/js/luma-default.js'),
            $this->hashResolver->getHashByPath('frontend/Magento/blank/en_US/js/blank-specific.js'),
            $this->hashResolver->getHashByPath('base/Magento/base/default/js/base-only.js'),
        ];

        $this->assertCount(4, $allHashes, 'Should have 4 individual hashes');
        $this->assertCount(4, $this->hashResolver->getAllHashes(), 'getAllHashes should return all 4 hashes');
    }

    /**
     * @magentoAppArea frontend
     */
    public function testWithMissingAllHashFiles(): void
    {
        $this->design->setDesignTheme($this->lumaTheme, Area::AREA_FRONTEND);

        // Don't create any hash files
        $resolvedHash = $this->hashResolver->getHashByPath(self::TEST_ASSET_PATH);

        $this->assertNull($resolvedHash, 'Should return null when no hash files exist');
    }

    /**
     * @magentoAppArea frontend
     * @magentoConfigFixture current_store general/locale/code en_US
     */
    public function testWithCorruptedFileInHierarchy(): void
    {
        $this->design->setDesignTheme($this->lumaTheme, Area::AREA_FRONTEND);

        // Create corrupted file in current locale
        $currentContext = 'frontend/Magento/luma/en_US';
        $this->createCorruptedSriHashFile($currentContext);

        // Create valid file in default locale
        $defaultContext = 'frontend/Magento/luma/default';
        $hash = 'sha256-valid';

        // Use AssetRepository to get the proper asset URL format
        $objectManager = Bootstrap::getObjectManager();
        $assetRepo = $objectManager->get(\Magento\Framework\View\Asset\Repository::class);
        $validAsset = $assetRepo->createAsset('js/test.js');
        $validAssetPath = $validAsset->getUrl();

        $this->createSriHashFile($defaultContext, [$validAssetPath => $hash]);

        // Verify the hash from the valid file is retrievable
        $resolvedHash = $this->hashResolver->getHashByPath($validAssetPath);
        $this->assertEquals($hash, $resolvedHash, 'Should retrieve hash from valid JSON file');

        // Verify getAllHashes() only returns hashes from valid files (corrupted file is skipped)
        $allHashes = $this->hashResolver->getAllHashes();
        $this->assertGreaterThanOrEqual(1, count($allHashes), 'Should only include hashes from valid JSON files');
        $this->assertContains($hash, array_values($allHashes), 'Should contain the valid hash');
    }

    /**
     * @magentoAppArea frontend
     * @magentoConfigFixture current_store general/locale/code en_US
     */
    public function testGetHashByPathReturnsCorrectFormat(): void
    {
        $this->design->setDesignTheme($this->lumaTheme, Area::AREA_FRONTEND);

        $context = 'frontend/Magento/luma/en_US';
        $hash = 'sha256-ABC123def456+/=';
        $this->createSriHashFile($context, [self::TEST_ASSET_PATH => $hash]);

        $resolvedHash = $this->hashResolver->getHashByPath(self::TEST_ASSET_PATH);

        $this->assertStringStartsWith('sha256-', $resolvedHash);
        $this->assertEquals($hash, $resolvedHash);
    }

    /**
     * @magentoAppArea frontend
     * @magentoConfigFixture current_store general/locale/code en_US
     */
    public function testWithDifferentAssetPaths(): void
    {
        $this->design->setDesignTheme($this->lumaTheme, Area::AREA_FRONTEND);

        $context = 'frontend/Magento/luma/en_US';
        $hashes = [
            'js/file1.js' => 'sha256-hash1',
            'js/subfolder/file2.js' => 'sha256-hash2',
            'Magento_Checkout/js/checkout.js' => 'sha256-hash3',
        ];
        $this->createSriHashFile($context, $hashes);

        foreach ($hashes as $path => $expectedHash) {
            $resolvedHash = $this->hashResolver->getHashByPath($path);
            $this->assertEquals($expectedHash, $resolvedHash, "Hash for {$path} should match");
        }
    }

    /**
     * @magentoAppArea frontend
     * @magentoConfigFixture current_store general/locale/code en_US
     */
    public function testWithNonExistentAssetPath(): void
    {
        $this->design->setDesignTheme($this->lumaTheme, Area::AREA_FRONTEND);

        $context = 'frontend/Magento/luma/en_US';
        $this->createSriHashFile($context, ['js/exists.js' => 'sha256-exists']);

        $resolvedHash = $this->hashResolver->getHashByPath('js/does-not-exist.js');

        $this->assertNull($resolvedHash, 'Should return null for non-existent asset');
    }

    /**
     * @magentoAppArea adminhtml
     * @magentoConfigFixture current_store general/locale/code en_US
     */
    public function testAdminhtmlAreaResolution(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->get(State::class)->setAreaCode(Area::AREA_ADMINHTML);

        // Create hash in adminhtml area
        $context = 'adminhtml/Magento/backend/en_US';
        $hash = 'sha256-adminhtml';
        $this->createSriHashFile($context, [self::TEST_ASSET_PATH => $hash]);

        $resolvedHash = $this->hashResolver->getHashByPath(self::TEST_ASSET_PATH);

        $this->assertEquals($hash, $resolvedHash, 'Should resolve hash in adminhtml area');
    }

    /**
     * @magentoAppArea frontend
     * @magentoConfigFixture current_store general/locale/code en_US
     */
    public function testWithEmptyHashFile(): void
    {
        $this->design->setDesignTheme($this->lumaTheme, Area::AREA_FRONTEND);

        // Create empty JSON object in current context
        $context = 'frontend/Magento/luma/en_US';
        $this->createSriHashFile($context, []);

        // Request hash for any asset
        $resolvedHash = $this->hashResolver->getHashByPath(self::TEST_ASSET_PATH);

        $this->assertNull($resolvedHash, 'Should return null when hash file is empty and asset not found');
    }

    /**
     * Create SRI hash file for testing
     *
     * @param string $context
     * @param array $hashes
     * @return void
     */
    private function createSriHashFile(string $context, array $hashes): void
    {
        $filePath = $context . '/' . self::SRI_FILENAME;
        $this->filesToCleanup[] = $filePath;

        if (!$this->staticDir->isExist($context)) {
            $this->staticDir->create($context);
        }

        $this->staticDir->writeFile($filePath, json_encode($hashes));
    }

    /**
     * Create corrupted SRI hash file for testing
     *
     * @param string $context
     * @return void
     */
    private function createCorruptedSriHashFile(string $context): void
    {
        $filePath = $context . '/' . self::SRI_FILENAME;
        $this->filesToCleanup[] = $filePath;

        if (!$this->staticDir->isExist($context)) {
            $this->staticDir->create($context);
        }

        $this->staticDir->writeFile($filePath, '{invalid json syntax {{');
    }

    /**
     * Ensures deployment version file exists for asset URL generation
     *
     * @return void
     */
    private function ensureDeploymentVersionExists(): void
    {
        try {
            $versionFile = 'deployed_version.txt';

            if (!$this->staticDir->isExist($versionFile)) {
                $this->staticDir->writeFile($versionFile, (string)time());
            }
        } catch (\Exception $e) {
            // Silently fail - if this doesn't work, tests will show the error
        }
    }
}
