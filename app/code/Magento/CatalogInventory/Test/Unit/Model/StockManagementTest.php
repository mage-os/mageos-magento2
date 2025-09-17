<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogInventory\Test\Unit\Model;

use PHPUnit\Framework\Attributes\DataProvider;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Api\StockConfigurationInterface;
use Magento\CatalogInventory\Model\ResourceModel\QtyCounterInterface;
use Magento\CatalogInventory\Model\ResourceModel\Stock as ResourceStock;
use Magento\CatalogInventory\Model\Spi\StockRegistryProviderInterface;
use Magento\CatalogInventory\Model\StockManagement;
use Magento\CatalogInventory\Model\StockRegistryStorage;
use Magento\CatalogInventory\Model\StockState;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Magento\CatalogInventory\Model\StockManagement
 */
class StockManagementTest extends TestCase
{
    /**
     * @var StockManagement|MockObject
     */
    private $stockManagement;

    /**
     * @var ResourceStock|MockObject
     */
    private $stockResourceMock;

    /**
     * @var StockRegistryProviderInterface|MockObject
     */
    private $stockRegistryProviderMock;

    /**
     * @var StockState|MockObject
     */
    private $stockStateMock;

    /**
     * @var StockConfigurationInterface|MockObject
     */
    private $stockConfigurationMock;

    /**
     * @var ProductRepositoryInterface|MockObject
     */
    private $productRepositoryMock;

    /**
     * @var QtyCounterInterface|MockObject
     */
    private $qtyCounterMock;

    /**
     * @var StockRegistryStorage|MockObject
     */
    private $stockRegistryStorageMock;

    /**
     * @var StockItemInterface|MockObject
     */
    private $stockItemInterfaceMock;

    /**
     * @var int
     */
    private $websiteId = 0;

