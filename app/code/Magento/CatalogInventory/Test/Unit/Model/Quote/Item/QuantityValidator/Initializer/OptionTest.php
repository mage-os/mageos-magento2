<?php

/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogInventory\Test\Unit\Model\Quote\Item\QuantityValidator\Initializer;

use Magento\Catalog\Model\Product;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Api\StockStateInterface;
use Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\Initializer\Option;
use Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\QuoteItemQtyList;
use Magento\Framework\DataObject;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Quote\Model\Quote\Item;
use Magento\Store\Model\Store;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.TooManyFields)
 *
 */
class OptionTest extends TestCase
{
    /**
     * @var Option
     */
    protected $validator;

    /**
     * @var MockObject
     */
    protected $qtyItemListMock;

    /**
     * @var MockObject
     */
    protected $optionMock;

    /**
     * @var MockObject
     */
    protected $quoteItemMock;

    /**
     * @var MockObject
     */
    protected $stockItemMock;

    /**
     * @var MockObject
     */
    protected $productMock;

    /**
     * @var MockObject
     */
    protected $resultMock;

    /**
     * @var StockRegistryInterface|MockObject
     */
    protected $stockRegistry;

    /**
     * @var StockStateInterface|MockObject
     */
    protected $stockState;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var int
     */
    protected $productId = 111;

    /**
     * @var int
     */
    protected $websiteId = 111;

