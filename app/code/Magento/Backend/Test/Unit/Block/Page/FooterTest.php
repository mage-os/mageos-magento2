<?php
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Block\Page;

use Magento\Backend\Api\VersionComparisonInterface;
use Magento\Backend\Block\Page\Footer;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FooterTest extends TestCase
{
    private VersionComparisonInterface|MockObject $versionComparison;
    private ScopeConfigInterface|MockObject $scopeConfig;
    private Footer $block;

    protected function setUp(): void
    {
        $objectManager = $this->getMockForAbstractClass(ObjectManagerInterface::class);
        $objectManager->method('get')->willReturn(new \stdClass());
        ObjectManager::setInstance($objectManager);

        $this->scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $context = $this->createMock(Context::class);
        $context->method('getScopeConfig')->willReturn($this->scopeConfig);
        $productMetadata = $this->createMock(ProductMetadataInterface::class);
        $this->versionComparison = $this->createMock(VersionComparisonInterface::class);

        $this->block = new Footer($context, $productMetadata, $this->versionComparison);
    }

    public function testGetReleasesUrl(): void
    {
        $this->scopeConfig->method('getValue')
            ->with('system/version_check/releases_url')
            ->willReturn('https://mage-os.org/category/releases/');

        $this->assertSame('https://mage-os.org/category/releases/', $this->block->getReleasesUrl());
    }

    public function testIsUpdateAvailableDelegatesToVersionComparison(): void
    {
        $this->versionComparison->method('isUpdateAvailable')->willReturn(true);

        $this->assertTrue($this->block->isUpdateAvailable());
    }

    public function testGetLatestVersionDelegatesToVersionComparison(): void
    {
        $this->versionComparison->method('getLatestVersion')->willReturn('2.5.0');

        $this->assertSame('2.5.0', $this->block->getLatestVersion());
    }

    public function testIsMajorOrMinorUpdateDelegatesToVersionComparison(): void
    {
        $this->versionComparison->method('isMajorOrMinorUpdate')->willReturn(true);

        $this->assertTrue($this->block->isMajorOrMinorUpdate());
    }

    public function testIsMajorOrMinorUpdateReturnsFalseForPatchOnly(): void
    {
        $this->versionComparison->method('isMajorOrMinorUpdate')->willReturn(false);

        $this->assertFalse($this->block->isMajorOrMinorUpdate());
    }

    public function testBackwardCompatibleWithoutVersionComparison(): void
    {
        $context = $this->createMock(Context::class);
        $productMetadata = $this->createMock(ProductMetadataInterface::class);

        $block = new Footer($context, $productMetadata);

        $this->assertFalse($block->isUpdateAvailable());
        $this->assertNull($block->getLatestVersion());
        $this->assertFalse($block->isMajorOrMinorUpdate());
    }
}
