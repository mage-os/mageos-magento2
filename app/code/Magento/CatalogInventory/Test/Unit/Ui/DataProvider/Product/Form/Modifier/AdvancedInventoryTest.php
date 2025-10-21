<?php
/**
 * Copyright 2016 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogInventory\Test\Unit\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Test\Unit\Ui\DataProvider\Product\Form\Modifier\AbstractModifierTestCase;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Api\StockConfigurationInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Test\Unit\Helper\StockItemInterfaceTestHelper;
use Magento\CatalogInventory\Ui\DataProvider\Product\Form\Modifier\AdvancedInventory;
use Magento\Framework\Serialize\JsonValidator;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\Store;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;

class AdvancedInventoryTest extends AbstractModifierTestCase
{
    /**
     * @var StockRegistryInterface|MockObject
     */
    private $stockRegistryMock;

    /**
     * @var StockItemInterface|MockObject
     */
    private $stockItemMock;

    /**
     * @var StockConfigurationInterface|MockObject
     */
    private $stockConfigurationMock;

    /**
     * @var Json|MockObject
     */
    private $serializerMock;

    /**
     * @var JsonValidator|MockObject
     */
    private $jsonValidatorMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stockRegistryMock = $this->createMock(StockRegistryInterface::class);
        $this->storeMock = $this->createMock(Store::class);
        $this->stockItemMock = new StockItemInterfaceTestHelper();
        $this->stockConfigurationMock = $this->createMock(StockConfigurationInterface::class);

        $this->stockRegistryMock->expects($this->any())
            ->method('getStockItem')
            ->willReturn($this->stockItemMock);
        $this->productMock->setStore($this->storeMock);
        $this->serializerMock = $this->createMock(Json::class);
        $this->jsonValidatorMock = $this->createMock(JsonValidator::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function createModel()
    {
        return $this->objectManager->getObject(
            AdvancedInventory::class,
            [
                'locator' => $this->locatorMock,
                'stockRegistry' => $this->stockRegistryMock,
                'stockConfiguration' => $this->stockConfigurationMock,
                'arrayManager' => $this->arrayManagerMock,
                'serializer' => $this->serializerMock,
                'jsonValidator' => $this->jsonValidatorMock,
            ]
        );
    }

    public function testModifyMeta()
    {
        $this->assertNotEmpty($this->getModel()->modifyMeta(['meta_key' => 'meta_value']));
    }

    /**
     * @param int $modelId
     * @param int $someData
     * @param int|string $defaultConfigValue
     * @param null|array $unserializedValue
     * @param int $serializeCalledNum
     * @param int $isValidCalledNum
     */
    #[DataProvider('modifyDataProvider')]
    public function testModifyData(
        $modelId,
        $someData,
        $defaultConfigValue,
        $unserializedValue = null,
        $serializeCalledNum = 0,
        $isValidCalledNum = 0
    ) {
        $this->productMock->setProductId($modelId);

        $this->stockConfigurationMock->expects($this->any())
            ->method('getDefaultConfigValue')
            ->willReturn($defaultConfigValue);

        $this->serializerMock->expects($this->exactly($serializeCalledNum))
            ->method('unserialize')
            ->with($defaultConfigValue)
            ->willReturn($unserializedValue);

        $this->jsonValidatorMock->expects($this->exactly($isValidCalledNum))
            ->method('isValid')
            ->willReturn(true);

        $this->stockItemMock->setData([
            'manage_stock' => $someData,
            'qty' => $someData,
            'min_qty' => $someData,
            'min_sale_qty' => $someData,
            'max_sale_qty' => $someData,
            'is_qty_decimal' => $someData,
            'is_decimal_divided' => $someData,
            'backorders' => $someData,
            'notify_stock_qty' => $someData,
            'enable_qty_increments' => $someData,
            'qty_increments' => $someData,
            'is_in_stock' => $someData
        ]);

        $this->arrayManagerMock->expects($this->once())
            ->method('set')
            ->with('1/product/stock_data/min_qty_allowed_in_shopping_cart')
            ->willReturnArgument(1);

        $this->assertArrayHasKey($modelId, $this->getModel()->modifyData([]));
    }

    /**
     * @return array
     */
    public static function modifyDataProvider()
    {
        return [
            [1, 1, 1],
            [1, 1, '{"36000":2}', ['36000' => 2], 1, 1]
        ];
    }
}
