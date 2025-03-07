<?php
declare(strict_types=1);

namespace Magento\CatalogInventory\Test\Unit\Model\Product;

use Magento\CatalogInventory\Model\Product\QuantityValidator;
use Magento\InventoryConfigurationApi\Api\GetStockItemConfigurationInterface;
use Magento\InventoryConfigurationApi\Api\Data\StockItemConfigurationInterface;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;
use Magento\InventoryApi\Api\Data\StockInterface;
use Magento\InventorySalesApi\Api\StockResolverInterface;
use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Magento\CatalogInventory\Model\Product\QuantityValidator
 */
class QuantityValidatorTest extends TestCase
{
    private const WEBSITE_ID = 1;
    private const WEBSITE_CODE = 'base';
    private const STOCK_ID = 1;
    private const TEST_SKU = 'TEST-SKU';

    /**
     * @var QuantityValidator
     */
    private $quantityValidator;

    /**
     * @var GetStockItemConfigurationInterface|MockObject
     */
    private $getStockItemConfiguration;

    /**
     * @var StoreManagerInterface|MockObject
     */
    private $storeManager;

    /**
     * @var StockResolverInterface|MockObject
     */
    private $stockResolver;

    protected function setUp(): void
    {
        $this->getStockItemConfiguration = $this->createMock(GetStockItemConfigurationInterface::class);
        $this->storeManager = $this->createMock(StoreManagerInterface::class);
        $this->stockResolver = $this->createMock(StockResolverInterface::class);

        $this->quantityValidator = new QuantityValidator(
            $this->getStockItemConfiguration,
            $this->storeManager,
            $this->stockResolver
        );
    }

    public function testGetDataWithValidators(): void
    {
        $website = $this->createMock(WebsiteInterface::class);
        $website->method('getCode')->willReturn(self::WEBSITE_CODE);

        $stock = $this->createMock(StockInterface::class);
        $stock->method('getStockId')->willReturn(self::STOCK_ID);

        $stockItemConfiguration = $this->createMock(StockItemConfigurationInterface::class);
        $stockItemConfiguration->method('getMinSaleQty')->willReturn(2.0);
        $stockItemConfiguration->method('getMaxSaleQty')->willReturn(10.0);
        $stockItemConfiguration->method('getQtyIncrements')->willReturn(2.0);

        // Set expectations
        $this->storeManager->expects($this->once())
            ->method('getWebsite')
            ->with(self::WEBSITE_ID)
            ->willReturn($website);

        $this->stockResolver->expects($this->once())
            ->method('execute')
            ->with(SalesChannelInterface::TYPE_WEBSITE, self::WEBSITE_CODE)
            ->willReturn($stock);

        $this->getStockItemConfiguration->expects($this->once())
            ->method('execute')
            ->with(self::TEST_SKU, self::STOCK_ID)
            ->willReturn($stockItemConfiguration);

        $expected = [
            'validate-item-quantity' => [
                'minAllowed' => 2,
                'maxAllowed' => 10,
                'qtyIncrements' => 2.0
            ]
        ];

        $result = $this->quantityValidator->getData(self::TEST_SKU, self::WEBSITE_ID);
        $this->assertEquals($expected, $result);
    }
}
