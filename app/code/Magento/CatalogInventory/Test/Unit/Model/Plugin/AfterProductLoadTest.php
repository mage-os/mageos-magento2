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
use Magento\ConfigurableProduct\Test\Unit\Model\Product\ProductExtensionAttributes;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AfterProductLoadTest extends TestCase
{
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

        // Create anonymous class extending ProductExtensionAttributes with setStockItem method
        $this->productExtensionMock = new class extends ProductExtensionAttributes {
            private $stockItem = null;

            public function __construct() {}

            public function setStockItem($stockItem) {
                $this->stockItem = $stockItem;
                return $this;
            }

            public function getStockItem() {
                return $this->stockItem;
            }

            // Implement other interface methods as needed
            public function getStockItems() { return null; }
            public function setStockItems($stockItems) { return $this; }
        };
        
        // Use setter method instead of expects for anonymous class
        $this->productExtensionMock->setStockItem($this->stockItemMock);

        $this->productMock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->getMock();
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
