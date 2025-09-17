<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogInventory\Test\Unit\Api;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\CatalogInventory\Api\Data\StockInterface;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Api\Data\StockStatusInterface;
use Magento\CatalogInventory\Api\StockItemRepositoryInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Model\Spi\StockRegistryProviderInterface;
use Magento\CatalogInventory\Model\StockRegistry;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class StockRegistryTest extends TestCase
{
    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /**
     * @var StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * @var StockRegistryProviderInterface|MockObject
     */
    protected $stockRegistryProvider;

    /**
     * @var StockInterface|MockObject
     */
    protected $stock;

    /**
     * @var StockItemInterface|MockObject
     */
    protected $stockItem;

    /**
     * @var StockStatusInterface|MockObject
     */
    protected $stockStatus;

    /**
     * @var ProductFactory|MockObject
     */
    protected $productFactory;

    /**
     * @var StockItemRepositoryInterface|MockObject
     */
    protected $stockItemRepository;

    /**
     * @var Product|MockObject
     */
    protected $product;

    private const PRODUCT_ID = 111;
    private const PRODUCT_SKU = 'simple';
    private const WEBSITE_ID = 111;

    protected function setUp(): void
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $this->product = $this->createPartialMock(Product::class, ['__wakeup', 'getIdBySku']);
        $this->product->method('getIdBySku')->willReturn(self::PRODUCT_ID);
        //getIdBySku
        $this->productFactory = $this->createPartialMock(ProductFactory::class, ['create']);
        $this->productFactory->method('create')->willReturn($this->product);

        $this->stock = $this->createMock(
            StockInterface::class,
            ['__wakeup'],
            '',
            false
        );
        $this->stockItem = new class implements StockItemInterface {
            /** @var array */
            private $data = [];
            /** @var int|null */
            private $itemId = null;
            /** @var int|null */
            private $productId = null;
            /** @var int|null */
            private $stockId = null;
            /** @var float|null */
            private $qty = null;
            /** @var bool|null */
            private $isInStock = null;
            /** @var bool|null */
            private $isQtyDecimal = null;
            /** @var bool|null */
            private $showDefaultNotificationMessage = null;
            /** @var bool|null */
            private $useConfigMinQty = null;
            /** @var float|null */
            private $minQty = null;
            /** @var bool|null */
            private $useConfigMinSaleQty = null;
            /** @var float|null */
            private $minSaleQty = null;
            /** @var bool|null */
            private $useConfigMaxSaleQty = null;
            /** @var float|null */
            private $maxSaleQty = null;
            /** @var bool|null */
            private $useConfigBackorders = null;
            /** @var int|null */
            private $backorders = null;
            /** @var bool|null */
            private $useConfigNotifyStockQty = null;
            /** @var float|null */
            private $notifyStockQty = null;
            /** @var bool|null */
            private $useConfigQtyIncrements = null;
            /** @var float|null */
            private $qtyIncrements = null;
            /** @var bool|null */
            private $useConfigEnableQtyInc = null;
            /** @var bool|null */
            private $enableQtyIncrements = null;
            /** @var bool|null */
            private $useConfigManageStock = null;
            /** @var bool|null */
            private $manageStock = null;
            /** @var string|null */
            private $lowStockDate = null;
            /** @var bool|null */
            private $isDecimalDivided = null;
            /** @var bool|null */
            private $stockStatusChangedAuto = null;
            /** @var mixed */
            private $extensionAttributes = null;
            /** @var int|null */
            private $websiteId = null;

            public function __construct()
            {
            }

            // DataObject methods
            public function getData($key = '', $index = null)
            {
                if ('' === $key) {
                    return $this->data;
                }
                return $this->data[$key] ?? null;
            }
            public function setData($key, $value = null)
            {
                if (is_array($key)) {
                    $this->data = $key;
                } else {
                    $this->data[$key] = $value;
                }
                return $this;
            }
            public function addData(array $arr)
            {
                foreach ($arr as $index => $value) {
                    $this->setData($index, $value);
                }
                return $this;
            }
            public function unsetData($key = null)
            {
                if ($key === null) {
                    $this->data = [];
                } elseif (is_string($key)) {
                    unset($this->data[$key]);
                }
                return $this;
            }
            public function hasData($key = '')
            {
                return isset($this->data[$key]);
            }
            public function toArray(array $keys = [])
            {
                return $this->data;
            }
            public function toJson(array $keys = [])
            {
                return json_encode($this->data);
            }
            public function toString($format = '')
            {
                return json_encode($this->data);
            }
            public function isEmpty()
            {
                return empty($this->data);
            }

            // StockItemInterface methods
            public function getItemId()
            {
                return $this->itemId;
            }
            public function setItemId($itemId)
            {
                $this->itemId = $itemId;
                return $this;
            }
            public function getProductId()
            {
                return $this->productId;
            }
            public function setProductId($productId)
            {
                $this->productId = $productId;
                return $this;
            }
            public function getStockId()
            {
                return $this->stockId;
            }
            public function setStockId($stockId)
            {
                $this->stockId = $stockId;
                return $this;
            }
            public function getQty()
            {
                return $this->qty;
            }
            public function setQty($qty)
            {
                $this->qty = $qty;
                return $this;
            }
            public function getIsInStock()
            {
                return $this->isInStock;
            }
            public function setIsInStock($isInStock)
            {
                $this->isInStock = $isInStock;
                return $this;
            }
            public function getIsQtyDecimal()
            {
                return $this->isQtyDecimal;
            }
            public function setIsQtyDecimal($isQtyDecimal)
            {
                $this->isQtyDecimal = $isQtyDecimal;
                return $this;
            }
            public function getShowDefaultNotificationMessage()
            {
                return $this->showDefaultNotificationMessage;
            }
            public function getUseConfigMinQty()
            {
                return $this->useConfigMinQty;
            }
            public function setUseConfigMinQty($useConfigMinQty)
            {
                $this->useConfigMinQty = $useConfigMinQty;
                return $this;
            }
            public function getMinQty()
            {
                return $this->minQty;
            }
            public function setMinQty($minQty)
            {
                $this->minQty = $minQty;
                return $this;
            }
            public function getUseConfigMinSaleQty()
            {
                return $this->useConfigMinSaleQty;
            }
            public function setUseConfigMinSaleQty($useConfigMinSaleQty)
            {
                $this->useConfigMinSaleQty = $useConfigMinSaleQty;
                return $this;
            }
            public function getMinSaleQty()
            {
                return $this->minSaleQty;
            }
            public function setMinSaleQty($minSaleQty)
            {
                $this->minSaleQty = $minSaleQty;
                return $this;
            }
            public function getUseConfigMaxSaleQty()
            {
                return $this->useConfigMaxSaleQty;
            }
            public function setUseConfigMaxSaleQty($useConfigMaxSaleQty)
            {
                $this->useConfigMaxSaleQty = $useConfigMaxSaleQty;
                return $this;
            }
            public function getMaxSaleQty()
            {
                return $this->maxSaleQty;
            }
            public function setMaxSaleQty($maxSaleQty)
            {
                $this->maxSaleQty = $maxSaleQty;
                return $this;
            }
            public function getUseConfigBackorders()
            {
                return $this->useConfigBackorders;
            }
            public function setUseConfigBackorders($useConfigBackorders)
            {
                $this->useConfigBackorders = $useConfigBackorders;
                return $this;
            }
            public function getBackorders()
            {
                return $this->backorders;
            }
            public function setBackorders($backOrders)
            {
                $this->backorders = $backOrders;
                return $this;
            }
            public function getUseConfigNotifyStockQty()
            {
                return $this->useConfigNotifyStockQty;
            }
            public function setUseConfigNotifyStockQty($useConfigNotifyStockQty)
            {
                $this->useConfigNotifyStockQty = $useConfigNotifyStockQty;
                return $this;
            }
            public function getNotifyStockQty()
            {
                return $this->notifyStockQty;
            }
            public function setNotifyStockQty($notifyStockQty)
            {
                $this->notifyStockQty = $notifyStockQty;
                return $this;
            }
            public function getUseConfigQtyIncrements()
            {
                return $this->useConfigQtyIncrements;
            }
            public function setUseConfigQtyIncrements($useConfigQtyIncrements)
            {
                $this->useConfigQtyIncrements = $useConfigQtyIncrements;
                return $this;
            }
            public function getQtyIncrements()
            {
                return $this->qtyIncrements;
            }
            public function setQtyIncrements($qtyIncrements)
            {
                $this->qtyIncrements = $qtyIncrements;
                return $this;
            }
            public function getUseConfigEnableQtyInc()
            {
                return $this->useConfigEnableQtyInc;
            }
            public function setUseConfigEnableQtyInc($useConfigEnableQtyInc)
            {
                $this->useConfigEnableQtyInc = $useConfigEnableQtyInc;
                return $this;
            }
            public function getEnableQtyIncrements()
            {
                return $this->enableQtyIncrements;
            }
            public function setEnableQtyIncrements($enableQtyIncrements)
            {
                $this->enableQtyIncrements = $enableQtyIncrements;
                return $this;
            }
            public function getUseConfigManageStock()
            {
                return $this->useConfigManageStock;
            }
            public function setUseConfigManageStock($useConfigManageStock)
            {
                $this->useConfigManageStock = $useConfigManageStock;
                return $this;
            }
            public function getManageStock()
            {
                return $this->manageStock;
            }
            public function setManageStock($manageStock)
            {
                $this->manageStock = $manageStock;
                return $this;
            }
            public function getLowStockDate()
            {
                return $this->lowStockDate;
            }
            public function setLowStockDate($lowStockDate)
            {
                $this->lowStockDate = $lowStockDate;
                return $this;
            }
            public function getIsDecimalDivided()
            {
                return $this->isDecimalDivided;
            }
            public function setIsDecimalDivided($isDecimalDivided)
            {
                $this->isDecimalDivided = $isDecimalDivided;
                return $this;
            }
            public function getStockStatusChangedAuto()
            {
                return $this->stockStatusChangedAuto;
            }
            public function setStockStatusChangedAuto($stockStatusChangedAuto)
            {
                $this->stockStatusChangedAuto = $stockStatusChangedAuto;
                return $this;
            }
            public function getExtensionAttributes()
            {
                return $this->extensionAttributes;
            }
            public function setExtensionAttributes($extensionAttributes)
            {
                $this->extensionAttributes = $extensionAttributes;
                return $this;
            }
            public function getWebsiteId()
            {
                return $this->websiteId;
            }
            public function setWebsiteId($websiteId)
            {
                $this->websiteId = $websiteId;
                return $this;
            }
        };
        $this->stockStatus = $this->createMock(
            StockStatusInterface::class,
            ['__wakeup'],
            '',
            false
        );

        $this->stockRegistryProvider = $this->createMock(
            StockRegistryProviderInterface::class,
            ['getStock', 'getStockItem', 'getStockStatus'],
            '',
            false
        );
        $this->stockRegistryProvider->method('getStock')->willReturn($this->stock);
        $this->stockRegistryProvider->method('getStockItem')->willReturn($this->stockItem);
        $this->stockRegistryProvider->method('getStockStatus')->willReturn($this->stockStatus);

        $this->stockItemRepository = $this->createMock(
            StockItemRepositoryInterface::class,
            ['save'],
            '',
            false
        );
        $this->stockItemRepository->method('save')->willReturn($this->stockItem);

        $this->stockRegistry = $this->objectManagerHelper->getObject(
            StockRegistry::class,
            [
                'stockRegistryProvider' => $this->stockRegistryProvider,
                'productFactory' => $this->productFactory,
                'stockItemRepository' => $this->stockItemRepository
            ]
        );
    }

    protected function tearDown(): void
    {
        $this->stockRegistry = null;
    }

    public function testGetStock()
    {
        $this->assertEquals($this->stock, $this->stockRegistry->getStock(self::WEBSITE_ID));
    }

    public function testGetStockItem()
    {
        $this->assertEquals($this->stockItem, $this->stockRegistry->getStockItem(self::PRODUCT_ID, self::WEBSITE_ID));
    }

    public function testGetStockItemBySku()
    {
        $this->assertEquals(
            $this->stockItem,
            $this->stockRegistry->getStockItemBySku(self::PRODUCT_SKU, self::WEBSITE_ID)
        );
    }

    public function testGetStockStatus()
    {
        $this->assertEquals(
            $this->stockStatus,
            $this->stockRegistry->getStockStatus(self::PRODUCT_ID, self::WEBSITE_ID)
        );
    }

    public function testGetStockStatusBySku()
    {
        $this->assertEquals(
            $this->stockStatus,
            $this->stockRegistry->getStockStatus(self::PRODUCT_ID, self::WEBSITE_ID)
        );
    }

    public function testUpdateStockItemBySku()
    {
        $itemId = 1;
        $this->stockItem->setProductId(self::PRODUCT_ID);
        $this->stockItem->setData([]);
        $this->stockItem->addData([]);
        $this->stockItem->setItemId($itemId);
        $this->assertEquals(
            $itemId,
            $this->stockRegistry->updateStockItemBySku(self::PRODUCT_SKU, $this->stockItem)
        );
    }
}
