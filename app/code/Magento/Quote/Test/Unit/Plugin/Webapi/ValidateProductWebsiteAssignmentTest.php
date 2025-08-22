<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Plugin\Webapi;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartItemRepositoryInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;
use Magento\Quote\Plugin\Webapi\ValidateProductWebsiteAssignment;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for ValidateProductWebsiteAssignment plugin
 */
class ValidateProductWebsiteAssignmentTest extends TestCase
{
    /**
     * @var ValidateProductWebsiteAssignment
     */
    private $plugin;

    /**
     * @var ProductRepositoryInterface|MockObject
     */
    private $productRepositoryMock;

    /**
     * @var StoreManagerInterface|MockObject
     */
    private $storeManagerMock;

    /**
     * @var CartRepositoryInterface|MockObject
     */
    private $cartRepositoryMock;

    /**
     * @var CartItemRepositoryInterface|MockObject
     */
    private $cartItemRepositoryMock;

    /**
     * @var CartItemInterface|MockObject
     */
    private $cartItemMock;

    /**
     * @var Product|MockObject
     */
    private $productMock;

    /**
     * @var Quote|MockObject
     */
    private $quoteMock;

    /**
     * @var Item|MockObject
     */
    private $quoteItemMock;

    /**
     * @var StoreInterface|MockObject
     */
    private $storeMock;

    /**
     * Set up test environment
     */
    protected function setUp(): void
    {
        $this->productRepositoryMock = $this->createMock(ProductRepositoryInterface::class);
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->cartRepositoryMock = $this->createMock(CartRepositoryInterface::class);
        $this->cartItemRepositoryMock = $this->createMock(CartItemRepositoryInterface::class);
        $this->cartItemMock = $this->createMock(CartItemInterface::class);
        $this->productMock = $this->createMock(Product::class);
        $this->quoteMock = $this->createMock(Quote::class);
        $this->storeMock = $this->createMock(StoreInterface::class);

        // Create quote item mock with addMethods to handle methods that might not exist in interface
        $this->quoteItemMock = $this->getMockBuilder(Item::class)
            ->disableOriginalConstructor()
            ->addMethods(['getStoreId', 'getProductId'])
            ->onlyMethods(['getSku'])
            ->getMock();

        $this->plugin = new ValidateProductWebsiteAssignment(
            $this->productRepositoryMock,
            $this->storeManagerMock,
            $this->cartRepositoryMock
        );
    }

    /**
     * Test successful validation when product is assigned to current website
     */
    public function testBeforeSaveSuccessful()
    {
        $sku = 'test-product';
        $quoteId = 1;
        $storeId = 1;
        $productId = 123;
        $websiteId = 1;
        $productWebsiteIds = [1, 2];

        $this->cartItemMock->expects($this->once())
            ->method('getSku')
            ->willReturn($sku);

        $this->cartItemMock->expects($this->once())
            ->method('getQuoteId')
            ->willReturn($quoteId);

        $this->cartRepositoryMock->expects($this->once())
            ->method('getActive')
            ->with($quoteId)
            ->willReturn($this->quoteMock);

        $this->quoteMock->expects($this->once())
            ->method('getAllItems')
            ->willReturn([$this->quoteItemMock]);

        $this->quoteItemMock->expects($this->once())
            ->method('getSku')
            ->willReturn($sku);

        $this->quoteItemMock->expects($this->once())
            ->method('getStoreId')
            ->willReturn($storeId);

        $this->quoteItemMock->expects($this->once())
            ->method('getProductId')
            ->willReturn($productId);

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->with($storeId)
            ->willReturn($this->storeMock);

        $this->storeMock->expects($this->once())
            ->method('getWebsiteId')
            ->willReturn($websiteId);

        $this->productRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($productId, false, $storeId)
            ->willReturn($this->productMock);

        $this->productMock->expects($this->once())
            ->method('getWebsiteIds')
            ->willReturn($productWebsiteIds);

        // Method returns void, so we just verify no exception is thrown
        $this->plugin->beforeSave($this->cartItemRepositoryMock, $this->cartItemMock);

        // If we reach this point, validation passed
        $this->assertTrue(true);
    }

