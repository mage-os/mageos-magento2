<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogInventory\Test\Unit\Model;

use PHPUnit\Framework\Attributes\DataProvider;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Test\Unit\Helper\StockItemInterfaceTestHelper;
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
 *
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.TooManyFields)
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
        // Create StockItemInterfaceTestHelper implementing StockItemInterface with dynamic methods
        $this->stockItemInterfaceMock = new StockItemInterfaceTestHelper();
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
