<?php
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Block\Page;

use Magento\Backend\Block\Page\Footer;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\VersionUpdate\VersionComparison;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FooterTest extends TestCase
{
    private VersionComparison|MockObject $versionComparison;
    private Footer $block;

    protected function setUp(): void
    {
        $objectManager = $this->getMockForAbstractClass(ObjectManagerInterface::class);
        $objectManager->method('get')->willReturn(new \stdClass());
        ObjectManager::setInstance($objectManager);

        $context = $this->createMock(Context::class);
        $productMetadata = $this->createMock(ProductMetadataInterface::class);
        $this->versionComparison = $this->createMock(VersionComparison::class);

        $this->block = new Footer($context, $productMetadata, $this->versionComparison);
    }

    public function testIsUpdateAvailableDelegates(): void
    {
        $this->versionComparison->method('isUpdateAvailable')->willReturn(true);
        $this->assertTrue($this->block->isUpdateAvailable());
    }

    public function testIsUpdateAvailableReturnsFalseByDefault(): void
    {
        $this->versionComparison->method('isUpdateAvailable')->willReturn(false);
        $this->assertFalse($this->block->isUpdateAvailable());
    }

    public function testGetLatestVersionDelegates(): void
    {
        $this->versionComparison->method('getLatestVersion')->willReturn('2.1.0');
        $this->assertSame('2.1.0', $this->block->getLatestVersion());
    }

    public function testIsMajorOrMinorUpdateDelegates(): void
    {
        $this->versionComparison->method('isMajorOrMinorUpdate')->willReturn(true);
        $this->assertTrue($this->block->isMajorOrMinorUpdate());
    }

    public function testGetReleasesUrl(): void
    {
        $this->assertSame('https://mage-os.org/category/releases/', $this->block->getReleasesUrl());
    }
}
