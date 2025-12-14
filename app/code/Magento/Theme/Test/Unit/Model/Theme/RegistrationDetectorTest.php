<?php
/**
 * Copyright © Mage-OS, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Theme\Test\Unit\Model\Theme;

use Magento\Framework\View\Design\ThemeInterface;
use Magento\Theme\Model\ResourceModel\Theme\Data\Collection as DbCollection;
use Magento\Theme\Model\ResourceModel\Theme\Data\CollectionFactory;
use Magento\Theme\Model\Theme\Data\Collection as FilesystemCollection;
use Magento\Theme\Model\Theme\RegistrationDetector;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RegistrationDetectorTest extends TestCase
{
    private CollectionFactory|MockObject $collectionFactory;
    private FilesystemCollection|MockObject $filesystemCollection;
    private RegistrationDetector $registrationDetector;

    protected function setUp(): void
    {
        $this->collectionFactory = $this->createMock(CollectionFactory::class);
        $this->filesystemCollection = $this->createMock(FilesystemCollection::class);

        $this->registrationDetector = new RegistrationDetector(
            $this->collectionFactory,
            $this->filesystemCollection
        );
    }

    public function testHasUnregisteredThemeReturnsFalseWhenAllRegistered(): void
    {
        $this->setupDbThemes(['frontend/Vendor/theme1', 'frontend/Vendor/theme2']);
        $this->setupFilesystemThemes(['frontend/Vendor/theme1', 'frontend/Vendor/theme2']);

        $this->assertFalse($this->registrationDetector->hasUnregisteredTheme());
    }

    public function testHasUnregisteredThemeReturnsTrueWhenMissing(): void
    {
        $this->setupDbThemes(['frontend/Vendor/theme1']);
        $this->setupFilesystemThemes(['frontend/Vendor/theme1', 'frontend/Vendor/theme2']);

        $this->assertTrue($this->registrationDetector->hasUnregisteredTheme());
    }

    public function testGetMissingThemesReturnsUnregisteredThemes(): void
    {
        $this->setupDbThemes(['frontend/Vendor/theme1']);
        $this->setupFilesystemThemes(['frontend/Vendor/theme1', 'frontend/Vendor/theme2', 'adminhtml/Vendor/admin']);

        $missing = $this->registrationDetector->getMissingThemes();

        $this->assertCount(2, $missing);
        $this->assertContains('frontend/Vendor/theme2', $missing);
        $this->assertContains('adminhtml/Vendor/admin', $missing);
    }

    public function testGetMissingThemesReturnsEmptyWhenAllRegistered(): void
    {
        $this->setupDbThemes(['frontend/Vendor/theme1']);
        $this->setupFilesystemThemes(['frontend/Vendor/theme1']);

        $this->assertEmpty($this->registrationDetector->getMissingThemes());
    }

    public function testOrphanedDbThemesAreIgnored(): void
    {
        $this->setupDbThemes(['frontend/Vendor/theme1', 'frontend/Vendor/deleted']);
        $this->setupFilesystemThemes(['frontend/Vendor/theme1']);

        $this->assertFalse($this->registrationDetector->hasUnregisteredTheme());
        $this->assertEmpty($this->registrationDetector->getMissingThemes());
    }

    private function setupDbThemes(array $paths): void
    {
        $themes = [];
        foreach ($paths as $path) {
            $theme = $this->createMock(ThemeInterface::class);
            $theme->method('getFullPath')->willReturn($path);
            $themes[] = $theme;
        }

        $dbCollection = $this->createMock(DbCollection::class);
        $dbCollection->method('addTypeFilter')->willReturnSelf();
        $dbCollection->method('getIterator')->willReturn(new \ArrayIterator($themes));

        $this->collectionFactory->method('create')->willReturn($dbCollection);
    }

    private function setupFilesystemThemes(array $paths): void
    {
        $themes = [];
        foreach ($paths as $path) {
            $theme = $this->createMock(ThemeInterface::class);
            $theme->method('getFullPath')->willReturn($path);
            $themes[] = $theme;
        }

        $this->filesystemCollection->method('clear')->willReturnSelf();
        $this->filesystemCollection->method('getIterator')->willReturn(new \ArrayIterator($themes));
    }
}