    /**
     * Test validation when product is not assigned to current website
     */
    public function testBeforeSaveProductNotAssignedToWebsite()
    {
        $sku = 'test-product';
        $quoteId = 1;
        $storeId = 1;
        $productId = 123;
        $websiteId = 1;
        $productWebsiteIds = [2, 3]; // Different website IDs

        $this->cartItemMock->expects($this->once())
            ->method('getSku')
            ->willReturn($sku);

        $this->cartItemMock->expects($this->once())
            ->method('getQuoteId')
            ->willReturn($quoteId);

        $this->cartRepositoryMock->expects($this->once())
            ->method('getActive')
            ->with($quoteId)
            ->willReturn($this->quoteMock);

        $this->quoteMock->expects($this->once())
            ->method('getAllItems')
            ->willReturn([$this->quoteItemMock]);

        $this->quoteItemMock->expects($this->once())
            ->method('getSku')
            ->willReturn($sku);

        $this->quoteItemMock->expects($this->once())
            ->method('getStoreId')
            ->willReturn($storeId);

        $this->quoteItemMock->expects($this->once())
            ->method('getProductId')
            ->willReturn($productId);

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->with($storeId)
            ->willReturn($this->storeMock);

        $this->storeMock->expects($this->once())
            ->method('getWebsiteId')
            ->willReturn($websiteId);

        $this->productRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($productId, false, $storeId)
            ->willReturn($this->productMock);

        $this->productMock->expects($this->once())
            ->method('getWebsiteIds')
            ->willReturn($productWebsiteIds);

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Product that you are trying to add is not available.');

        $this->plugin->beforeSave($this->cartItemRepositoryMock, $this->cartItemMock);
    }

    /**
     * Test validation when product website IDs is not an array
     */
    public function testBeforeSaveProductWebsiteIdsNotArray()
    {
        $sku = 'test-product';
        $quoteId = 1;
        $storeId = 1;
        $productId = 123;
        $websiteId = 1;
        $productWebsiteIds = null; // Not an array

        $this->cartItemMock->expects($this->once())
            ->method('getSku')
            ->willReturn($sku);

        $this->cartItemMock->expects($this->once())
            ->method('getQuoteId')
            ->willReturn($quoteId);

        $this->cartRepositoryMock->expects($this->once())
            ->method('getActive')
            ->with($quoteId)
            ->willReturn($this->quoteMock);

        $this->quoteMock->expects($this->once())
            ->method('getAllItems')
            ->willReturn([$this->quoteItemMock]);

        $this->quoteItemMock->expects($this->once())
            ->method('getSku')
            ->willReturn($sku);

        $this->quoteItemMock->expects($this->once())
            ->method('getStoreId')
            ->willReturn($storeId);

        $this->quoteItemMock->expects($this->once())
            ->method('getProductId')
            ->willReturn($productId);

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->with($storeId)
            ->willReturn($this->storeMock);

        $this->storeMock->expects($this->once())
            ->method('getWebsiteId')
            ->willReturn($websiteId);

        $this->productRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($productId, false, $storeId)
            ->willReturn($this->productMock);

        $this->productMock->expects($this->once())
            ->method('getWebsiteIds')
            ->willReturn($productWebsiteIds);

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Product that you are trying to add is not available.');

        $this->plugin->beforeSave($this->cartItemRepositoryMock, $this->cartItemMock);
    }

