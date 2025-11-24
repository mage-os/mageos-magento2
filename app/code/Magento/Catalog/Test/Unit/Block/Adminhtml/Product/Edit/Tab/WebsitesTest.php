<?php
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Block\Adminhtml\Product\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Element\ElementCreator;
use Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Websites;
use Magento\Catalog\Model\Product;
use Magento\Framework\Escaper;
use Magento\Framework\Filesystem\Directory\ReadInterface as DirectoryHelper;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Registry;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Store\Model\Group;
use Magento\Store\Model\GroupFactory;
use Magento\Store\Model\ResourceModel\Group\Collection as GroupCollection;
use Magento\Store\Model\ResourceModel\Store\Collection as StoreCollection;
use Magento\Store\Model\ResourceModel\Website\Collection as WebsiteCollection;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Website;
use Magento\Store\Model\WebsiteFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class WebsitesTest extends TestCase
{
    private Websites $block;
    private Registry|MockObject $registry;
    private Product|MockObject $product;
    private StoreManagerInterface|MockObject $storeManager;
    private WebsiteFactory|MockObject $websiteFactory;
    private GroupFactory|MockObject $groupFactory;
    private StoreFactory|MockObject $storeFactory;
    private Escaper|MockObject $escaper;

    protected function setUp(): void
    {
        $objectManagerHelper = new ObjectManager($this);
        $objectManagerHelper->prepareObjectManager();

        $this->registry = $this->createMock(Registry::class);

        // Mock product with methods
        $this->product = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getStoreId', 'getId', 'getWebsiteIds'])
            ->addMethods(['getWebsitesReadonly'])
            ->getMock();
        $this->storeManager = $this->getMockForAbstractClass(StoreManagerInterface::class);
        $this->websiteFactory = $this->createMock(WebsiteFactory::class);
        $this->groupFactory = $this->createMock(GroupFactory::class);
        $this->storeFactory = $this->createMock(StoreFactory::class);
        $this->escaper = $this->createMock(Escaper::class);

        $context = $this->createMock(Context::class);
        $context->method('getStoreManager')->willReturn($this->storeManager);
        $context->method('getEscaper')->willReturn($this->escaper);

        $this->block = $this->getMockBuilder(Websites::class)
            ->setConstructorArgs([
                $context,
                $this->websiteFactory,
                $this->groupFactory,
                $this->storeFactory,
                $this->registry
            ])
            ->onlyMethods(['getWebsiteCollection', 'getGroupCollection', 'getStoreCollection', 'escapeHtml'])
            ->getMock();

        // Default: product in registry
        $this->registry->method('registry')->with('product')->willReturn($this->product);
    }

    public function testGetProduct(): void
    {
        $this->assertSame($this->product, $this->block->getProduct());
    }

    public function testGetStoreId(): void
    {
        $this->product->method('getStoreId')->willReturn(5);
        $this->assertSame(5, $this->block->getStoreId());
    }

    public function testGetProductId(): void
    {
        $this->product->method('getId')->willReturn(123);
        $this->assertSame(123, $this->block->getProductId());
    }

    public function testGetWebsites(): void
    {
        $websiteIds = [1, 2, 3];
        $this->product->method('getWebsiteIds')->willReturn($websiteIds);
        $this->assertSame($websiteIds, $this->block->getWebsites());
    }

    public function testHasWebsiteReturnsTrue(): void
    {
        $this->product->method('getWebsiteIds')->willReturn([1, 2, 3]);
        $this->assertTrue($this->block->hasWebsite(2));
    }

    public function testHasWebsiteReturnsFalse(): void
    {
        $this->product->method('getWebsiteIds')->willReturn([1, 2, 3]);
        $this->assertFalse($this->block->hasWebsite(999));
    }

    public function testIsReadonlyReturnsTrue(): void
    {
        $this->product->method('getWebsitesReadonly')->willReturn(true);
        $this->assertTrue($this->block->isReadonly());
    }

    public function testIsReadonlyReturnsFalse(): void
    {
        $this->product->method('getWebsitesReadonly')->willReturn(false);
        $this->assertFalse($this->block->isReadonly());
    }

    public function testGetStoreName(): void
    {
        $store = $this->createMock(Store::class);
        $store->method('getName')->willReturn('Main Store');
        $this->storeManager->method('getStore')->with(1)->willReturn($store);

        $this->assertSame('Main Store', $this->block->getStoreName(1));
    }

    public function testGetChooseFromStoreHtml(): void
    {
        // Mock product websites
        $this->product->method('getWebsiteIds')->willReturn([1]);

        // Mock website
        $website = $this->createMock(Website::class);
        $website->method('getId')->willReturn(1);
        $website->method('getName')->willReturn('Main Website');

        $websiteCollection = $this->createMock(WebsiteCollection::class);
        $websiteCollection->method('getIterator')->willReturn(new \ArrayIterator([$website]));

        // Mock group
        $group = $this->createMock(Group::class);
        $group->method('getName')->willReturn('Main Store');

        $groupCollection = $this->createMock(GroupCollection::class);
        $groupCollection->method('getIterator')->willReturn(new \ArrayIterator([$group]));

        // Mock store
        $store = $this->createMock(Store::class);
        $store->method('getId')->willReturn(1);
        $store->method('getName')->willReturn('Default Store View');

        $storeCollection = $this->createMock(StoreCollection::class);
        $storeCollection->method('getIterator')->willReturn(new \ArrayIterator([$store]));

        // Stub collection methods
        $this->block->method('getWebsiteCollection')->willReturn($websiteCollection);
        $this->block->method('getGroupCollection')->with($website)->willReturn($groupCollection);
        $this->block->method('getStoreCollection')->with($group)->willReturn($storeCollection);
        $this->block->method('escapeHtml')->willReturnArgument(0);

        // Mock target store
        $storeTo = $this->createMock(Store::class);
        $storeTo->method('getId')->willReturn(2);

        // Act
        $html = $this->block->getChooseFromStoreHtml($storeTo);

        // Assert
        $this->assertStringContainsString('<select', $html);
        $this->assertStringContainsString('name="copy_to_stores[2]"', $html);
        $this->assertStringContainsString('Default Values', $html);
        $this->assertStringContainsString('Main Website', $html);
        $this->assertStringContainsString('Main Store', $html);
        $this->assertStringContainsString('Default Store View', $html);
    }

    public function testGetChooseFromStoreHtmlSkipsWebsitesNotAssignedToProduct(): void
    {
        // Mock product with only website ID 1
        $this->product->method('getWebsiteIds')->willReturn([1]);

        // Mock two websites - one assigned, one not assigned
        $website1 = $this->createMock(Website::class);
        $website1->method('getId')->willReturn(1);
        $website1->method('getName')->willReturn('Assigned Website');

        $website2 = $this->createMock(Website::class);
        $website2->method('getId')->willReturn(2);
        $website2->method('getName')->willReturn('Skipped Website');

        $websiteCollection = $this->createMock(WebsiteCollection::class);
        $websiteCollection->method('getIterator')->willReturn(new \ArrayIterator([$website1, $website2]));

        // Mock group and store for assigned website
        $group = $this->createMock(Group::class);
        $group->method('getName')->willReturn('Main Store');

        $groupCollection = $this->createMock(GroupCollection::class);
        $groupCollection->method('getIterator')->willReturn(new \ArrayIterator([$group]));

        $store = $this->createMock(Store::class);
        $store->method('getId')->willReturn(1);
        $store->method('getName')->willReturn('Default Store View');

        $storeCollection = $this->createMock(StoreCollection::class);
        $storeCollection->method('getIterator')->willReturn(new \ArrayIterator([$store]));

        // Stub collection methods
        $this->block->method('getWebsiteCollection')->willReturn($websiteCollection);
        $this->block->method('getGroupCollection')->with($website1)->willReturn($groupCollection);
        $this->block->method('getStoreCollection')->with($group)->willReturn($storeCollection);
        $this->block->method('escapeHtml')->willReturnArgument(0);

        // Mock target store
        $storeTo = $this->createMock(Store::class);
        $storeTo->method('getId')->willReturn(2);

        // Act
        $html = $this->block->getChooseFromStoreHtml($storeTo);

        // Assert - should contain assigned website but not the skipped one
        $this->assertStringContainsString('Assigned Website', $html);
        $this->assertStringNotContainsString('Skipped Website', $html);
        $this->assertStringContainsString('Main Store', $html);
        $this->assertStringContainsString('Default Store View', $html);
    }
}