    protected function setUp(): void
    {
        $this->stockResourceMock = $this->getMockBuilder(ResourceStock::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->stockRegistryProviderMock = $this->getMockBuilder(StockRegistryProviderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->stockStateMock = $this->getMockBuilder(StockState::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->stockConfigurationMock = $this->getMockBuilder(StockConfigurationInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->productRepositoryMock = $this->createMock(ProductRepositoryInterface::class);
        $this->qtyCounterMock = $this->getMockBuilder(QtyCounterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->stockRegistryStorageMock = $this->getMockBuilder(StockRegistryStorage::class)
            ->disableOriginalConstructor()
            ->getMock();
        // Create anonymous class implementing StockItemInterface with dynamic methods
        $this->stockItemInterfaceMock = new class implements StockItemInterface {
            /** @var bool */
            private $hasAdminArea = false;
            /** @var int */
            private $websiteId = 0;
            /** @var int|null */
            private $itemId = null;

            public function __construct()
            {
            }

            // Dynamic methods from addMethods
            public function hasAdminArea()
            {
                return $this->hasAdminArea;
            }
            public function setHasAdminArea($value)
            {
                $this->hasAdminArea = $value;
                return $this;
            }
            public function getWebsiteId()
            {
                return $this->websiteId;
            }
            public function setWebsiteId($value)
            {
                $this->websiteId = $value;
                return $this;
            }
            public function getItemId()
            {
                return $this->itemId;
            }
            public function setItemId($value)
            {
                $this->itemId = $value;
                return $this;
            }

            // Required StockItemInterface methods
            public function getProductId()
            {
                return null;
            }
            public function setProductId($productId)
            {
                return $this;
            }
            public function getStockId()
            {
                return null;
            }
            public function setStockId($stockId)
            {
                return $this;
            }
            public function getQty()
            {
                return null;
            }
            public function setQty($qty)
            {
                return $this;
            }
            public function getMinQty()
            {
                return null;
            }
            public function setMinQty($minQty)
            {
                return $this;
            }
            public function getMinSaleQty()
            {
                return null;
            }
            public function setMinSaleQty($minSaleQty)
            {
                return $this;
            }
            public function getMaxSaleQty()
            {
                return null;
            }
            public function setMaxSaleQty($maxSaleQty)
            {
                return $this;
            }
            public function getIsInStock()
            {
                return null;
            }
            public function setIsInStock($isInStock)
            {
                return $this;
            }
            public function getLowStockDate()
            {
                return null;
            }
            public function setLowStockDate($lowStockDate)
            {
                return $this;
            }
            public function getNotifyStockQty()
            {
                return null;
            }
            public function setNotifyStockQty($notifyStockQty)
            {
                return $this;
            }
            public function getManageStock()
            {
                return null;
            }
            public function setManageStock($manageStock)
            {
                return $this;
            }
            public function getBackorders()
            {
                return null;
            }
            public function setBackorders($backorders)
            {
                return $this;
            }
            public function getQtyIncrements()
            {
                return null;
            }
            public function setQtyIncrements($qtyIncrements)
            {
                return $this;
            }
            public function getEnableQtyIncrements()
            {
                return null;
            }
            public function setEnableQtyIncrements($enableQtyIncrements)
            {
                return $this;
            }
            public function getIsQtyDecimal()
            {
                return null;
            }
            public function setIsQtyDecimal($isQtyDecimal)
            {
                return $this;
            }
            public function getIsDecimalDivided()
            {
                return null;
            }
            public function setIsDecimalDivided($isDecimalDivided)
            {
                return $this;
            }
            public function getShowDefaultNotificationMessage()
            {
                return null;
            }
            public function setShowDefaultNotificationMessage($showDefaultNotificationMessage)
            {
                return $this;
            }
            public function getUseConfigMinQty()
            {
                return null;
            }
            public function setUseConfigMinQty($useConfigMinQty)
            {
                return $this;
            }
            public function getUseConfigMinSaleQty()
            {
                return null;
            }
            public function setUseConfigMinSaleQty($useConfigMinSaleQty)
            {
                return $this;
            }
            public function getUseConfigMaxSaleQty()
            {
                return null;
            }
            public function setUseConfigMaxSaleQty($useConfigMaxSaleQty)
            {
                return $this;
            }
            public function getUseConfigBackorders()
            {
                return null;
            }
            public function setUseConfigBackorders($useConfigBackorders)
            {
                return $this;
            }
            public function getUseConfigNotifyStockQty()
            {
                return null;
            }
            public function setUseConfigNotifyStockQty($useConfigNotifyStockQty)
            {
                return $this;
            }
            public function getUseConfigQtyIncrements()
            {
                return null;
            }
            public function setUseConfigQtyIncrements($useConfigQtyIncrements)
            {
                return $this;
            }
            public function getUseConfigEnableQtyInc()
            {
                return null;
            }
            public function setUseConfigEnableQtyInc($useConfigEnableQtyInc)
            {
                return $this;
            }
            public function getUseConfigManageStock()
            {
                return null;
            }
            public function setUseConfigManageStock($useConfigManageStock)
            {
                return $this;
            }
            public function getStockStatusChangedAuto()
            {
                return null;
            }
            public function setStockStatusChangedAuto($stockStatusChangedAuto)
            {
                return $this;
            }
            public function getExtensionAttributes()
            {
                return null;
            }
            public function setExtensionAttributes($extensionAttributes)
            {
                return $this;
            }
            public function getData($key = '', $index = null)
            {
                return null;
            }
            public function setData($key, $value = null)
            {
                return $this;
            }
            public function addData(array $arr)
            {
                return $this;
            }
            public function unsetData($key = null)
            {
                return $this;
            }
            public function hasData($key = '')
            {
                return false;
            }
            public function toArray($arrAttributes = [])
            {
                return [];
            }
            public function toJson($arrAttributes = [])
            {
                return '';
            }
            public function toString($format = '')
            {
                return '';
            }
            public function isEmpty()
            {
                return true;
            }
        };
        $this->stockManagement = $this->getMockBuilder(StockManagement::class)
            ->onlyMethods(['getResource', 'canSubtractQty'])
            ->setConstructorArgs(
                [
                    'stockResource' => $this->stockResourceMock,
                    'stockRegistryProvider' => $this->stockRegistryProviderMock,
                    'stockState' => $this->stockStateMock,
                    'stockConfiguration' => $this->stockConfigurationMock,
                    'productRepository' => $this->productRepositoryMock,
                    'qtyCounter' => $this->qtyCounterMock,
                    'stockRegistryStorage' => $this->stockRegistryStorageMock,
                ]
            )->getMock();

        $this->stockConfigurationMock
            ->expects($this->once())
            ->method('getDefaultScopeId')
            ->willReturn($this->websiteId);
        $this->stockManagement
            ->method('getResource')->willReturn($this->stockResourceMock);
        $this->stockRegistryProviderMock
            ->method('getStockItem')->willReturn($this->stockItemInterfaceMock);
        $this->stockItemInterfaceMock->setHasAdminArea(false);
    }

    /**
     *
     * @param array $items
     * @param array $lockedItems
     * @param bool $canSubtract
     * @param bool $isQty
     * @param bool $verifyStock
     * @return void
     */
    #[DataProvider('productsWithCorrectQtyDataProvider')]
    public function testRegisterProductsSale(
        array $items,
        array $lockedItems,
        bool $canSubtract,
        bool $isQty,
        bool $verifyStock = true
    ) {
        $this->stockResourceMock
            ->expects($this->once())
            ->method('beginTransaction');
        $this->stockResourceMock
            ->expects($this->once())
            ->method('lockProductsStock')
            ->willReturn([$lockedItems]);
        $this->stockItemInterfaceMock->setItemId($lockedItems['product_id']);
        $this->stockManagement
            ->method('canSubtractQty')->willReturn($canSubtract);
        $this->stockConfigurationMock
            ->method('isQty')->willReturn($isQty);
        $this->stockItemInterfaceMock->setWebsiteId($this->websiteId);
        $this->stockStateMock
            ->method('checkQty')->willReturn(true);
        $this->stockStateMock
            ->method('verifyStock')->willReturn($verifyStock);
        $this->stockStateMock
            ->method('verifyNotification')->willReturn(false);
        $this->stockResourceMock
            ->expects($this->once())
            ->method('commit');

        $this->stockManagement->registerProductsSale($items, $this->websiteId);
    }

    /**
     *
     * @param array $items
     * @param array $lockedItems
     * @return void
     */
    #[DataProvider('productsWithIncorrectQtyDataProvider')]
    public function testRegisterProductsSaleException(array $items, array $lockedItems)
    {
        $this->expectException('Magento\CatalogInventory\Model\StockStateException');
        $this->expectExceptionMessage('Some of the products are out of stock.');
        $this->stockResourceMock
            ->expects($this->once())
            ->method('beginTransaction');
        $this->stockResourceMock
            ->expects($this->once())
            ->method('lockProductsStock')
            ->willReturn([$lockedItems]);
        $this->stockItemInterfaceMock->setItemId($lockedItems['product_id']);
        $this->stockManagement
            ->method('canSubtractQty')->willReturn(true);
        $this->stockConfigurationMock
            ->method('isQty')->willReturn(true);
        $this->stockStateMock
            ->method('checkQty')->willReturn(false);
        $this->stockResourceMock
            ->expects($this->once())
            ->method('commit');

        $this->stockManagement->registerProductsSale($items, $this->websiteId);
    }

    /**
     * @return array
     */
    public static function productsWithCorrectQtyDataProvider(): array
    {
        return [
            [
                [1 => 3],
                [
                    'product_id' => 1,
                    'qty' => 10,
                    'type_id' => 'simple',
                ],
                false,
                false,
            ],
            [
                [2 => 4],
                [
                    'product_id' => 2,
                    'qty' => 10,
                    'type_id' => 'simple',
                ],
                true,
                true,
            ],
            [
                [3 => 5],
                [
                    'product_id' => 3,
                    'qty' => 10,
                    'type_id' => 'simple',
                ],
                true,
                true,
                false,
            ],
        ];
    }

    /**
     * @return array
     */
    public static function productsWithIncorrectQtyDataProvider(): array
    {
        return [
            [
                [2 => 4],
                [
                    'product_id' => 2,
                    'qty' => 2,
                    'type_id' => 'simple',
                ],
            ],
        ];
    }
}