    /**
     * Test validation when product is not found
     */
    public function testBeforeSaveProductNotFound()
    {
        $sku = 'test-product';
        $quoteId = 1;
        $storeId = 1;
        $productId = 123;

        $this->cartItemMock->expects($this->once())
            ->method('getSku')
            ->willReturn($sku);

        $this->cartItemMock->expects($this->once())
            ->method('getQuoteId')
            ->willReturn($quoteId);

        $this->cartRepositoryMock->expects($this->once())
            ->method('getActive')
            ->with($quoteId)
            ->willReturn($this->quoteMock);

        $this->quoteMock->expects($this->once())
            ->method('getAllItems')
            ->willReturn([$this->quoteItemMock]);

        $this->quoteItemMock->expects($this->once())
            ->method('getSku')
            ->willReturn($sku);

        $this->quoteItemMock->expects($this->once())
            ->method('getStoreId')
            ->willReturn($storeId);

        $this->quoteItemMock->expects($this->once())
            ->method('getProductId')
            ->willReturn($productId);

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->with($storeId)
            ->willReturn($this->storeMock);

        $this->storeMock->expects($this->once())
            ->method('getWebsiteId')
            ->willReturn(1);

        $this->productRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($productId, false, $storeId)
            ->willThrowException(new NoSuchEntityException(__('Product not found')));

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Product that you are trying to add is not available.');

        $this->plugin->beforeSave($this->cartItemRepositoryMock, $this->cartItemMock);
    }

    /**
     * Test validation skips when no SKU provided
     */
    public function testBeforeSaveNoSku()
    {
        $this->cartItemMock->expects($this->once())
            ->method('getSku')
            ->willReturn(null);

        // No further method calls expected since validation should return early
        // Method returns void, so we just verify no exception is thrown
        $this->plugin->beforeSave($this->cartItemRepositoryMock, $this->cartItemMock);

        // If we reach this point, validation passed
        $this->assertTrue(true);
    }

    /**
     * Test validation skips when empty SKU provided
     */
    public function testBeforeSaveEmptySku()
    {
        $this->cartItemMock->expects($this->once())
            ->method('getSku')
            ->willReturn('');

        // No further method calls expected since validation should return early
        // Method returns void, so we just verify no exception is thrown
        $this->plugin->beforeSave($this->cartItemRepositoryMock, $this->cartItemMock);

        // If we reach this point, validation passed
        $this->assertTrue(true);
    }

    /**
     * Test validation when product is assigned to multiple websites including current
     */
    public function testBeforeSaveMultipleWebsitesIncludingCurrent()
    {
        $sku = 'test-product';
        $quoteId = 1;
        $storeId = 1;
        $productId = 123;
        $websiteId = 2;
        $productWebsiteIds = [1, 2, 3]; // Multiple websites including current

        $this->cartItemMock->expects($this->once())
            ->method('getSku')
            ->willReturn($sku);

        $this->cartItemMock->expects($this->once())
            ->method('getQuoteId')
            ->willReturn($quoteId);

        $this->cartRepositoryMock->expects($this->once())
            ->method('getActive')
            ->with($quoteId)
            ->willReturn($this->quoteMock);

        $this->quoteMock->expects($this->once())
            ->method('getAllItems')
            ->willReturn([$this->quoteItemMock]);

        $this->quoteItemMock->expects($this->once())
            ->method('getSku')
            ->willReturn($sku);

        $this->quoteItemMock->expects($this->once())
            ->method('getStoreId')
            ->willReturn($storeId);

        $this->quoteItemMock->expects($this->once())
            ->method('getProductId')
            ->willReturn($productId);

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->with($storeId)
            ->willReturn($this->storeMock);

        $this->storeMock->expects($this->once())
            ->method('getWebsiteId')
            ->willReturn($websiteId);

        $this->productRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($productId, false, $storeId)
            ->willReturn($this->productMock);

        $this->productMock->expects($this->once())
            ->method('getWebsiteIds')
            ->willReturn($productWebsiteIds);

        // Method returns void, so we just verify no exception is thrown
        $this->plugin->beforeSave($this->cartItemRepositoryMock, $this->cartItemMock);

        // If we reach this point, validation passed
        $this->assertTrue(true);
    }

