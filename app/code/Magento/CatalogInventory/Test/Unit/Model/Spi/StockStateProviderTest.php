<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogInventory\Test\Unit\Model\Spi;

use PHPUnit\Framework\Attributes\DataProvider;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Model\Spi\StockStateProviderInterface;
use Magento\CatalogInventory\Model\StockStateProvider;
use Magento\Framework\DataObject;
use Magento\Framework\DataObject\Factory;
use Magento\Framework\Locale\FormatInterface;
use Magento\Framework\Math\Division;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for \Magento\CatalogInventory\Model\StockStateProvider class.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class StockStateProviderTest extends TestCase
{
    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /**
     * @var StockStateProviderInterface
     */
    protected $stockStateProvider;

    /**
     * @var ProductFactory|MockObject
     */
    protected $productFactory;

    /**
     * @var Product|MockObject
     */
    protected $product;

    /**
     * @var Division|MockObject
     */
    protected $mathDivision;

    /**
     * @var FormatInterface|MockObject
     */
    protected $localeFormat;

    /**
     * @var Factory|MockObject
     */
    protected $objectFactory;

    /**
     * @var DataObject|MockObject
     */
    protected $object;

    /**
     * @var float
     */
    protected $qty = 50.5;

    /**
     * @var bool
     */
    protected $qtyCheckApplicable = true;

    /**
     * @var array
     */
    protected $stockAddItemMethods = [
        'getId',
        'getWebsiteId',
        'hasStockQty',
        'setStockQty',
        'getData',
        'getSuppressCheckQtyIncrements',
        'getIsChildItem',
        'getIsSaleable',
        'getOrderedItems',
        'setOrderedItems',
        'getProductName'
    ];

    /**
     * @var array
     */
    protected $stockItemMethods = [
        'getProductId',
        'getStockId',
        'getQty',
        'getIsInStock',
        'getIsQtyDecimal',
        'getShowDefaultNotificationMessage',
        'getUseConfigMinQty',
        'getMinQty',
        'getUseConfigMinSaleQty',
        'getMinSaleQty',
        'getUseConfigMaxSaleQty',
        'getMaxSaleQty',
        'getUseConfigBackorders',
        'getBackorders',
        'getUseConfigNotifyStockQty',
        'getNotifyStockQty',
        'getUseConfigQtyIncrements',
        'getQtyIncrements',
        'getUseConfigEnableQtyInc',
        'getEnableQtyIncrements',
        'getUseConfigManageStock',
        'getManageStock',
        'getLowStockDate',
        'getIsDecimalDivided',
        'getStockStatusChangedAuto',
    ];

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $this->mathDivision = $this->createPartialMock(Division::class, ['getExactDivision']);

        $this->localeFormat = $this->createMock(FormatInterface::class
        );
        $this->localeFormat->method('getNumber')->willReturn($this->qty);

        $this->object = $this->objectManagerHelper->getObject(DataObject::class);
        $this->objectFactory = $this->createPartialMock(Factory::class, ['create']);
        $this->objectFactory->method('create')->willReturn($this->object);

        $this->product = $this->createPartialMock(
            Product::class,
            ['load', 'isComposite', '__wakeup', 'isSaleable']
        );
        $this->productFactory = $this->createPartialMock(ProductFactory::class, ['create']);
        $this->productFactory->method('create')->willReturn($this->product);

        $this->stockStateProvider = $this->objectManagerHelper->getObject(
            StockStateProvider::class,
            [
                'mathDivision' => $this->mathDivision,
                'localeFormat' => $this->localeFormat,
                'objectFactory' => $this->objectFactory,
                'productFactory' => $this->productFactory,
                'qtyCheckApplicable' => $this->qtyCheckApplicable
            ]
        );
    }

    protected function tearDown(): void
    {
        $this->stockStateProvider = null;
    }

    /**
     * @param \Closure $stockItem
     * @param mixed $expectedResult
     */
    #[DataProvider('verifyStockDataProvider')]
    public function testVerifyStock(\Closure $stockItem, $expectedResult)
    {
        $stockItem = $stockItem($this);
        $this->assertEquals(
            $expectedResult,
            $this->stockStateProvider->verifyStock($stockItem)
        );
    }

    /**
     * @param \Closure $stockItem
     * @param mixed $expectedResult
     */
    #[DataProvider('verifyNotificationDataProvider')]
    public function testVerifyNotification(\Closure $stockItem, $expectedResult)
    {
        $stockItem = $stockItem($this);
        $this->assertEquals(
            $expectedResult,
            $this->stockStateProvider->verifyNotification($stockItem)
        );
    }

    /**
     * @param \Closure $stockItem
     * @param mixed $expectedResult
     */
    #[DataProvider('checkQtyDataProvider')]
    public function testCheckQty(\Closure $stockItem, $expectedResult)
    {
        $stockItem = $stockItem($this);
        $this->assertEquals(
            $expectedResult,
            $this->stockStateProvider->checkQty($stockItem, $this->qty)
        );
    }

    /**
     * Check quantity with out-of-stock status but positive or 0 quantity.
     *
     * @param \Closure $stockItem
     * @param mixed $expectedResult
     */
    #[DataProvider('checkQtyWithStockStatusDataProvider')]
    public function testCheckQtyWithPositiveQtyAndOutOfStockstatus(\Closure $stockItem, $expectedResult)
    {
        $stockItem = $stockItem($this);
        $this->assertEquals(
            $expectedResult,
            $this->stockStateProvider->checkQty($stockItem, $this->qty)
        );
    }

    /**
     * @param \Closure $stockItem
     * @param mixed $expectedResult
     */
    #[DataProvider('suggestQtyDataProvider')]
    public function testSuggestQty(\Closure $stockItem, $expectedResult)
    {
        $stockItem = $stockItem($this);
        $this->assertEquals(
            $expectedResult,
            $this->stockStateProvider->suggestQty($stockItem, $this->qty)
        );
    }

    /**
     * @param \Closure $stockItem
     * @param mixed $expectedResult
     */
    #[DataProvider('getStockQtyDataProvider')]
    public function testGetStockQty(\Closure $stockItem, $expectedResult)
    {
        $stockItem = $stockItem($this);
        $this->assertEquals(
            $expectedResult,
            $this->stockStateProvider->getStockQty($stockItem)
        );
    }

    /**
     * @param \Closure $stockItem
     * @param mixed $expectedResult
     */
    #[DataProvider('checkQtyIncrementsDataProvider')]
    public function testCheckQtyIncrements(\Closure $stockItem, $expectedResult)
    {
        $stockItem = $stockItem($this);
        $this->assertEquals(
            $expectedResult,
            $this->stockStateProvider->checkQtyIncrements($stockItem, $this->qty)->getHasError()
        );
    }

    /**
     * @param \Closure $stockItem
     * @param mixed $expectedResult
     */
    #[DataProvider('checkQuoteItemQtyDataProvider')]
    public function testCheckQuoteItemQty(\Closure $stockItem, $expectedResult)
    {
        $stockItem = $stockItem($this);
        $this->assertEquals(
            $expectedResult,
            $this->stockStateProvider->checkQuoteItemQty(
                $stockItem,
                $this->qty,
                $this->qty,
                $this->qty
            )->getHasError()
        );
    }

    /**
     * @return array
     */
    public static function verifyStockDataProvider()
    {
        return self::prepareDataForMethod('verifyStock');
    }

    /**
     * @return array
     */
    public static function verifyNotificationDataProvider()
    {
        return self::prepareDataForMethod('verifyNotification');
    }

    /**
     * @return array
     */
    public static function checkQtyDataProvider()
    {
        return self::prepareDataForMethod('checkQty');
    }

    /**
     * @return array
     */
    public static function checkQtyWithStockStatusDataProvider()
    {
        return self::prepareDataForMethod('checkQty', self::getVariationsForQtyAndStock());
    }

    /**
     * @return array
     */
    public static function suggestQtyDataProvider()
    {
        return self::prepareDataForMethod('suggestQty');
    }

    /**
     * @return array
     */
    public static function getStockQtyDataProvider()
    {
        return self::prepareDataForMethod('getStockQty');
    }

    /**
     * @return array
     */
    public static function checkQtyIncrementsDataProvider()
    {
        return self::prepareDataForMethod('checkQtyIncrements');
    }

    /**
     * @return array
     */
    public static function checkQuoteItemQtyDataProvider()
    {
        return self::prepareDataForMethod('checkQuoteItemQty');
    }

    protected function getStockItemClassMock($variation)
    {
        // Create anonymous class implementing StockItemInterface with dynamic methods
        $stockItem = new class implements StockItemInterface {
            private $suppressCheckQtyIncrements = false;
            private $isSaleable = true;
            private $orderedItems = 0;
            private $productName = '';
            private $isChildItem = false;
            private $hasStockQty = false;
            private $stockQty = null;
            private $values = [];

            public function __construct() {}

            // Dynamic methods from stockAddItemMethods
            public function getSuppressCheckQtyIncrements() { return $this->suppressCheckQtyIncrements; }
            public function setSuppressCheckQtyIncrements($value) { $this->suppressCheckQtyIncrements = $value; return $this; }
            public function getIsSaleable() { return $this->isSaleable; }
            public function setIsSaleable($value) { $this->isSaleable = $value; return $this; }
            public function getOrderedItems() { return $this->orderedItems; }
            public function setOrderedItems($value) { $this->orderedItems = $value; return $this; }
            public function getProductName() { return $this->productName; }
            public function setProductName($value) { $this->productName = $value; return $this; }
            public function getIsChildItem() { return $this->isChildItem; }
            public function setIsChildItem($value) { $this->isChildItem = $value; return $this; }
            public function hasStockQty() { return $this->hasStockQty; }
            public function setHasStockQty($value) { $this->hasStockQty = $value; return $this; }
            public function setStockQty($value) { $this->stockQty = $value; return $this; }
            public function getData($key = '', $index = null) { 
                if ($key === 'stock_qty') return $this->stockQty;
                return isset($this->values[$key]) ? $this->values[$key] : null; 
            }
            public function setData($key, $value = null) { $this->values[$key] = $value; return $this; }

            // Required StockItemInterface methods - these will return values from $values array
            public function getItemId() { return $this->getData('getItemId'); }
            public function setItemId($itemId) { return $this->setData('getItemId', $itemId); }
            public function getProductId() { return $this->getData('getProductId'); }
            public function setProductId($productId) { return $this->setData('getProductId', $productId); }
            public function getWebsiteId() { return $this->getData('getWebsiteId'); }
            public function setWebsiteId($websiteId) { return $this->setData('getWebsiteId', $websiteId); }
            public function getStockId() { return $this->getData('getStockId'); }
            public function setStockId($stockId) { return $this->setData('getStockId', $stockId); }
            public function getQty() { return $this->getData('getQty'); }
            public function setQty($qty) { return $this->setData('getQty', $qty); }
            public function getStockQty() { return $this->stockQty; }
            public function getMinQty() { return $this->getData('getMinQty'); }
            public function setMinQty($minQty) { return $this->setData('getMinQty', $minQty); }
            public function getMinSaleQty() { return $this->getData('getMinSaleQty'); }
            public function setMinSaleQty($minSaleQty) { return $this->setData('getMinSaleQty', $minSaleQty); }
            public function getMaxSaleQty() { return $this->getData('getMaxSaleQty'); }
            public function setMaxSaleQty($maxSaleQty) { return $this->setData('getMaxSaleQty', $maxSaleQty); }
            public function getIsInStock() { return $this->getData('getIsInStock'); }
            public function setIsInStock($isInStock) { return $this->setData('getIsInStock', $isInStock); }
            public function getLowStockDate() { return $this->getData('getLowStockDate'); }
            public function setLowStockDate($lowStockDate) { return $this->setData('getLowStockDate', $lowStockDate); }
            public function getNotifyStockQty() { return $this->getData('getNotifyStockQty'); }
            public function setNotifyStockQty($notifyStockQty) { return $this->setData('getNotifyStockQty', $notifyStockQty); }
            public function getManageStock() { return $this->getData('getManageStock'); }
            public function setManageStock($manageStock) { return $this->setData('getManageStock', $manageStock); }
            public function getBackorders() { return $this->getData('getBackorders'); }
            public function setBackorders($backorders) { return $this->setData('getBackorders', $backorders); }
            public function getQtyIncrements() { return $this->getData('getQtyIncrements'); }
            public function setQtyIncrements($qtyIncrements) { return $this->setData('getQtyIncrements', $qtyIncrements); }
            public function getEnableQtyIncrements() { return $this->getData('getEnableQtyIncrements'); }
            public function setEnableQtyIncrements($enableQtyIncrements) { return $this->setData('getEnableQtyIncrements', $enableQtyIncrements); }
            public function getIsQtyDecimal() { return $this->getData('getIsQtyDecimal'); }
            public function setIsQtyDecimal($isQtyDecimal) { return $this->setData('getIsQtyDecimal', $isQtyDecimal); }
            public function getIsDecimalDivided() { return $this->getData('getIsDecimalDivided'); }
            public function setIsDecimalDivided($isDecimalDivided) { return $this->setData('getIsDecimalDivided', $isDecimalDivided); }
            public function getShowDefaultNotificationMessage() { return $this->getData('getShowDefaultNotificationMessage'); }
            public function setShowDefaultNotificationMessage($showDefaultNotificationMessage) { return $this->setData('getShowDefaultNotificationMessage', $showDefaultNotificationMessage); }
            public function getUseConfigMinQty() { return $this->getData('getUseConfigMinQty'); }
            public function setUseConfigMinQty($useConfigMinQty) { return $this->setData('getUseConfigMinQty', $useConfigMinQty); }
            public function getUseConfigMinSaleQty() { return $this->getData('getUseConfigMinSaleQty'); }
            public function setUseConfigMinSaleQty($useConfigMinSaleQty) { return $this->setData('getUseConfigMinSaleQty', $useConfigMinSaleQty); }
            public function getUseConfigMaxSaleQty() { return $this->getData('getUseConfigMaxSaleQty'); }
            public function setUseConfigMaxSaleQty($useConfigMaxSaleQty) { return $this->setData('getUseConfigMaxSaleQty', $useConfigMaxSaleQty); }
            public function getUseConfigBackorders() { return $this->getData('getUseConfigBackorders'); }
            public function setUseConfigBackorders($useConfigBackorders) { return $this->setData('getUseConfigBackorders', $useConfigBackorders); }
            public function getUseConfigNotifyStockQty() { return $this->getData('getUseConfigNotifyStockQty'); }
            public function setUseConfigNotifyStockQty($useConfigNotifyStockQty) { return $this->setData('getUseConfigNotifyStockQty', $useConfigNotifyStockQty); }
            public function getUseConfigQtyIncrements() { return $this->getData('getUseConfigQtyIncrements'); }
            public function setUseConfigQtyIncrements($useConfigQtyIncrements) { return $this->setData('getUseConfigQtyIncrements', $useConfigQtyIncrements); }
            public function getUseConfigEnableQtyInc() { return $this->getData('getUseConfigEnableQtyInc'); }
            public function setUseConfigEnableQtyInc($useConfigEnableQtyInc) { return $this->setData('getUseConfigEnableQtyInc', $useConfigEnableQtyInc); }
            public function getUseConfigManageStock() { return $this->getData('getUseConfigManageStock'); }
            public function setUseConfigManageStock($useConfigManageStock) { return $this->setData('getUseConfigManageStock', $useConfigManageStock); }
            public function getStockStatusChangedAuto() { return $this->getData('getStockStatusChangedAuto'); }
            public function setStockStatusChangedAuto($stockStatusChangedAuto) { return $this->setData('getStockStatusChangedAuto', $stockStatusChangedAuto); }
            public function getExtensionAttributes() { return null; }
            public function setExtensionAttributes($extensionAttributes) { return $this; }
            public function addData(array $arr) { return $this; }
            public function unsetData($key = null) { return $this; }
            public function hasData($key = '') { return false; }
            public function toArray($arrAttributes = []) { return []; }
            public function toJson($arrAttributes = []) { return ''; }
            public function toString($format = '') { return ''; }
            public function isEmpty() { return true; }
        };

        // Configure the anonymous class with variation data
        $stockItem->setSuppressCheckQtyIncrements($variation['values']['_suppress_check_qty_increments_']);
        $stockItem->setIsSaleable($variation['values']['_is_saleable_']);
        $stockItem->setOrderedItems($variation['values']['_ordered_items_']);
        $stockItem->setProductName($variation['values']['_product_']);
        $stockItem->setIsChildItem(false);
        $stockItem->setHasStockQty(true);
        $stockItem->setStockQty($variation['values']['_stock_qty_']);

        // Set values for stockItemMethods
        foreach ($this->stockItemMethods as $method) {
            $value = isset($variation['values'][$method]) ? $variation['values'][$method] : null;
            $stockItem->setData($method, $value);
        }
        
        // Ensure getQty returns the correct value for getStockQty test
        if (isset($variation['values']['getQty'])) {
            $stockItem->setQty($variation['values']['getQty']);
        }

        return $stockItem;
    }

    /**
     * @param $methodName
     * @param array|null $options
     * @return array
     */
    protected static function prepareDataForMethod($methodName, ?array $options = null)
    {
        $variations = [];
        if ($options === null) {
            $options = self::getVariations();
        }
        foreach ($options as $variation) {
            $stockItem = static fn (self $testCase) => $testCase->getStockItemClassMock($variation);
            $expectedResult = isset($variation['results'][$methodName]) ? $variation['results'][$methodName] : null;
            $variations[] = [
                'stockItem' => $stockItem,
                'expectedResult' => $expectedResult,
            ];
        }
        return $variations;
    }

    /**
     * @return array
     */
    private static function getVariations()
    {
        $stockQty = 100;
        return [
            [
                'values' => [
                    'getIsInStock' => true,
                    'getQty' => $stockQty,
                    'getMinQty' => 0,
                    'getMinSaleQty' => 0,
                    'getMaxSaleQty' => 99,
                    'getNotifyStockQty' => 10,
                    'getManageStock' => true,
                    'getBackorders' => 1,
                    'getQtyIncrements' => 3,
                    '_stock_qty_' => $stockQty,
                    '_suppress_check_qty_increments_' => false,
                    '_is_saleable_' => true,
                    '_ordered_items_' => 0,
                    '_product_' => 'Test product Name',
                ],
                'results' => [
                    'verifyStock' => true,
                    'verifyNotification' => false,
                    'checkQty' => true,
                    'suggestQty' => 51,
                    'getStockQty' => $stockQty,
                    'checkQtyIncrements' => false,
                    'checkQuoteItemQty' => true,
                ],
            ],
            [
                'values' => [
                    'getIsInStock' => true,
                    'getQty' => $stockQty,
                    'getMinQty' => 60,
                    'getMinSaleQty' => 0,
                    'getMaxSaleQty' => 99,
                    'getNotifyStockQty' => 101,
                    'getManageStock' => true,
                    'getBackorders' => 3,
                    'getQtyIncrements' => 1,
                    '_stock_qty_' => $stockQty,
                    '_suppress_check_qty_increments_' => false,
                    '_is_saleable_' => true,
                    '_ordered_items_' => 0,
                    '_product_' => 'Test product Name',
                ],
                'results' => [
                    'verifyStock' => true,
                    'verifyNotification' => true,
                    'checkQty' => false,
                    'suggestQty' => 50.5,
                    'getStockQty' => $stockQty,
                    'checkQtyIncrements' => false,
                    'checkQuoteItemQty' => true,
                ],
            ],
            [
                'values' => [
                    'getIsInStock' => true,
                    'getQty' => null,
                    'getMinQty' => 60,
                    'getMinSaleQty' => 1,
                    'getMaxSaleQty' => 99,
                    'getNotifyStockQty' => 101,
                    'getManageStock' => true,
                    'getBackorders' => 0,
                    'getQtyIncrements' => 1,
                    '_stock_qty_' => null,
                    '_suppress_check_qty_increments_' => false,
                    '_is_saleable_' => true,
                    '_ordered_items_' => 0,
                    '_product_' => 'Test product Name',
                ],
                'results' => [
                    'verifyStock' => false,
                    'verifyNotification' => true,
                    'checkQty' => false,
                    'suggestQty' => 50.5,
                    'getStockQty' => null,
                    'checkQtyIncrements' => false,
                    'checkQuoteItemQty' => true,
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    private static function getVariationsForQtyAndStock()
    {
        $stockQty = 100;
        return [
            [
                'values' => [
                    'getIsInStock' => false,
                    'getQty' => $stockQty,
                    'getMinQty' => 60,
                    'getMinSaleQty' => 1,
                    'getMaxSaleQty' => 99,
                    'getNotifyStockQty' => 101,
                    'getManageStock' => true,
                    'getBackorders' => 0,
                    'getQtyIncrements' => 1,
                    '_stock_qty_' => null,
                    '_suppress_check_qty_increments_' => false,
                    '_is_saleable_' => true,
                    '_ordered_items_' => 0,
                    '_product_' => 'Test product Name',
                ],
                'results' => [
                    'checkQty' => false
                ]
            ],
            [
                'values' => [
                    'getIsInStock' => false,
                    'getQty' => 0,
                    'getMinQty' => 60,
                    'getMinSaleQty' => 1,
                    'getMaxSaleQty' => 99,
                    'getNotifyStockQty' => 101,
                    'getManageStock' => true,
                    'getBackorders' => 0,
                    'getQtyIncrements' => 1,
                    '_stock_qty_' => null,
                    '_suppress_check_qty_increments_' => false,
                    '_is_saleable_' => true,
                    '_ordered_items_' => 0,
                    '_product_' => 'Test product Name',
                ],
                'results' => [
                    'checkQty' => false
                ]
            ]
        ];
    }

    /**
     * @param bool $isChildItem
     * @param string $expectedMsg
     */
    #[DataProvider('checkQtyIncrementsMsgDataProvider')]
    public function testCheckQtyIncrementsMsg($isChildItem, $expectedMsg)
    {
        $qty = 1;
        $qtyIncrements = 5;
        
        // Create anonymous class implementing StockItemInterface with dynamic methods
        $stockItem = new class implements StockItemInterface {
            private $suppressCheckQtyIncrements = false;
            private $qtyIncrements = null;
            private $isChildItem = false;
            private $productName = '';

            public function __construct() {}

            // Dynamic methods from stockAddItemMethods
            public function getSuppressCheckQtyIncrements() { return $this->suppressCheckQtyIncrements; }
            public function setSuppressCheckQtyIncrements($value) { $this->suppressCheckQtyIncrements = $value; return $this; }
            public function getQtyIncrements() { return $this->qtyIncrements; }
            public function setQtyIncrements($value) { $this->qtyIncrements = $value; return $this; }
            public function getIsChildItem() { return $this->isChildItem; }
            public function setIsChildItem($value) { $this->isChildItem = $value; return $this; }
            public function getProductName() { return $this->productName; }
            public function setProductName($value) { $this->productName = $value; return $this; }

            // Required StockItemInterface methods
            public function getItemId() { return null; }
            public function setItemId($itemId) { return $this; }
            public function getProductId() { return null; }
            public function setProductId($productId) { return $this; }
            public function getWebsiteId() { return null; }
            public function setWebsiteId($websiteId) { return $this; }
            public function getStockId() { return null; }
            public function setStockId($stockId) { return $this; }
            public function getQty() { return null; }
            public function setQty($qty) { return $this; }
            public function getMinQty() { return null; }
            public function setMinQty($minQty) { return $this; }
            public function getMinSaleQty() { return null; }
            public function setMinSaleQty($minSaleQty) { return $this; }
            public function getMaxSaleQty() { return null; }
            public function setMaxSaleQty($maxSaleQty) { return $this; }
            public function getIsInStock() { return null; }
            public function setIsInStock($isInStock) { return $this; }
            public function getLowStockDate() { return null; }
            public function setLowStockDate($lowStockDate) { return $this; }
            public function getNotifyStockQty() { return null; }
            public function setNotifyStockQty($notifyStockQty) { return $this; }
            public function getManageStock() { return null; }
            public function setManageStock($manageStock) { return $this; }
            public function getBackorders() { return null; }
            public function setBackorders($backorders) { return $this; }
            public function getEnableQtyIncrements() { return null; }
            public function setEnableQtyIncrements($enableQtyIncrements) { return $this; }
            public function getIsQtyDecimal() { return null; }
            public function setIsQtyDecimal($isQtyDecimal) { return $this; }
            public function getIsDecimalDivided() { return null; }
            public function setIsDecimalDivided($isDecimalDivided) { return $this; }
            public function getShowDefaultNotificationMessage() { return null; }
            public function setShowDefaultNotificationMessage($showDefaultNotificationMessage) { return $this; }
            public function getUseConfigMinQty() { return null; }
            public function setUseConfigMinQty($useConfigMinQty) { return $this; }
            public function getUseConfigMinSaleQty() { return null; }
            public function setUseConfigMinSaleQty($useConfigMinSaleQty) { return $this; }
            public function getUseConfigMaxSaleQty() { return null; }
            public function setUseConfigMaxSaleQty($useConfigMaxSaleQty) { return $this; }
            public function getUseConfigBackorders() { return null; }
            public function setUseConfigBackorders($useConfigBackorders) { return $this; }
            public function getUseConfigNotifyStockQty() { return null; }
            public function setUseConfigNotifyStockQty($useConfigNotifyStockQty) { return $this; }
            public function getUseConfigQtyIncrements() { return null; }
            public function setUseConfigQtyIncrements($useConfigQtyIncrements) { return $this; }
            public function getUseConfigEnableQtyInc() { return null; }
            public function setUseConfigEnableQtyInc($useConfigEnableQtyInc) { return $this; }
            public function getUseConfigManageStock() { return null; }
            public function setUseConfigManageStock($useConfigManageStock) { return $this; }
            public function getStockStatusChangedAuto() { return null; }
            public function setStockStatusChangedAuto($stockStatusChangedAuto) { return $this; }
            public function getExtensionAttributes() { return null; }
            public function setExtensionAttributes($extensionAttributes) { return $this; }
            public function getData($key = '', $index = null) { return null; }
            public function setData($key, $value = null) { return $this; }
            public function addData(array $arr) { return $this; }
            public function unsetData($key = null) { return $this; }
            public function hasData($key = '') { return false; }
            public function toArray($arrAttributes = []) { return []; }
            public function toJson($arrAttributes = []) { return ''; }
            public function toString($format = '') { return ''; }
            public function isEmpty() { return true; }
        };

        // Configure the anonymous class
        $stockItem->setSuppressCheckQtyIncrements(false);
        $stockItem->setQtyIncrements($qtyIncrements);
        $stockItem->setIsChildItem($isChildItem);
        $stockItem->setProductName('Simple Product');
        
        $this->mathDivision->method('getExactDivision')->willReturn(1);

        $result = $this->stockStateProvider->checkQtyIncrements($stockItem, $qty);
        $this->assertTrue($result->getHasError());
        $this->assertEquals($expectedMsg, $result->getMessage()->render());
    }

    /**
     * @return array
     */
    public static function checkQtyIncrementsMsgDataProvider()
    {
        return [
            [true, 'You can buy Simple Product only in quantities of 5 at a time.'],
            [false, 'You can buy this product only in quantities of 5 at a time.'],
        ];
    }
}
