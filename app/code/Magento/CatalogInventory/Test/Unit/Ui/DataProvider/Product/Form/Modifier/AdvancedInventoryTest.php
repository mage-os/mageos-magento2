<?php
/**
 * Copyright 2016 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogInventory\Test\Unit\Ui\DataProvider\Product\Form\Modifier;

use PHPUnit\Framework\Attributes\DataProvider as DataProviderAttribute;
use Magento\Catalog\Test\Unit\Ui\DataProvider\Product\Form\Modifier\AbstractModifierTestCase;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Api\StockConfigurationInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Ui\DataProvider\Product\Form\Modifier\AdvancedInventory;
use Magento\Framework\Serialize\JsonValidator;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\Store;
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
        
        // Create anonymous class for StockItemInterface with getData method
        $this->stockItemMock = new class implements StockItemInterface {
            private $data = [];
            private $manageStock = null;
            private $qty = null;
            private $minQty = null;
            private $minSaleQty = null;
            private $maxSaleQty = null;
            private $isQtyDecimal = null;
            private $isDecimalDivided = null;
            private $backorders = null;
            private $notifyStockQty = null;
            private $enableQtyIncrements = null;
            private $qtyIncrements = null;
            private $isInStock = null;
            private $itemId = null;
            private $productId = null;
            private $stockId = null;
            private $isInStockValue = null;
            private $useConfigMinQty = null;
            private $useConfigMinSaleQty = null;
            private $useConfigMaxSaleQty = null;
            private $useConfigBackorders = null;
            private $useConfigNotifyStockQty = null;
            private $useConfigQtyIncrements = null;
            private $useConfigEnableQtyInc = null;
            private $useConfigManageStock = null;
            private $lowStockDate = null;
            private $stockStatusChangedAuto = null;
            private $extensionAttributes = null;
            private $showDefaultNotificationMessage = null;

            public function __construct() {}

            public function getData($key = '', $index = null) {
                return $this->data;
            }

            public function setData($key, $value = null) {
                $this->data = $value;
                return $this;
            }

            public function getManageStock() {
                return $this->manageStock;
            }

            public function setManageStock($manageStock) {
                $this->manageStock = $manageStock;
                return $this;
            }

            public function getQty() {
                return $this->qty;
            }

            public function setQty($qty) {
                $this->qty = $qty;
                return $this;
            }

            public function getMinQty() {
                return $this->minQty;
            }

            public function setMinQty($minQty) {
                $this->minQty = $minQty;
                return $this;
            }

            public function getMinSaleQty() {
                return $this->minSaleQty;
            }

            public function setMinSaleQty($minSaleQty) {
                $this->minSaleQty = $minSaleQty;
                return $this;
            }

            public function getMaxSaleQty() {
                return $this->maxSaleQty;
            }

            public function setMaxSaleQty($maxSaleQty) {
                $this->maxSaleQty = $maxSaleQty;
                return $this;
            }

            public function getIsQtyDecimal() {
                return $this->isQtyDecimal;
            }

            public function setIsQtyDecimal($isQtyDecimal) {
                $this->isQtyDecimal = $isQtyDecimal;
                return $this;
            }

            public function getBackorders() {
                return $this->backorders;
            }

            public function setBackorders($backorders) {
                $this->backorders = $backorders;
                return $this;
            }

            public function getNotifyStockQty() {
                return $this->notifyStockQty;
            }

            public function setNotifyStockQty($notifyStockQty) {
                $this->notifyStockQty = $notifyStockQty;
                return $this;
            }

            public function getEnableQtyIncrements() {
                return $this->enableQtyIncrements;
            }

            public function setEnableQtyIncrements($enableQtyIncrements) {
                $this->enableQtyIncrements = $enableQtyIncrements;
                return $this;
            }

            public function getQtyIncrements() {
                return $this->qtyIncrements;
            }

            public function setQtyIncrements($qtyIncrements) {
                $this->qtyIncrements = $qtyIncrements;
                return $this;
            }

            public function getIsInStock() {
                return $this->isInStock;
            }

            public function setIsInStock($isInStock) {
                $this->isInStock = $isInStock;
                return $this;
            }

            public function getItemId() {
                return $this->itemId;
            }

            public function setItemId($itemId) {
                $this->itemId = $itemId;
                return $this;
            }

            public function getProductId() {
                return $this->productId;
            }

            public function setProductId($productId) {
                $this->productId = $productId;
                return $this;
            }

            public function getStockId() {
                return $this->stockId;
            }

            public function setStockId($stockId) {
                $this->stockId = $stockId;
                return $this;
            }

            public function getIsInStockValue() {
                return $this->isInStockValue;
            }

            public function setIsInStockValue($isInStockValue) {
                $this->isInStockValue = $isInStockValue;
                return $this;
            }

            public function getUseConfigMinQty() {
                return $this->useConfigMinQty;
            }

            public function setUseConfigMinQty($useConfigMinQty) {
                $this->useConfigMinQty = $useConfigMinQty;
                return $this;
            }

            public function getMinQtyAllowedInShoppingCart() {
                return $this->minQty;
            }

            public function setMinQtyAllowedInShoppingCart($minQty) {
                $this->minQty = $minQty;
                return $this;
            }

            public function getUseConfigMinSaleQty() {
                return $this->useConfigMinSaleQty;
            }

            public function setUseConfigMinSaleQty($useConfigMinSaleQty) {
                $this->useConfigMinSaleQty = $useConfigMinSaleQty;
                return $this;
            }

            public function getUseConfigMaxSaleQty() {
                return $this->useConfigMaxSaleQty;
            }

            public function setUseConfigMaxSaleQty($useConfigMaxSaleQty) {
                $this->useConfigMaxSaleQty = $useConfigMaxSaleQty;
                return $this;
            }

            public function getUseConfigBackorders() {
                return $this->useConfigBackorders;
            }

            public function setUseConfigBackorders($useConfigBackorders) {
                $this->useConfigBackorders = $useConfigBackorders;
                return $this;
            }

            public function getUseConfigNotifyStockQty() {
                return $this->useConfigNotifyStockQty;
            }

            public function setUseConfigNotifyStockQty($useConfigNotifyStockQty) {
                $this->useConfigNotifyStockQty = $useConfigNotifyStockQty;
                return $this;
            }

            public function getUseConfigQtyIncrements() {
                return $this->useConfigQtyIncrements;
            }

            public function setUseConfigQtyIncrements($useConfigQtyIncrements) {
                $this->useConfigQtyIncrements = $useConfigQtyIncrements;
                return $this;
            }

            public function getUseConfigEnableQtyInc() {
                return $this->useConfigEnableQtyInc;
            }

            public function setUseConfigEnableQtyInc($useConfigEnableQtyInc) {
                $this->useConfigEnableQtyInc = $useConfigEnableQtyInc;
                return $this;
            }

            public function getUseConfigManageStock() {
                return $this->useConfigManageStock;
            }

            public function setUseConfigManageStock($useConfigManageStock) {
                $this->useConfigManageStock = $useConfigManageStock;
                return $this;
            }

            public function getLowStockDate() {
                return $this->lowStockDate;
            }

            public function setLowStockDate($lowStockDate) {
                $this->lowStockDate = $lowStockDate;
                return $this;
            }

            public function getStockStatusChangedAuto() {
                return $this->stockStatusChangedAuto;
            }

            public function setStockStatusChangedAuto($stockStatusChangedAuto) {
                $this->stockStatusChangedAuto = $stockStatusChangedAuto;
                return $this;
            }

            public function getExtensionAttributes() {
                return $this->extensionAttributes;
            }

            public function setExtensionAttributes($extensionAttributes) {
                $this->extensionAttributes = $extensionAttributes;
                return $this;
            }

            public function getShowDefaultNotificationMessage() {
                return $this->showDefaultNotificationMessage;
            }

            public function setShowDefaultNotificationMessage($showDefaultNotificationMessage) {
                $this->showDefaultNotificationMessage = $showDefaultNotificationMessage;
                return $this;
            }

            public function getIsDecimalDivided() {
                return $this->isDecimalDivided;
            }

            public function setIsDecimalDivided($isDecimalDivided) {
                $this->isDecimalDivided = $isDecimalDivided;
                return $this;
            }
        };

        $this->stockConfigurationMock = $this->createMock(StockConfigurationInterface::class);

        $this->stockRegistryMock->method('getStockItem')->willReturn($this->stockItemMock);
        // Use setter instead of expects for the anonymous class
        $this->productMock->setStore($this->storeMock);
        $this->serializerMock = $this->createMock(Json::class);
        $this->jsonValidatorMock = $this->createMock(JsonValidator::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function createModel()
    {
        // Direct instantiation instead of ObjectManagerHelper
        return new AdvancedInventory(
            $this->locatorMock,
            $this->stockRegistryMock,
            $this->arrayManagerMock,
            $this->stockConfigurationMock,
            $this->serializerMock,
            $this->jsonValidatorMock
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
    #[DataProviderAttribute('modifyDataProvider')]
    public function testModifyData(
        $modelId,
        $someData,
        $defaultConfigValue,
        $unserializedValue = null,
        $serializeCalledNum = 0,
        $isValidCalledNum = 0
    ) {
        // Use setters instead of expects for the anonymous class
        $this->productMock->setId($modelId);

        $this->stockConfigurationMock->method('getDefaultConfigValue')->willReturn($defaultConfigValue);

        $this->serializerMock->expects($this->exactly($serializeCalledNum))
            ->method('unserialize')
            ->with($defaultConfigValue)
            ->willReturn($unserializedValue);

        $this->jsonValidatorMock->expects($this->exactly($isValidCalledNum))
            ->method('isValid')
            ->willReturn(true);

        // Use setters instead of expects for the anonymous class
        $this->stockItemMock->setData(['someData']);
        $this->stockItemMock->setManageStock($someData);
        $this->stockItemMock->setQty($someData);
        $this->stockItemMock->setMinQty($someData);
        $this->stockItemMock->setMinSaleQty($someData);
        $this->stockItemMock->setMaxSaleQty($someData);
        $this->stockItemMock->setIsQtyDecimal($someData);
        $this->stockItemMock->setIsDecimalDivided($someData);
        $this->stockItemMock->setBackorders($someData);
        $this->stockItemMock->setNotifyStockQty($someData);
        $this->stockItemMock->setEnableQtyIncrements($someData);
        $this->stockItemMock->setQtyIncrements($someData);
        $this->stockItemMock->setIsInStock($someData);

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
