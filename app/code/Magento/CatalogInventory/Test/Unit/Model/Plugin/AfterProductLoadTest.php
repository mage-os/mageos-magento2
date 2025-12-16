<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogInventory\Test\Unit\Model\Plugin;

use Magento\Catalog\Api\Data\ProductExtensionFactory;
use Magento\Catalog\Api\Data\ProductExtensionInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Model\Plugin\AfterProductLoad;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\MockCreationTrait;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 */
class AfterProductLoadTest extends TestCase
{
    use MockCreationTrait;

    /**
     * @var AfterProductLoad
     */
    protected $plugin;

    /**
     * @var ProductInterface|MockObject
     */
    protected $productMock;

    /**
     * @var ProductExtensionInterface|MockObject
     */
    protected $productExtensionMock;

    /**
     * @var StockItemInterface|MockObject
     */
    protected $stockItemMock;

    protected function setUp(): void
    {
        $stockRegistryMock = $this->createMock(StockRegistryInterface::class);

        $this->plugin = new AfterProductLoad(
            $stockRegistryMock
        );

        $productId = 5494;
        $this->stockItemMock = $this->createMock(StockItemInterface::class);

        $stockRegistryMock->expects($this->once())
            ->method('getStockItem')
            ->with($productId)
            ->willReturn($this->stockItemMock);

        $this->productExtensionMock = $this->createStub(ProductExtensionInterface::class);
        
        // Implement stateful behavior for setStockItem/getStockItem
        $stockItem = $this->stockItemMock;
        $productExtensionMock = $this->productExtensionMock;
        
        $this->productExtensionMock->method('setStockItem')->willReturnCallback(
            function ($val) use (&$stockItem, $productExtensionMock) {
                $stockItem = $val;
                return $productExtensionMock;
            }
        );
        $this->productExtensionMock->method('getStockItem')->willReturnCallback(function () use (&$stockItem) {
            return $stockItem;
        });

        $this->productMock = $this->createMock(Product::class);
        $this->productMock->expects($this->once())
            ->method('setExtensionAttributes')
            ->with($this->productExtensionMock)
            ->willReturnSelf();
        $this->productMock->expects(($this->once()))
            ->method('getId')
            ->willReturn($productId);
    }

    public function testAfterLoad()
    {
        $this->productMock->expects($this->once())
            ->method('getExtensionAttributes')
            ->willReturn($this->productExtensionMock);

        $result = $this->plugin->afterLoad($this->productMock);
        
        // Verify the plugin returns the product
        $this->assertEquals($this->productMock, $result);
        
        // Verify that setStockItem was called on the extension attributes
        $this->assertSame($this->stockItemMock, $this->productExtensionMock->getStockItem());
    }
}