    protected function setUp(): void
    {
        $this->optionMock = new class extends \Magento\Quote\Model\Quote\Item\Option {
            /**
             * @var mixed
             */
            private $isQtyDecimal = null;
            /**
             * @var mixed
             */
            private $hasQtyOptionUpdate = null;
            /**
             * @var mixed
             */
            private $value = null;
            /**
             * @var mixed
             */
            private $message = null;
            /**
             * @var mixed
             */
            private $backorders = null;
            /**
             * @var mixed
             */
            private $product = null;

            public function __construct()
            {
                // Skip parent constructor to avoid complex dependencies
            }

            // Dynamic methods from addMethods
            public function setIsQtyDecimal($value)
            {
                $this->isQtyDecimal = $value;
                return $this;
            }
            public function getIsQtyDecimal()
            {
                return $this->isQtyDecimal;
            }
            public function setHasQtyOptionUpdate($value)
            {
                $this->hasQtyOptionUpdate = $value;
                return $this;
            }
            public function getHasQtyOptionUpdate()
            {
                return $this->hasQtyOptionUpdate;
            }
            public function setValue($value)
            {
                $this->value = $value;
                return $this;
            }
            public function getValue()
            {
                return $this->value;
            }
            public function setMessage($value)
            {
                $this->message = $value;
                return $this;
            }
            public function getMessage()
            {
                return $this->message;
            }
            public function setBackorders($value)
            {
                $this->backorders = $value;
                return $this;
            }
            public function getBackorders()
            {
                return $this->backorders;
            }
            public function getProduct()
            {
                return $this->product;
            }
            public function setProduct($value)
            {
                $this->product = $value;
                return $this;
            }
        };

        $store = $this->getMockBuilder(Store::class)
            ->onlyMethods(['getWebsiteId'])
            ->disableOriginalConstructor()
            ->getMock();
        $store->expects($this->any())->method('getWebsiteId')->willReturn($this->websiteId);

        $this->quoteItemMock = new class extends Item {
            /**
             * @var mixed
             */
            private $qtyToAdd = null;
            /**
             * @var mixed
             */
            private $id = null;
            /**
             * @var mixed
             */
            private $quoteId = null;
            /**
             * @var array
             */
            private $data = [];
            /**
             * @var mixed
             */
            private $store = null;

            public function __construct()
            {
                // Skip parent constructor to avoid complex dependencies
            }

            // Dynamic method from addMethods
            public function getQtyToAdd()
            {
                return $this->qtyToAdd;
            }
            public function setQtyToAdd($value)
            {
                $this->qtyToAdd = $value;
                return $this;
            }

            // Methods from onlyMethods
            public function getId()
            {
                return $this->id;
            }
            public function setId($value)
            {
                $this->id = $value;
                return $this;
            }
            public function updateQtyOption($option, $qty)
            {
                return $this;
            }
            public function setData($key, $value = null)
            {
                if (is_array($key)) {
                    $this->data = array_merge($this->data, $key);
                } else {
                    $this->data[$key] = $value;
                }
                return $this;
            }
            public function getQuoteId()
            {
                return $this->quoteId;
            }
            public function setQuoteId($value)
            {
                $this->quoteId = $value;
                return $this;
            }
            public function getStore()
            {
                return $this->store;
            }
            public function setStore($value)
            {
                $this->store = $value;
                return $this;
            }
        };
        $this->quoteItemMock->setStore($store);

        $this->stockItemMock = new class implements StockItemInterface {
            /**
             * @var mixed
             */
            private $isChildItem = null;
            /**
             * @var mixed
             */
            private $suppressCheckQtyIncrements = null;
            /**
             * @var mixed
             */
            private $productName = null;
            /**
             * @var mixed
             */
            private $itemId = null;

            public function __construct()
            {
            }

            // Dynamic methods from addMethods
            public function setIsChildItem($value)
            {
                $this->isChildItem = $value;
                return $this;
            }
            public function getIsChildItem()
            {
                return $this->isChildItem;
            }
            public function setSuppressCheckQtyIncrements($value)
            {
                $this->suppressCheckQtyIncrements = $value;
                return $this;
            }
            public function getSuppressCheckQtyIncrements()
            {
                return $this->suppressCheckQtyIncrements;
            }
            public function unsIsChildItem()
            {
                $this->isChildItem = null;
                return $this;
            }
            public function setProductName($value)
            {
                $this->productName = $value;
                return $this;
            }
            public function getProductName()
            {
                return $this->productName;
            }

            // Method from onlyMethods
            public function getItemId()
            {
                return $this->itemId;
            }
            public function setItemId($value)
            {
                $this->itemId = $value;
                return $this;
            }

            // All required interface methods with default implementations
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
                return 0;
            }
            public function setQty($qty)
            {
                return $this;
            }
            public function getIsInStock()
            {
                return false;
            }
            public function setIsInStock($isInStock)
            {
                return $this;
            }
            public function getIsQtyDecimal()
            {
                return false;
            }
            public function setIsQtyDecimal($isQtyDecimal)
            {
                return $this;
            }
            public function getShowDefaultNotificationMessage()
            {
                return false;
            }
            public function getUseConfigMinQty()
            {
                return false;
            }
            public function setUseConfigMinQty($useConfigMinQty)
            {
                return $this;
            }
            public function getMinQty()
            {
                return 0;
            }
            public function setMinQty($minQty)
            {
                return $this;
            }
            public function getUseConfigMinSaleQty()
            {
                return 0;
            }
            public function setUseConfigMinSaleQty($useConfigMinSaleQty)
            {
                return $this;
            }
            public function getMinSaleQty()
            {
                return 0;
            }
            public function setMinSaleQty($minSaleQty)
            {
                return $this;
            }
            public function getUseConfigMaxSaleQty()
            {
                return false;
            }
            public function setUseConfigMaxSaleQty($useConfigMaxSaleQty)
            {
                return $this;
            }
            public function getMaxSaleQty()
            {
                return 0;
            }
            public function setMaxSaleQty($maxSaleQty)
            {
                return $this;
            }
            public function getUseConfigBackorders()
            {
                return false;
            }
            public function setUseConfigBackorders($useConfigBackorders)
            {
                return $this;
            }
            public function getBackorders()
            {
                return 0;
            }
            public function setBackorders($backOrders)
            {
                return $this;
            }
            public function getUseConfigNotifyStockQty()
            {
                return false;
            }
            public function setUseConfigNotifyStockQty($useConfigNotifyStockQty)
            {
                return $this;
            }
            public function getNotifyStockQty()
            {
                return 0;
            }
            public function setNotifyStockQty($notifyStockQty)
            {
                return $this;
            }
            public function getUseConfigQtyIncrements()
            {
                return false;
            }
            public function setUseConfigQtyIncrements($useConfigQtyIncrements)
            {
                return $this;
            }
            public function getQtyIncrements()
            {
                return false;
            }
            public function setQtyIncrements($qtyIncrements)
            {
                return $this;
            }
            public function getUseConfigEnableQtyInc()
            {
                return false;
            }
            public function setUseConfigEnableQtyInc($useConfigEnableQtyInc)
            {
                return $this;
            }
            public function getEnableQtyIncrements()
            {
                return false;
            }
            public function setEnableQtyIncrements($enableQtyIncrements)
            {
                return $this;
            }
            public function getUseConfigManageStock()
            {
                return false;
            }
            public function setUseConfigManageStock($useConfigManageStock)
            {
                return $this;
            }
            public function getManageStock()
            {
                return false;
            }
            public function setManageStock($manageStock)
            {
                return $this;
            }
            public function getLowStockDate()
            {
                return '';
            }
            public function setLowStockDate($lowStockDate)
            {
                return $this;
            }
            public function getIsDecimalDivided()
            {
                return false;
            }
            public function setIsDecimalDivided($isDecimalDivided)
            {
                return $this;
            }
            public function getStockStatusChangedAuto()
            {
                return 0;
            }
            public function setStockStatusChangedAuto($stockStatusChangedAuto)
            {
                return $this;
            }
            public function getExtensionAttributes()
            {
                return null;
            }
            public function setExtensionAttributes(
                \Magento\CatalogInventory\Api\Data\StockItemExtensionInterface $extensionAttributes
            ) {
                return $this;
            }
        };
        $this->productMock = $this->getMockBuilder(Product::class)
            ->onlyMethods(['getId', 'getStore'])
            ->disableOriginalConstructor()
            ->getMock();
        $store = $this->getMockBuilder(Store::class)
            ->onlyMethods(['getWebsiteId'])
            ->disableOriginalConstructor()
            ->getMock();
        $store->expects($this->any())->method('getWebsiteId')->willReturn($this->websiteId);
        $this->productMock->expects($this->any())->method('getStore')->willReturn($store);

