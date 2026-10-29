<?php
/**
 * Copyright © Mage-OS, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Framework\Cache\Test\Unit\Frontend\Adapter;

use Magento\Framework\App\Cache\Manager;
use Magento\Framework\App\Cache\StateInterface;
use Magento\Framework\App\Cache\Type\FrontendPool;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Cache\CacheConstants;
use Magento\Framework\Cache\Frontend\Adapter\Symfony;
use Magento\Framework\Cache\Frontend\Adapter\SymfonyAdapterProvider;
use Magento\Framework\Cache\Frontend\Decorator\TagScope;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\Framework\Serialize\Serializer\Serialize;
use PHPUnit\Framework\TestCase;

/**
 * Filesystem cache frontends must keep their tag index next to their own data
 *
 * @see https://github.com/mage-os/mageos-magento2/issues/392
 */
class FilesystemTagIndexIsolationTest extends TestCase
{
    private const FPC_TAG = 'FPC';
    private const FPC_ID = 'fpc_entry';

    /**
     * @var string
     */
    private string $varDir;

    /**
     * @var SymfonyAdapterProvider
     */
    private SymfonyAdapterProvider $provider;

    protected function setUp(): void
    {
        $this->varDir = sys_get_temp_dir() . '/mageos_fs_tag_isolation_' . uniqid();
        mkdir($this->varDir . '/cache', 0770, true);
        mkdir($this->varDir . '/page_cache', 0770, true);

        $cacheDir = $this->createStub(ReadInterface::class);
        $cacheDir->method('getAbsolutePath')->willReturn($this->varDir . '/cache/');
        $filesystem = $this->createStub(Filesystem::class);
        $filesystem->method('getDirectoryRead')->willReturn($cacheDir);

        $this->provider = new SymfonyAdapterProvider(
            $filesystem,
            $this->createStub(ResourceConnection::class),
            $this->createStub(Serialize::class)
        );
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->varDir);
    }

    public function testTagIndexIsStoredUnderFrontendCacheDir(): void
    {
        $this->createPageCache()->save('data', self::FPC_ID, ['cat_p_42']);

        $this->assertFileExists($this->varDir . '/page_cache/tags/CAT_P_42');
        $this->assertDirectoryDoesNotExist($this->varDir . '/cache/symfony/tags');
    }

    public function testFpcTagInvalidationSurvivesRemovalOfDefaultCacheDir(): void
    {
        $this->createDefaultCache()->save('data', 'config_entry', ['cat_p_42']);
        $pageCache = $this->createPageCache();
        $pageCache->save('data', self::FPC_ID, ['cat_c_3', 'cat_p_42']);

        $this->removeDirectory($this->varDir . '/cache');

        $pageCache->clean(CacheConstants::CLEANING_MODE_MATCHING_ANY_TAG, ['cat_p_42']);
        $this->assertFalse($this->createPageCache()->load(self::FPC_ID));
    }

    public function testDefaultCacheFlushKeepsFpcTagIndex(): void
    {
        $pageCache = $this->createPageCache();
        $pageCache->save('data', self::FPC_ID, ['cat_p_42']);

        $this->createDefaultCache()->getBackend()->clean();

        $pageCache->clean(CacheConstants::CLEANING_MODE_MATCHING_ANY_TAG, ['cat_p_42']);
        $this->assertFalse($this->createPageCache()->load(self::FPC_ID));
    }

    public function testCacheFlushRemovesEntriesWhoseTagIndexIsLost(): void
    {
        $pageCache = $this->createPageCache();
        $pageCache->save('data', self::FPC_ID, ['cat_p_42']);
        $this->removeDirectory($this->varDir . '/page_cache/tags');
        $this->removeDirectory($this->varDir . '/cache/symfony/tags');

        $pool = $this->createStub(FrontendPool::class);
        $pool->method('get')->willReturn($pageCache);
        $manager = new Manager(
            $this->createStub(TypeListInterface::class),
            $this->createStub(StateInterface::class),
            $pool
        );
        $manager->flush(['full_page']);

        $this->assertFalse($this->createPageCache()->load(self::FPC_ID));
    }

    private function createDefaultCache(): Symfony
    {
        return $this->createFrontend($this->varDir . '/cache/', 'def_');
    }

    private function createPageCache(): TagScope
    {
        return new TagScope($this->createFrontend($this->varDir . '/page_cache', 'fpc_'), self::FPC_TAG);
    }

    private function createFrontend(string $cacheDir, string $idPrefix): Symfony
    {
        $options = ['cache_dir' => $cacheDir];
        $factory = fn () => $this->provider->createAdapter('file', $options, $idPrefix);
        $pool = $factory();

        return new Symfony(
            $factory,
            $this->provider->createTagAdapter('file', $pool, $idPrefix, false, $options),
            Symfony::DEFAULT_LIFETIME,
            $idPrefix
        );
    }

    private function removeDirectory(string $path): void
    {
        if (!is_dir($path)) {
            return;
        }
        foreach (array_diff(scandir($path), ['.', '..']) as $file) {
            $fullPath = $path . '/' . $file;
            is_dir($fullPath) ? $this->removeDirectory($fullPath) : unlink($fullPath);
        }
        rmdir($path);
    }
}
