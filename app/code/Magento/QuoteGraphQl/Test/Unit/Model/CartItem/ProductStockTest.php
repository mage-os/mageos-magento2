<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\QuoteGraphQl\Test\Unit\Model\CartItem;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogInventory\Api\Data\StockStatusInterface;
use Magento\CatalogInventory\Api\StockConfigurationInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Model\StockState;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Item;
use Magento\QuoteGraphQl\Model\CartItem\ProductStock;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for ProductStock::isProductAvailable()
 */
class ProductStockTest extends TestCase
{
    /**
     * @var ProductStock
     */
    private $productStock;

    /**
     * @var ProductRepositoryInterface|MockObject
     */
    private $productRepositoryMock;

    /**
     * @var StockState|MockObject
     */
    private $stockStateMock;

    /**
     * @var StockConfigurationInterface|MockObject
     */
    private $stockConfigurationMock;

    /**
     * @var ScopeConfigInterface|MockObject
     */
    private $scopeConfigMock;

    /**
     * @var StockRegistryInterface|MockObject
     */
    private $stockRegistryMock;

    /**
     * @var StoreManagerInterface|MockObject
     */
    private $storeManagerMock;

    /**
     * Set up mocks and initialize the ProductStock class
     */
    protected function setUp(): void
    {
        $this->productRepositoryMock = $this->createMock(ProductRepositoryInterface::class);
        $this->stockStateMock = $this->createMock(StockState::class);
        $this->stockConfigurationMock = $this->createMock(StockConfigurationInterface::class);
        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);
        $this->stockRegistryMock = $this->createMock(StockRegistryInterface::class);
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->productStock = new ProductStock(
            $this->productRepositoryMock,
            $this->stockStateMock,
            $this->stockConfigurationMock,
            $this->scopeConfigMock,
            $this->stockRegistryMock,
            $this->storeManagerMock
        );
    }

    /**
     * Test isProductAvailable() for a simple product with sufficient stock
     */
    public function testIsProductAvailableForSimpleProductWithStock(): void
    {
        $cartItemMock = $this->getMockBuilder(Item::class)
            ->addMethods(['getQtyToAdd', 'getPreviousQty'])
            ->onlyMethods(['getProductType', 'getProduct'])
            ->disableOriginalConstructor()
            ->getMock();
        $productMock = $this->createMock(ProductInterface::class);
        $storeMock = $this->createMock(StoreInterface::class);
        $stockStatusMock = $this->getMockBuilder(StockStatusInterface::class)
            ->disableOriginalConstructor()
            ->addMethods(['getHasError'])
            ->getMockForAbstractClass();
        $cartItemMock->expects($this->once())
            ->method('getQtyToAdd')
            ->willReturn(2);
        $cartItemMock->expects($this->once())
            ->method('getPreviousQty')
            ->willReturn(1);
        $cartItemMock->expects($this->exactly(2))
            ->method('getProductType')
            ->willReturn('simple');
        $cartItemMock->expects($this->once())
            ->method('getProduct')
            ->willReturn($productMock);
        $productMock->expects($this->once())
            ->method('getId')
            ->willReturn(1);
        $this->storeManagerMock->expects($this->any())
            ->method('getStore')
            ->willReturn($storeMock);
        $storeMock->expects($this->once())
            ->method('getId')
            ->willReturn(1);
        $storeMock->expects($this->once())
            ->method('getWebsiteId')
            ->willReturn(1);
        $this->stockConfigurationMock->expects($this->never())->method('getDefaultScopeId');
        $this->stockStateMock->expects($this->once())
            ->method('checkQuoteItemQty')
            ->with(1, 2, 3, 1, 1)
            ->willReturn($stockStatusMock);
        $stockStatusMock->expects($this->once())
            ->method('getHasError')
            ->willReturn(false);
        $this->assertTrue($this->productStock->isProductAvailable($cartItemMock));
    }

    /**
     * Test isProductAvailable() for a simple product with insufficient stock
     */
    public function testIsProductAvailableForSimpleProductWithoutStock(): void
    {
        $cartItemMock = $this->getMockBuilder(Item::class)
            ->addMethods(['getQtyToAdd', 'getPreviousQty'])
            ->onlyMethods(['getProductType', 'getProduct'])
            ->disableOriginalConstructor()
            ->getMock();
        $productMock = $this->createMock(ProductInterface::class);
        $storeMock = $this->createMock(StoreInterface::class);
        $stockStatusMock = $this->getMockBuilder(StockStatusInterface::class)
            ->disableOriginalConstructor()
            ->addMethods(['getHasError'])
            ->getMockForAbstractClass();
        $cartItemMock->expects($this->once())
            ->method('getQtyToAdd')
            ->willReturn(5);
        $cartItemMock->expects($this->once())
            ->method('getPreviousQty')
            ->willReturn(0);
        $cartItemMock->expects($this->exactly(2))
            ->method('getProductType')
            ->willReturn('simple');
        $cartItemMock->expects($this->once())
            ->method('getProduct')
            ->willReturn($productMock);
        $productMock->expects($this->once())
            ->method('getId')
            ->willReturn(1);
        $this->storeManagerMock->expects($this->exactly(2))
            ->method('getStore')
            ->willReturn($storeMock);
        $storeMock->expects($this->once())
            ->method('getId')
            ->willReturn(1);
        $storeMock->expects($this->once())
            ->method('getWebsiteId')
            ->willReturn(1);
        $this->stockStateMock->expects($this->once())
            ->method('checkQuoteItemQty')
            ->with(1, 5, 5, 0, 1)
            ->willReturn($stockStatusMock);
        $stockStatusMock->expects($this->once())
            ->method('getHasError')
            ->willReturn(true);
        $this->assertFalse($this->productStock->isProductAvailable($cartItemMock));
    }
}