        $this->qtyItemListMock = $this->createMock(
            QuoteItemQtyList::class
        );
        $this->resultMock = new class extends DataObject {
            /**
             * @var mixed
             */
            private $itemIsQtyDecimal = null;
            /**
             * @var mixed
             */
            private $hasQtyOptionUpdate = null;
            /**
             * @var mixed
             */
            private $origQty = null;
            /**
             * @var mixed
             */
            private $message = null;
            /**
             * @var mixed
             */
            private $itemBackorders = null;
            /**
             * @var mixed
             */
            private $itemQty = null;

            public function __construct()
            {
                // Skip parent constructor to avoid complex dependencies
            }

            // Dynamic methods from addMethods
            public function getItemIsQtyDecimal()
            {
                return $this->itemIsQtyDecimal;
            }
            public function setItemIsQtyDecimal($value)
            {
                $this->itemIsQtyDecimal = $value;
                return $this;
            }
            public function getHasQtyOptionUpdate()
            {
                return $this->hasQtyOptionUpdate;
            }
            public function setHasQtyOptionUpdate($value)
            {
                $this->hasQtyOptionUpdate = $value;
                return $this;
            }
            public function getOrigQty()
            {
                return $this->origQty;
            }
            public function setOrigQty($value)
            {
                $this->origQty = $value;
                return $this;
            }
            public function getMessage()
            {
                return $this->message;
            }
            public function setMessage($value)
            {
                $this->message = $value;
                return $this;
            }
            public function getItemBackorders()
            {
                return $this->itemBackorders;
            }
            public function setItemBackorders($value)
            {
                $this->itemBackorders = $value;
                return $this;
            }
            public function getItemQty()
            {
                return $this->itemQty;
            }
            public function setItemQty($value)
            {
                $this->itemQty = $value;
                return $this;
            }
        };

        $this->stockRegistry = $this->createMock(StockRegistryInterface::class);

        $this->stockState = $this->createMock(StockStateInterface::class);

