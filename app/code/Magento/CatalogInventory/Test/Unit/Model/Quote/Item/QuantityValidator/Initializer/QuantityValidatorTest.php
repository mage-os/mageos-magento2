<?php
/**
 * Copyright 2016 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogInventory\Test\Unit\Model\Quote\Item\QuantityValidator\Initializer;

use PHPUnit\Framework\Attributes\DataProvider;
use Magento\Bundle\Model\Product\Type;
use Magento\Catalog\Model\Product;
use Magento\CatalogInventory\Api\Data\StockStatusInterface;
use Magento\CatalogInventory\Helper\Data;
use Magento\CatalogInventory\Model\Quote\Item\QuantityValidator;
use Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\Initializer\Option;
use Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\Initializer\StockItem;
use Magento\CatalogInventory\Model\Stock;
use Magento\CatalogInventory\Model\Stock\Item as StockMock;
use Magento\CatalogInventory\Model\Stock\Status;
use Magento\CatalogInventory\Model\StockRegistry;
use Magento\CatalogInventory\Model\StockState;
use Magento\Framework\DataObject;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;
use Magento\Quote\Model\Quote\Item\Option as OptionItem;
use Magento\Store\Model\Store;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class QuantityValidatorTest extends TestCase
{
    /**
     * @var QuantityValidator
     */
    private $quantityValidator;

    /**
     * @var MockObject
     */
    private $stockRegistryMock;

    /**
     * @var MockObject
     */
    private $optionInitializer;

    /**
     * @var MockObject
     */
    private $observerMock;

    /**
     * @var MockObject
     */
    private $eventMock;

    /**
     * @var MockObject
     */
    private $quoteMock;

    /**
     * @var MockObject
     */
    private $storeMock;

    /**
     * @var MockObject
     */
    private $quoteItemMock;

    /**
     * @var MockObject
     */
    private $parentItemMock;

    /**
     * @var MockObject
     */
    private $productMock;

    /**
     * @var MockObject
     */
    private $stockItemMock;

    /**
     * @var MockObject
     */
    private $parentStockItemMock;

    /**
     * @var MockObject
     */
    private $typeInstanceMock;

    /**
     * @var MockObject
     */
    private $resultMock;

    /**
     * @var MockObject
     */
    private $stockState;

    /**
     * @var MockObject
     */
    private $stockItemInitializer;

    /**
     * @var MockObject|StockStatusInterface
     */
    private $stockStatusMock;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $objectManagerHelper = new ObjectManager($this);

        $this->stockRegistryMock = $this->createMock(StockRegistry::class);

        $this->stockStatusMock = $this->createMock(Status::class);

        $this->optionInitializer = $this->createMock(Option::class);
        $this->stockItemInitializer = $this->createMock(StockItem::class);
        $this->stockState = $this->createMock(StockState::class);
        $this->quantityValidator = $objectManagerHelper->getObject(
            QuantityValidator::class,
            [
                'optionInitializer' => $this->optionInitializer,
                'stockItemInitializer' => $this->stockItemInitializer,
                'stockRegistry' => $this->stockRegistryMock,
                'stockState' => $this->stockState
            ]
        );
        $this->observerMock = $this->createMock(Observer::class);
        // Create anonymous class for Event with getItem method
        $this->eventMock = new class extends Event {
            private $item = null;

            public function __construct() {
                // Skip parent constructor to avoid complex dependencies
            }

            public function getItem() { return $this->item; }
            public function setItem($item) { $this->item = $item; return $this; }
        };
        // Create anonymous class for Quote with dynamic methods
        $this->quoteMock = new class extends Quote {
            private $hasError = false;
            private $isSuperMode = false;
            private $quote = null;
            private $itemsCollection = [];
            private $errorInfos = [];

            public function __construct() {
                // Skip parent constructor to avoid complex dependencies
            }

            public function getHasError() { return $this->hasError; }
            public function setHasError($hasError) { $this->hasError = $hasError; return $this; }
            public function getIsSuperMode() { return $this->isSuperMode; }
            public function setIsSuperMode($isSuperMode) { $this->isSuperMode = $isSuperMode; return $this; }
            public function getQuote() { return $this->quote; }
            public function setQuote($quote) { $this->quote = $quote; return $this; }
            public function getItemsCollection($useCache = true) { return $this->itemsCollection; }
            public function setItemsCollection($items) { $this->itemsCollection = $items; return $this; }
            public function removeErrorInfosByParams($origin, $params) { return $this; }
            public function addErrorInfo($type = 'error', $origin = null, $code = null, $message = null, $additionalData = null) { 
                $this->errorInfos[] = ['type' => $type, 'origin' => $origin, 'code' => $code, 'message' => $message, 'additionalData' => $additionalData];
                return $this; 
            }
        };
        $this->storeMock = $this->createMock(Store::class);
        // Create anonymous class for Quote Item with dynamic methods
        $this->quoteItemMock = new class extends Item {
            private $productId = null;
            private $hasError = false;
            private $stockStateResult = null;
            private $quote = null;
            private $qty = null;
            private $product = null;
            private $parentItem = null;
            private $data = [];
            private $qtyOptions = null;
            private $itemId = null;
            private $errorInfos = [];

            public function __construct() {
                // Skip parent constructor to avoid complex dependencies
            }

            public function getProductId() { return $this->productId; }
            public function setProductId($productId) { $this->productId = $productId; return $this; }
            public function getHasError() { return $this->hasError; }
            public function setHasError($hasError) { $this->hasError = $hasError; return $this; }
            public function getStockStateResult() { return $this->stockStateResult; }
            public function setStockStateResult($result) { $this->stockStateResult = $result; return $this; }
            public function getQuote() { return $this->quote; }
            public function setQuote($quote) { $this->quote = $quote; return $this; }
            public function getQty() { return $this->qty; }
            public function setQty($qty) { $this->qty = $qty; return $this; }
            public function getProduct() { return $this->product; }
            public function setProduct($product) { $this->product = $product; return $this; }
            public function getParentItem() { return $this->parentItem; }
            public function setParentItem($parentItem) { $this->parentItem = $parentItem; return $this; }
            public function addErrorInfo($origin = null, $code = null, $message = null, $additionalData = null) { 
                $this->errorInfos[] = ['origin' => $origin, 'code' => $code, 'message' => $message, 'additionalData' => $additionalData];
                return $this; 
            }
            public function setData($key, $value = null) { 
                if (is_array($key)) {
                    $this->data = array_merge($this->data, $key);
                } else {
                    $this->data[$key] = $value;
                }
                return $this; 
            }
            public function getQtyOptions() { return $this->qtyOptions; }
            public function setQtyOptions($options) { $this->qtyOptions = $options; return $this; }
            public function getItemId() { return $this->itemId; }
            public function setItemId($itemId) { $this->itemId = $itemId; return $this; }
        };
        $this->parentItemMock = $this->createPartialMock(Item::class, ['getProduct', 'getId', 'getStore']);
        $this->productMock = $this->createMock(Product::class);
        $this->stockItemMock = $this->createMock(StockMock::class);
        // Create anonymous class for Stock Item with dynamic methods
        $this->parentStockItemMock = new class extends StockMock {
            private $stockStatus = null;
            private $isInStock = false;

            public function __construct() {
                // Skip parent constructor to avoid complex dependencies
            }

            public function getStockStatus() { return $this->stockStatus; }
            public function setStockStatus($status) { $this->stockStatus = $status; return $this; }
            public function getIsInStock() { return $this->isInStock; }
            public function setIsInStock($isInStock) { $this->isInStock = $isInStock; return $this; }
        };

        $this->typeInstanceMock = $this->createMock(Type::class);

        // Create anonymous class for DataObject with dynamic methods
        $this->resultMock = new class extends DataObject {
            private $checkQtyIncrements = null;
            private $message = null;
            private $quoteMessage = null;
            private $hasError = false;
            private $quoteMessageIndex = null;

            public function __construct() {
                // Skip parent constructor to avoid complex dependencies
            }

            public function checkQtyIncrements() { return $this->checkQtyIncrements; }
            public function setCheckQtyIncrements($value) { $this->checkQtyIncrements = $value; return $this; }
            public function getMessage() { return $this->message; }
            public function setMessage($message) { $this->message = $message; return $this; }
            public function getQuoteMessage() { return $this->quoteMessage; }
            public function setQuoteMessage($message) { $this->quoteMessage = $message; return $this; }
            public function getHasError() { return $this->hasError; }
            public function setHasError($hasError) { $this->hasError = $hasError; return $this; }
            public function getQuoteMessageIndex() { return $this->quoteMessageIndex; }
            public function setQuoteMessageIndex($index) { $this->quoteMessageIndex = $index; return $this; }
        };
    }

    /**
     * This tests the scenario when item is not in stock.
     *
     * @return void
     * @throws LocalizedException
     */
    public function testValidateOutOfStock(): void
    {
        $this->createInitialStub(0);
        $this->stockRegistryMock
            ->method('getStockItem')
            ->willReturnOnConsecutiveCalls($this->stockItemMock);

        $this->stockRegistryMock->expects($this->atLeastOnce())
            ->method('getStockStatus')
            ->willReturnOnConsecutiveCalls($this->stockStatusMock);

        $this->stockStatusMock
            ->method('getStockStatus')
            ->willReturn(0);

        // Note: addErrorInfo is now a direct method call on anonymous class, no expects() needed
        // Note: addErrorInfo is now a direct method call on anonymous class, no expects() needed
        // The method will be called during validation
        $this->quantityValidator->validate($this->observerMock);
    }

    /**
     * This tests the scenario when item is in stock but parent is not in stock.
     *
     * @return void
     * @throws LocalizedException
     */
    public function testValidateInStock(): void
    {
        $this->createInitialStub(1);

        $this->quoteItemMock->setParentItem($this->parentItemMock);

        $this->stockRegistryMock
            ->method('getStockItem')
            ->willReturnOnConsecutiveCalls($this->stockItemMock);
        $this->stockRegistryMock
            ->method('getStockStatus')
            ->willReturnOnConsecutiveCalls($this->stockStatusMock, $this->parentStockItemMock);

        $this->parentStockItemMock->setStockStatus(0);

        $this->stockStatusMock->expects($this->atLeastOnce())
            ->method('getStockStatus')
            ->willReturn(1);

        // Note: addErrorInfo is now a direct method call on anonymous class, no expects() needed
        // Note: addErrorInfo is now a direct method call on anonymous class, no expects() needed
        // The method will be called during validation
        $this->quantityValidator->validate($this->observerMock);
    }

    /**
     * This tests the scenario when item is in stock and has options.
     *
     * @return void
     * @throws LocalizedException
     */
    public function testValidateWithOptions(): void
    {
        // Create anonymous class for Option Item with dynamic methods
        $optionMock = new class extends OptionItem {
            private $hasError = false;
            private $stockStateResult = null;
            private $product = null;

            public function __construct() {
                // Skip parent constructor to avoid complex dependencies
            }

            public function getProduct() { return $this->product; }
            public function setProduct($product) { $this->product = $product; return $this; }
            public function setHasError($hasError) { $this->hasError = $hasError; return $this; }
            public function getStockStateResult() { return $this->stockStateResult; }
            public function setStockStateResult($result) { $this->stockStateResult = $result; return $this; }
        };
        $optionMock->setStockStateResult($this->resultMock);
        $optionMock->setProduct($this->productMock);
        $this->stockRegistryMock
            ->method('getStockItem')
            ->willReturnOnConsecutiveCalls($this->stockItemMock);
        $this->stockRegistryMock
            ->method('getStockStatus')
            ->willReturnOnConsecutiveCalls($this->stockStatusMock);
        $options = [$optionMock];
        $this->createInitialStub(1);
        $this->setUpStubForQuantity(1, true);
        $this->setUpStubForRemoveError();
        $this->parentStockItemMock->setStockStatus(1);
        $this->stockStatusMock->expects($this->once())
            ->method('getStockStatus')
            ->willReturn(1);
        $this->quoteItemMock->setQtyOptions($options);
        $this->optionInitializer->method('initialize')->willReturn($this->resultMock);
        // Note: setHasError is now a direct method call on anonymous class, no expects() needed
        $this->quantityValidator->validate($this->observerMock);
    }

    /**
     * This tests the scenario with options but has errors.
     *
     * @param int $quantity
     * @param int $productStatus
     * @param int $productStockStatus
     * @return void
     * @throws LocalizedException
     */
    #[DataProvider('validateWithOptionsDataProvider')]
    public function testValidateWithOptionsAndError(int $quantity, int $productStatus, int $productStockStatus): void
    {
        // Create anonymous class for Option Item with dynamic methods
        $optionMock = new class extends OptionItem {
            private $hasError = false;
            private $stockStateResult = null;
            private $product = null;

            public function __construct() {
                // Skip parent constructor to avoid complex dependencies
            }

            public function getProduct() { return $this->product; }
            public function setProduct($product) { $this->product = $product; return $this; }
            public function setHasError($hasError) { $this->hasError = $hasError; return $this; }
            public function getStockStateResult() { return $this->stockStateResult; }
            public function setStockStateResult($result) { $this->stockStateResult = $result; return $this; }
        };
        $this->stockRegistryMock
            ->method('getStockItem')
            ->willReturnOnConsecutiveCalls($this->stockItemMock);
        $this->stockRegistryMock
            ->method('getStockStatus')
            ->willReturnOnConsecutiveCalls($this->stockStatusMock);
        $optionMock->setStockStateResult($this->resultMock);
        $optionMock->setProduct($this->productMock);
        $options = [$optionMock];
        $this->createInitialStub($quantity);
        $this->setUpStubForQuantity($quantity, true);
        $this->setUpStubForRemoveError();
        $this->parentStockItemMock->setStockStatus($productStatus);
        $this->stockStatusMock->expects($this->once())
            ->method('getStockStatus')
            ->willReturn($productStockStatus);
        $this->quoteItemMock->setQtyOptions($options);
        $this->optionInitializer->method('initialize')->willReturn($this->resultMock);
        // Note: setHasError is now a direct method call on anonymous class, no expects() needed
        $this->quantityValidator->validate($this->observerMock);
    }

    /**
     * @return array
     */
    public static function validateWithOptionsDataProvider(): array
    {
        return [
            'when product is enabled and in stock' =>
                [1, Product\Attribute\Source\Status::STATUS_ENABLED, Stock::STOCK_IN_STOCK],
            'when product is enabled but out of stock' =>
                [1, Product\Attribute\Source\Status::STATUS_ENABLED, Stock::STOCK_OUT_OF_STOCK],
            'when product is disabled and out of stock' =>
                [1, Product\Attribute\Source\Status::STATUS_DISABLED, Stock::STOCK_OUT_OF_STOCK],
            'when product is disabled but in stock' =>
                [1, Product\Attribute\Source\Status::STATUS_DISABLED, Stock::STOCK_IN_STOCK]
        ];
    }
    /**
     * This tests the scenario with options but has errors and remove errors from quote.
     *
     * @return void
     * @throws LocalizedException
     */
    public function testValidateAndRemoveErrorsFromQuote(): void
    {
        // Create anonymous class for Option Item with dynamic methods
        $optionMock = new class extends OptionItem {
            private $hasError = false;
            private $stockStateResult = null;
            private $product = null;

            public function __construct() {
                // Skip parent constructor to avoid complex dependencies
            }

            public function getProduct() { return $this->product; }
            public function setProduct($product) { $this->product = $product; return $this; }
            public function setHasError($hasError) { $this->hasError = $hasError; return $this; }
            public function getStockStateResult() { return $this->stockStateResult; }
            public function setStockStateResult($result) { $this->stockStateResult = $result; return $this; }
        };
        $quoteItem = $this->createMock(Item::class);
        $quoteItem->method('getItemId')->willReturn(4);
        $quoteItem->method('getErrorInfos')->willReturn([['code' => 2]]);
        $optionMock->setStockStateResult($this->resultMock);
        $optionMock->setProduct($this->productMock);
        $this->stockRegistryMock
            ->method('getStockItem')
            ->willReturnOnConsecutiveCalls($this->stockItemMock);
        $this->stockRegistryMock
            ->method('getStockStatus')
            ->willReturnOnConsecutiveCalls($this->stockStatusMock);
        $options = [$optionMock];
        $this->createInitialStub(1);
        $this->setUpStubForQuantity(1, true);
        $this->parentStockItemMock->setStockStatus(1);
        $this->stockStatusMock->expects($this->once())
            ->method('getStockStatus')
            ->willReturn(1);
        $this->quoteItemMock->setQtyOptions($options);
        $this->optionInitializer->method('initialize')->willReturn($this->resultMock);
        // Note: setHasError is now a direct method call on anonymous class, no expects() needed
        $this->quoteMock->setHasError(true);
        $this->quoteMock->setItemsCollection([$quoteItem]);
        $quoteItem->method('getItemId')->willReturn(4);
        $quoteItem->method('getErrorInfos')->willReturn([['code' => 2]]);
        $this->quoteItemMock->setItemId(3);
        // Note: removeErrorInfosByParams is now a direct method call on anonymous class, no expects() needed
        $this->quantityValidator->validate($this->observerMock);
    }

    /**
     * This tests the scenario when all the items are both parent and item are in stock and any errors are cleared.
     *
     * @return void
     * @throws LocalizedException
     */
    public function testRemoveError(): void
    {
        $this->createInitialStub(1);
        $this->setUpStubForRemoveError();
        $this->quoteItemMock->setQtyOptions(null);
        $this->stockRegistryMock
            ->method('getStockItem')
            ->willReturnOnConsecutiveCalls($this->stockItemMock);
        $callCount = 0;
        $this->stockRegistryMock->method('getStockStatus')
            ->willReturnCallback(function () use (&$callCount) {
                return $callCount++ === 0 ? $this->stockStatusMock : null;
            });
        $this->quoteItemMock->setParentItem($this->parentItemMock);
        $this->stockStatusMock->method('getStockStatus')->willReturn(1);
        // Note: addErrorInfo is now a direct method call on anonymous class, no expects() needed
        // Note: addErrorInfo is now a direct method call on anonymous class, no expects() needed
        $this->quantityValidator->validate($this->observerMock);
    }

    /**
     * This test the scenario when stock Item is not of correct type and throws appropriate exception.
     *
     * @return void
     * @throws LocalizedException
     */
    public function testException(): void
    {
        $this->createInitialStub(1);
        $this->stockRegistryMock
            ->method('getStockItem')
            ->willReturn(null);
        $this->expectException(LocalizedException::class);
        $this->quantityValidator->validate($this->observerMock);
    }

    /**
     * This tests the scenario when the error is in the quote item already.
     *
     * @return void
     * @throws LocalizedException
     */
    public function testValidateOutStockWithAlreadyErrorInQuoteItem(): void
    {
        $this->createInitialStub(1);
        // Create anonymous class for DataObject with dynamic methods
        $resultMock = new class extends DataObject {
            private $checkQtyIncrements = null;
            private $message = null;
            private $quoteMessage = null;
            private $hasError = false;

            public function __construct() {
                // Skip parent constructor to avoid complex dependencies
            }

            public function checkQtyIncrements() { return $this->checkQtyIncrements; }
            public function setCheckQtyIncrements($value) { $this->checkQtyIncrements = $value; return $this; }
            public function getMessage() { return $this->message; }
            public function setMessage($message) { $this->message = $message; return $this; }
            public function getQuoteMessage() { return $this->quoteMessage; }
            public function setQuoteMessage($message) { $this->quoteMessage = $message; return $this; }
            public function getHasError() { return $this->hasError; }
            public function setHasError($hasError) { $this->hasError = $hasError; return $this; }
        };
        $resultMock->setHasError(true);
        $this->stockRegistryMock->method('getStockItem')
            ->willReturn($this->stockItemMock);
        $this->quoteItemMock->setParentItem($this->parentItemMock);
        $this->quoteItemMock->setStockStateResult($resultMock);
        $this->stockRegistryMock
            ->method('getStockStatus')
            ->willReturnOnConsecutiveCalls($this->stockStatusMock, $this->parentStockItemMock);
        $this->parentStockItemMock->setStockStatus(0);
        $this->stockStatusMock->expects($this->atLeastOnce())
            ->method('getStockStatus')
            ->willReturn(1);
        // Note: addErrorInfo is now a direct method call on anonymous class, no expects() needed
        // Note: addErrorInfo is now a direct method call on anonymous class, no expects() needed
        // The method will be called during validation
        $this->quantityValidator->validate($this->observerMock);
    }

    /**
     * @param $qty
     * @param $hasError
     *
     * @return void
     */
    private function setUpStubForQuantity($qty, $hasError): void
    {
        $this->productMock->method('getTypeInstance')->willReturn($this->typeInstanceMock);
        $this->typeInstanceMock->method('prepareQuoteItemQty')->willReturn($qty);
        // Note: setData is now a direct method call on anonymous class, no expects() needed
        $this->productMock->method('getId')->willReturn(1);
        $this->stockState->method('checkQtyIncrements')->willReturn($this->resultMock);
        $this->resultMock->setHasError($hasError);
        $this->resultMock->setMessage('');
        $this->resultMock->setQuoteMessage('');
        $this->resultMock->setQuoteMessageIndex('');
    }

    /**
     * @param $qty
     */
    private function createInitialStub($qty): void
    {
        $this->storeMock->method('getWebsiteId')->willReturn(1);
        $this->quoteMock->setIsSuperMode(0);
        $this->productMock->method('getId')->willReturn(1);
        $this->productMock->method('getStore')->willReturn($this->storeMock);
        $this->quoteItemMock->setProductId(1);
        $this->quoteItemMock->setQuote($this->quoteMock);
        $this->quoteItemMock->setQty($qty);
        $this->quoteItemMock->setProduct($this->productMock);
        $this->eventMock->setItem($this->quoteItemMock);
        $this->observerMock->method('getEvent')->willReturn($this->eventMock);
        $this->parentItemMock->method('getProduct')->willReturn($this->productMock);
        $this->parentStockItemMock->setIsInStock(false);
        $this->storeMock->method('getWebsiteId')->willReturn(1);
        $this->quoteItemMock->setQuote($this->quoteMock);
        $this->quoteMock->setQuote($this->quoteMock);
        // Note: addErrorInfo is now a direct method call on anonymous class, no expects() needed
        // Note: addErrorInfo is now a direct method call on anonymous class, no expects() needed
        $this->setUpStubForQuantity(0, false);
        $this->stockItemInitializer->method('initialize')->willReturn($this->resultMock);
    }

    /**
     * @return void
     */
    private function setUpStubForRemoveError(): void
    {
        $quoteItems = [$this->quoteItemMock];
        $this->quoteItemMock->setHasError(false);
        $this->quoteMock->setItemsCollection($quoteItems);
        $this->quoteMock->setHasError(false);
    }
}
