<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogInventory\Test\Unit\Block;

use PHPUnit\Framework\Attributes\DataProvider;
use Magento\Catalog\Model\Product;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Block\Qtyincrements;
use Magento\Framework\Registry;
use Magento\Store\Model\Store;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for Qtyincrements block
 */
class QtyincrementsTest extends TestCase
{
    /**
     * @var Qtyincrements
     */
    protected $block;

    /**
     * @var Registry|MockObject
     */
    protected $registryMock;

    /**
     * @var StockItemInterface|MockObject
     */
    protected $stockItem;

    /**
     * @var StockRegistryInterface|MockObject
     */
    protected $stockRegistry;

    protected function setUp(): void
    {
        // Create minimal ObjectManager mock
        $objectManagerMock = $this->createMock(\Magento\Framework\ObjectManagerInterface::class);
        \Magento\Framework\App\ObjectManager::setInstance($objectManagerMock);
        
        // Create Context mock
        $contextMock = $this->createMock(\Magento\Framework\View\Element\Template\Context::class);
        
        $this->registryMock = $this->createMock(Registry::class);
        
        // Create anonymous class for StockItemInterface
        $this->stockItem = new class implements StockItemInterface {
            private $qtyIncrements = null;
            private $itemId = null;
            private $productId = null;
            private $stockId = null;
            private $qty = null;
            private $isInStock = null;
            private $isQtyDecimal = null;
            private $showDefaultNotificationMessage = null;
            private $useConfigMinQty = null;
            private $minQty = null;
            private $useConfigMinSaleQty = null;
            private $minSaleQty = null;
            private $useConfigMaxSaleQty = null;
            private $maxSaleQty = null;
            private $useConfigBackorders = null;
            private $backorders = null;
            private $useConfigNotifyStockQty = null;
            private $notifyStockQty = null;
            private $useConfigQtyIncrements = null;
            private $useConfigEnableQtyInc = null;
            private $enableQtyIncrements = null;
            private $useConfigManageStock = null;
            private $manageStock = null;
            private $lowStockDate = null;
            private $isDecimalDivided = null;
            private $stockStatusChangedAuto = null;
            private $extensionAttributes = null;
            private $data = [];

            public function __construct() {}

            // DataObject methods
            public function getData($key = '', $index = null) { 
                if ('' === $key) {
                    return $this->data;
                }
                return $this->data[$key] ?? null;
            }
            public function setData($key, $value = null) { 
                if (is_array($key)) {
                    $this->data = $key;
                } else {
                    $this->data[$key] = $value;
                }
                return $this;
            }
            public function addData(array $arr) { 
                foreach ($arr as $index => $value) {
                    $this->setData($index, $value);
                }
                return $this;
            }
            public function unsetData($key = null) { 
                if ($key === null) {
                    $this->data = [];
                } elseif (is_string($key)) {
                    unset($this->data[$key]);
                }
                return $this;
            }
            public function hasData($key = '') { return isset($this->data[$key]); }
            public function toArray(array $keys = []) { return $this->data; }
            public function toJson(array $keys = []) { return json_encode($this->data); }
            public function toString($format = '') { return json_encode($this->data); }
            public function isEmpty() { return empty($this->data); }

            // StockItemInterface methods
            public function getItemId() { return $this->itemId; }
            public function setItemId($itemId) { $this->itemId = $itemId; return $this; }
            public function getProductId() { return $this->productId; }
            public function setProductId($productId) { $this->productId = $productId; return $this; }
            public function getStockId() { return $this->stockId; }
            public function setStockId($stockId) { $this->stockId = $stockId; return $this; }
            public function getQty() { return $this->qty; }
            public function setQty($qty) { $this->qty = $qty; return $this; }
            public function getIsInStock() { return $this->isInStock; }
            public function setIsInStock($isInStock) { $this->isInStock = $isInStock; return $this; }
            public function getIsQtyDecimal() { return $this->isQtyDecimal; }
            public function setIsQtyDecimal($isQtyDecimal) { $this->isQtyDecimal = $isQtyDecimal; return $this; }
            public function getShowDefaultNotificationMessage() { return $this->showDefaultNotificationMessage; }
            public function getUseConfigMinQty() { return $this->useConfigMinQty; }
            public function setUseConfigMinQty($useConfigMinQty) { $this->useConfigMinQty = $useConfigMinQty; return $this; }
            public function getMinQty() { return $this->minQty; }
            public function setMinQty($minQty) { $this->minQty = $minQty; return $this; }
            public function getUseConfigMinSaleQty() { return $this->useConfigMinSaleQty; }
            public function setUseConfigMinSaleQty($useConfigMinSaleQty) { $this->useConfigMinSaleQty = $useConfigMinSaleQty; return $this; }
            public function getMinSaleQty() { return $this->minSaleQty; }
            public function setMinSaleQty($minSaleQty) { $this->minSaleQty = $minSaleQty; return $this; }
            public function getUseConfigMaxSaleQty() { return $this->useConfigMaxSaleQty; }
            public function setUseConfigMaxSaleQty($useConfigMaxSaleQty) { $this->useConfigMaxSaleQty = $useConfigMaxSaleQty; return $this; }
            public function getMaxSaleQty() { return $this->maxSaleQty; }
            public function setMaxSaleQty($maxSaleQty) { $this->maxSaleQty = $maxSaleQty; return $this; }
            public function getUseConfigBackorders() { return $this->useConfigBackorders; }
            public function setUseConfigBackorders($useConfigBackorders) { $this->useConfigBackorders = $useConfigBackorders; return $this; }
            public function getBackorders() { return $this->backorders; }
            public function setBackorders($backOrders) { $this->backorders = $backOrders; return $this; }
            public function getUseConfigNotifyStockQty() { return $this->useConfigNotifyStockQty; }
            public function setUseConfigNotifyStockQty($useConfigNotifyStockQty) { $this->useConfigNotifyStockQty = $useConfigNotifyStockQty; return $this; }
            public function getNotifyStockQty() { return $this->notifyStockQty; }
            public function setNotifyStockQty($notifyStockQty) { $this->notifyStockQty = $notifyStockQty; return $this; }
            public function getUseConfigQtyIncrements() { return $this->useConfigQtyIncrements; }
            public function setUseConfigQtyIncrements($useConfigQtyIncrements) { $this->useConfigQtyIncrements = $useConfigQtyIncrements; return $this; }
            public function getQtyIncrements() { return $this->qtyIncrements; }
            public function setQtyIncrements($qtyIncrements) { $this->qtyIncrements = $qtyIncrements; return $this; }
            public function getUseConfigEnableQtyInc() { return $this->useConfigEnableQtyInc; }
            public function setUseConfigEnableQtyInc($useConfigEnableQtyInc) { $this->useConfigEnableQtyInc = $useConfigEnableQtyInc; return $this; }
            public function getEnableQtyIncrements() { return $this->enableQtyIncrements; }
            public function setEnableQtyIncrements($enableQtyIncrements) { $this->enableQtyIncrements = $enableQtyIncrements; return $this; }
            public function getUseConfigManageStock() { return $this->useConfigManageStock; }
            public function setUseConfigManageStock($useConfigManageStock) { $this->useConfigManageStock = $useConfigManageStock; return $this; }
            public function getManageStock() { return $this->manageStock; }
            public function setManageStock($manageStock) { $this->manageStock = $manageStock; return $this; }
            public function getLowStockDate() { return $this->lowStockDate; }
            public function setLowStockDate($lowStockDate) { $this->lowStockDate = $lowStockDate; return $this; }
            public function getIsDecimalDivided() { return $this->isDecimalDivided; }
            public function setIsDecimalDivided($isDecimalDivided) { $this->isDecimalDivided = $isDecimalDivided; return $this; }
            public function getStockStatusChangedAuto() { return $this->stockStatusChangedAuto; }
            public function setStockStatusChangedAuto($stockStatusChangedAuto) { $this->stockStatusChangedAuto = $stockStatusChangedAuto; return $this; }
            public function getExtensionAttributes() { return $this->extensionAttributes; }
            public function setExtensionAttributes($extensionAttributes) { $this->extensionAttributes = $extensionAttributes; return $this; }
        };
        
        $this->stockRegistry = $this->createMock(StockRegistryInterface::class);
        $this->stockRegistry->method('getStockItem')->willReturn($this->stockItem);

        // Instantiate Qtyincrements block directly with mocks
        $this->block = new Qtyincrements(
            $contextMock,
            $this->registryMock,
            $this->stockRegistry,
            []
        );
    }

