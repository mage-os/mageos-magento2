<?php
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Block\Page;

use Magento\Backend\Block\Page\Footer;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\VersionCheck\VersionComparisonInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\State;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template\File\Resolver;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;

class FooterTest extends TestCase
{
    /**
     * @var VersionComparisonInterface|MockObject
     */
    private VersionComparisonInterface|MockObject $versionComparison;

    /**
     * @var ScopeConfigInterface|MockObject
     */
    private ScopeConfigInterface|MockObject $scopeConfig;

    /**
     * @var Footer
     */
    private Footer $block;

    protected function setUp(): void
    {
        $objectManager = $this->getMockForAbstractClass(ObjectManagerInterface::class);
        $objectManager->method('get')->willReturn(new stdClass());
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
        $versionComparison = $this->createMock(VersionComparisonInterface::class);
        $objectManager = $this->getMockForAbstractClass(ObjectManagerInterface::class);
        $objectManager->method('get')
            ->willReturnCallback(function (string $type) use ($versionComparison) {
                if ($type === VersionComparisonInterface::class) {
                    return $versionComparison;
                }
                return new stdClass();
            });
        ObjectManager::setInstance($objectManager);

        $context = $this->createMock(Context::class);
        $productMetadata = $this->createMock(ProductMetadataInterface::class);

        $block = new Footer($context, $productMetadata);

        $this->assertFalse($block->isUpdateAvailable());
        $this->assertNull($block->getLatestVersion());
        $this->assertFalse($block->isMajorOrMinorUpdate());
    }

    public function testGetCacheKeyInfoIncludesVersionString(): void
    {
        $versionComparison = $this->createMock(VersionComparisonInterface::class);
        $versionComparison->method('getLatestVersion')->willReturn('2.5.0');

        $block = $this->createFooterBlockForCacheTest($versionComparison);
        $cacheKeyInfo = $block->getCacheKeyInfo();

        $this->assertContains('latest_version_2.5.0', $cacheKeyInfo);
    }

    public function testGetCacheKeyInfoIncludesNoneWhenNoVersion(): void
    {
        $versionComparison = $this->createMock(VersionComparisonInterface::class);
        $versionComparison->method('getLatestVersion')->willReturn(null);

        $block = $this->createFooterBlockForCacheTest($versionComparison);
        $cacheKeyInfo = $block->getCacheKeyInfo();

        $this->assertContains('latest_version_none', $cacheKeyInfo);
    }

    public function testGetCacheKeyInfoFallsBackOnException(): void
    {
        $versionComparison = $this->createMock(VersionComparisonInterface::class);
        $versionComparison->method('getLatestVersion')
            ->willThrowException(new RuntimeException('broken'));

        $block = $this->createFooterBlockForCacheTest($versionComparison);
        $cacheKeyInfo = $block->getCacheKeyInfo();

        $this->assertContains('latest_version_error', $cacheKeyInfo);
    }

    private function createFooterBlockForCacheTest(
        VersionComparisonInterface|MockObject $versionComparison
    ): Footer {
        $store = $this->createMock(StoreInterface::class);
        $store->method('getCode')->willReturn('default');
        $storeManager = $this->createMock(StoreManagerInterface::class);
        $storeManager->method('getStore')->willReturn($store);

        $appState = $this->createMock(State::class);
        $appState->method('getAreaCode')->willReturn('adminhtml');

        $resolver = $this->createMock(Resolver::class);
        $resolver->method('getTemplateFileName')->willReturn('');

        $urlBuilder = $this->createMock(UrlInterface::class);
        $urlBuilder->method('getBaseUrl')->willReturn('https://example.com/');

        $context = $this->createMock(Context::class);
        $context->method('getScopeConfig')->willReturn($this->scopeConfig);
        $context->method('getStoreManager')->willReturn($storeManager);
        $context->method('getAppState')->willReturn($appState);
        $context->method('getResolver')->willReturn($resolver);
        $context->method('getUrlBuilder')->willReturn($urlBuilder);

        $productMetadata = $this->createMock(ProductMetadataInterface::class);

        return new Footer($context, $productMetadata, $versionComparison);
    }
}