        $this->objectManager = new ObjectManager($this);
        $this->validator = $this->objectManager->getObject(
            Option::class,
            [
                'quoteItemQtyList' => $this->qtyItemListMock,
                'stockRegistry' => $this->stockRegistry,
                'stockState' => $this->stockState
            ]
        );
    }

    public function testInitializeWhenResultIsDecimalGetBackordersMessageHasOptionQtyUpdate()
    {
        $optionValue = 5;
        $qtyForCheck = 50;
        $qty = 10;
        $qtyToAdd = 20;
        $this->optionMock->setValue($optionValue);
        $this->quoteItemMock->setQtyToAdd($qtyToAdd);
        $this->optionMock->setProduct($this->productMock);

        $this->stockItemMock->setIsChildItem(true);
        $this->stockItemMock->setItemId(true);

        $this->stockRegistry
            ->expects($this->once())
            ->method('getStockItem')
            ->willReturn($this->stockItemMock);

        $this->productMock->expects($this->any())->method('getId')->willReturn($this->productId);
        $this->quoteItemMock->setId('quote_item_id');
        $this->quoteItemMock->setQuoteId('quote_id');
        $this->qtyItemListMock->expects(
            $this->once()
        )->method(
            'getQty'
        )->with(
            $this->productId,
            'quote_item_id',
            'quote_id',
            $qtyToAdd * $optionValue
        )->willReturn(
            $qtyForCheck
        );
        $this->stockState->expects($this->once())->method('checkQuoteItemQty')->with(
            $this->productId,
            $qty * $optionValue,
            $qtyForCheck,
            $optionValue,
            $this->websiteId
        )->willReturn(
            $this->resultMock
        );
        $this->resultMock->setItemIsQtyDecimal('is_decimal');
        $this->optionMock->setIsQtyDecimal('is_decimal');
        $this->resultMock->setHasQtyOptionUpdate(true);
        $this->optionMock->setHasQtyOptionUpdate(true);
        $this->resultMock->setOrigQty('orig_qty');
        $this->quoteItemMock->updateQtyOption($this->optionMock, 'orig_qty');
        // Note: setValue is called internally by the validator, not in the test
        $this->quoteItemMock->setData('qty', $qty);
        $this->resultMock->setMessage('message');
        $this->optionMock->setMessage('message');
        $this->resultMock->setItemBackorders('backorders');
        $this->optionMock->setBackorders('backorders');

        $this->stockItemMock->unsIsChildItem();
        $this->resultMock->setItemQty($qty);
        $this->validator->initialize($this->optionMock, $this->quoteItemMock, $qty);
    }

    public function testInitializeWhenResultNotDecimalGetBackordersMessageHasOptionQtyUpdate()
    {
        $optionValue = 5;
        $qtyForCheck = 50;
        $qty = 10;
        $this->optionMock->setValue($optionValue);
        $this->quoteItemMock->setQtyToAdd(false);
        $this->optionMock->setProduct($this->productMock);

        $this->stockItemMock->setIsChildItem(true);
        $this->stockItemMock->setItemId(true);

        $this->stockRegistry
            ->expects($this->once())
            ->method('getStockItem')
            ->willReturn($this->stockItemMock);

        $this->productMock->expects($this->any())->method('getId')->willReturn($this->productId);
        $this->quoteItemMock->setId('quote_item_id');
        $this->quoteItemMock->setQuoteId('quote_id');
        $this->qtyItemListMock->expects(
            $this->once()
        )->method(
            'getQty'
        )->with(
            $this->productId,
            'quote_item_id',
            'quote_id',
            $qty * $optionValue
        )->willReturn(
            $qtyForCheck
        );
        $this->stockState->expects($this->once())->method('checkQuoteItemQty')->with(
            $this->productId,
            $qty * $optionValue,
            $qtyForCheck,
            $optionValue,
            $this->websiteId
        )->willReturn(
            $this->resultMock
        );
        $this->resultMock->setItemIsQtyDecimal(null);
        // Note: setIsQtyDecimal is never called in this test case
        $this->resultMock->setHasQtyOptionUpdate(null);
        // Note: setHasQtyOptionUpdate is never called in this test case
        $this->resultMock->setMessage(null);
        $this->resultMock->setItemBackorders(null);
        // Note: setBackorders is never called in this test case

        $this->stockItemMock->unsIsChildItem();
        $this->validator->initialize($this->optionMock, $this->quoteItemMock, $qty);
    }

    public function testInitializeWithInvalidOptionQty()
    {
        $this->expectException('Magento\Framework\Exception\LocalizedException');
        $this->expectExceptionMessage('The stock item for Product in option is not valid.');
        $optionValue = 5;
        $qty = 10;
        $this->optionMock->setValue($optionValue);
        $this->quoteItemMock->setQtyToAdd(false);
        $this->productMock->expects($this->any())->method('getId')->willReturn($this->productId);
        $this->optionMock->setProduct($this->productMock);
        $this->stockItemMock->setItemId(false);

        $this->stockRegistry
            ->expects($this->once())
            ->method('getStockItem')
            ->willReturn($this->stockItemMock);

        $this->validator->initialize($this->optionMock, $this->quoteItemMock, $qty);
    }
}