    protected function tearDown(): void
    {
        $this->block = null;
    }

    public function testGetIdentities()
    {
        $productTags = ['catalog_product_1'];
        $product = $this->createMock(Product::class);
        $product->expects($this->once())->method('getIdentities')->willReturn($productTags);
        $store = $this->createPartialMock(Store::class, ['getWebsiteId', '__wakeup']);
        $store->method('getWebsiteId')->willReturn(0);
        $product->method('getStore')->willReturn($store);
        $this->registryMock->expects($this->once())
            ->method('registry')
            ->with('current_product')
            ->willReturn($product);
        $this->assertEquals($productTags, $this->block->getIdentities());
    }

    /**
     * @param int $productId
     * @param int $qtyInc
     * @param bool $isSaleable
     * @param int|bool $result
     */
    #[DataProvider('getProductQtyIncrementsDataProvider')]
    public function testGetProductQtyIncrements($productId, $qtyInc, $isSaleable, $result)
    {
        $this->stockItem->setQtyIncrements($qtyInc);

        $product = $this->createMock(Product::class);
        $product->expects($this->once())->method('getId')->willReturn($productId);
        $product->expects($this->once())->method('isSaleable')->willReturn($isSaleable);
        $store = $this->createPartialMock(Store::class, ['getWebsiteId', '__wakeup']);
        $store->method('getWebsiteId')->willReturn(0);
        $product->method('getStore')->willReturn($store);

        $this->registryMock->expects($this->any())
            ->method('registry')
            ->with('current_product')
            ->willReturn($product);

        $this->assertSame($result, $this->block->getProductQtyIncrements());
        // test lazy load
        $this->assertSame($result, $this->block->getProductQtyIncrements());
    }

    /**
     * @return array
     */
    public static function getProductQtyIncrementsDataProvider()
    {
        return [
            [1, 100, true, 100],
            [1, 100, false, false],
        ];
    }
}