    /**
     * Test validation when product has empty website IDs array
     */
    public function testBeforeSaveEmptyWebsiteIds()
    {
        $sku = 'test-product';
        $quoteId = 1;
        $storeId = 1;
        $productId = 123;
        $websiteId = 1;
        $productWebsiteIds = []; // Empty array

        $this->cartItemMock->expects($this->once())
            ->method('getSku')
            ->willReturn($sku);

        $this->cartItemMock->expects($this->once())
            ->method('getQuoteId')
            ->willReturn($quoteId);

        $this->cartRepositoryMock->expects($this->once())
            ->method('getActive')
            ->with($quoteId)
            ->willReturn($this->quoteMock);

        $this->quoteMock->expects($this->once())
            ->method('getAllItems')
            ->willReturn([$this->quoteItemMock]);

        $this->quoteItemMock->expects($this->once())
            ->method('getSku')
            ->willReturn($sku);

        $this->quoteItemMock->expects($this->once())
            ->method('getStoreId')
            ->willReturn($storeId);

        $this->quoteItemMock->expects($this->once())
            ->method('getProductId')
            ->willReturn($productId);

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->with($storeId)
            ->willReturn($this->storeMock);

        $this->storeMock->expects($this->once())
            ->method('getWebsiteId')
            ->willReturn($websiteId);

        $this->productRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($productId, false, $storeId)
            ->willReturn($this->productMock);

        $this->productMock->expects($this->once())
            ->method('getWebsiteIds')
            ->willReturn($productWebsiteIds);

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Product that you are trying to add is not available.');

        $this->plugin->beforeSave($this->cartItemRepositoryMock, $this->cartItemMock);
    }

    /**
     * Test validation when website ID is zero
     */
    public function testBeforeSaveWebsiteIdZero()
    {
        $sku = 'test-product';
        $quoteId = 1;
        $storeId = 1;
        $productId = 123;
        $websiteId = 0; // Website ID 0
        $productWebsiteIds = [0, 1]; // Website ID 0 is included

        $this->cartItemMock->expects($this->once())
            ->method('getSku')
            ->willReturn($sku);

        $this->cartItemMock->expects($this->once())
            ->method('getQuoteId')
            ->willReturn($quoteId);

        $this->cartRepositoryMock->expects($this->once())
            ->method('getActive')
            ->with($quoteId)
            ->willReturn($this->quoteMock);

        $this->quoteMock->expects($this->once())
            ->method('getAllItems')
            ->willReturn([$this->quoteItemMock]);

        $this->quoteItemMock->expects($this->once())
            ->method('getSku')
            ->willReturn($sku);

        $this->quoteItemMock->expects($this->once())
            ->method('getStoreId')
            ->willReturn($storeId);

        $this->quoteItemMock->expects($this->once())
            ->method('getProductId')
            ->willReturn($productId);

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->with($storeId)
            ->willReturn($this->storeMock);

        $this->storeMock->expects($this->once())
            ->method('getWebsiteId')
            ->willReturn($websiteId);

        $this->productRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($productId, false, $storeId)
            ->willReturn($this->productMock);

        $this->productMock->expects($this->once())
            ->method('getWebsiteIds')
            ->willReturn($productWebsiteIds);

        // Method returns void, so we just verify no exception is thrown
        $this->plugin->beforeSave($this->cartItemRepositoryMock, $this->cartItemMock);

        // If we reach this point, validation passed
        $this->assertTrue(true);
    }

    /**
     * Test validation when no matching quote item found for SKU
     */
    public function testBeforeSaveNoMatchingQuoteItem()
    {
        $sku = 'test-product';
        $quoteId = 1;
        $differentSku = 'different-product';

        $this->cartItemMock->expects($this->once())
            ->method('getSku')
            ->willReturn($sku);

        $this->cartItemMock->expects($this->once())
            ->method('getQuoteId')
            ->willReturn($quoteId);

        $this->cartRepositoryMock->expects($this->once())
            ->method('getActive')
            ->with($quoteId)
            ->willReturn($this->quoteMock);

        $this->quoteMock->expects($this->once())
            ->method('getAllItems')
            ->willReturn([$this->quoteItemMock]);

        $this->quoteItemMock->expects($this->once())
            ->method('getSku')
            ->willReturn($differentSku); // Different SKU

        // No further method calls expected since no matching item found
        // Method returns void, so we just verify no exception is thrown
        $this->plugin->beforeSave($this->cartItemRepositoryMock, $this->cartItemMock);

        // If we reach this point, validation passed
        $this->assertTrue(true);
    }
}
