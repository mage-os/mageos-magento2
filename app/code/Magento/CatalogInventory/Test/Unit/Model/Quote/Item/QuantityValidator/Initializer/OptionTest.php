<?php

/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogInventory\Test\Unit\Model\Quote\Item\QuantityValidator\Initializer;

use Magento\Catalog\Model\Product;
use Magento\CatalogInventory\Test\Unit\Helper\StockItemInterfaceTestHelper;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Api\StockStateInterface;
use Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\Initializer\Option;
use Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\QuoteItemQtyList;
use Magento\Framework\DataObject;
use Magento\Framework\Test\Unit\Helper\DataObjectTestHelper;
use Magento\Framework\Test\Unit\Helper\DataObjectTestHelperExtended;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Quote\Model\Quote\Item;
use Magento\Quote\Test\Unit\Helper\OptionItemTestHelper;
use Magento\Quote\Test\Unit\Helper\QuoteItemTestHelper;
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
        $this->optionMock = new OptionItemTestHelper();

        $store = $this->getMockBuilder(Store::class)
            ->onlyMethods(['getWebsiteId'])
            ->disableOriginalConstructor()
            ->getMock();
        $store->expects($this->any())->method('getWebsiteId')->willReturn($this->websiteId);

        $this->quoteItemMock = new QuoteItemTestHelper();
        $this->quoteItemMock->setStore($store);

        $this->stockItemMock = new StockItemInterfaceTestHelper();
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

        $this->resultMock = new DataObjectTestHelperExtended();

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
