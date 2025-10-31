<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Wishlist\Test\Unit\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Wishlist\Model\ResourceModel\Item\Collection;
use Magento\Wishlist\Model\WishlistItemPermissionsCollectionProcessor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class WishlistItemPermissionsCollectionProcessorTest extends TestCase
{
    /** @var ProductRepositoryInterface|MockObject */
    private $productRepository;

    /** @var SearchCriteriaBuilder|MockObject */
    private $searchCriteriaBuilder;

    /**
     * @var WishlistItemPermissionsCollectionProcessor
     */
    private WishlistItemPermissionsCollectionProcessor $processor;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);
        $this->searchCriteriaBuilder = $this->createMock(SearchCriteriaBuilder::class);

        $this->processor = new WishlistItemPermissionsCollectionProcessor(
            $this->productRepository,
            $this->searchCriteriaBuilder
        );
        parent::setUp();
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testExecuteReturnsCollectionWhenEmpty(): void
    {
        $collection = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getItems'])
            ->getMock();

        $collection->expects($this->once())
            ->method('getItems')
            ->willReturn([]);

        $this->productRepository->expects($this->never())->method('getList');
        $this->searchCriteriaBuilder->expects($this->never())->method('addFilter');

        $result = $this->processor->execute($collection);

        $this->assertSame($collection, $result);
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testExecuteFiltersCollectionWithValidProducts(): void
    {
        $wishlistItem1 = $this->getMockBuilder(\Magento\Wishlist\Model\Item::class)
            ->disableOriginalConstructor()
            ->addMethods(['getProductId'])
            ->getMock();
        $wishlistItem1->method('getProductId')->willReturn(10);

        $wishlistItem2 = $this->getMockBuilder(\Magento\Wishlist\Model\Item::class)
            ->disableOriginalConstructor()
            ->addMethods(['getProductId'])
            ->getMock();
        $wishlistItem2->method('getProductId')->willReturn(11);

        $collection = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getItems', 'getColumnValues', 'addFieldToFilter'])
            ->getMock();

        /** @var Collection|MockObject $clonedCollection */
        $clonedCollection = clone $collection;

        $collection->expects($this->once())
            ->method('getItems')
            ->willReturn([$wishlistItem1, $wishlistItem2]);

        $collection->expects($this->once())
            ->method('getColumnValues')
            ->with('product_id')
            ->willReturn([10, 11]);

        $clonedCollection->expects($this->once())
            ->method('addFieldToFilter')
            ->with(
                'main_table.product_id',
                ['in' => [10]]
            )
            ->willReturnSelf();

        $searchCriteria = $this->createMock(SearchCriteria::class);

        $this->searchCriteriaBuilder->expects($this->once())
            ->method('addFilter')
            ->with('entity_id', [10, 11], 'in')
            ->willReturnSelf();

        $this->searchCriteriaBuilder->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteria);

        $product10 = $this->getMockBuilder(ProductInterface::class)
            ->disableOriginalConstructor()
            ->addMethods(['getIsHidden'])
            ->getMockForAbstractClass();
        $product10->method('getId')->willReturn(10);
        $product10->method('getIsHidden')->willReturn(false);

        $product11 = $this->getMockBuilder(ProductInterface::class)
            ->disableOriginalConstructor()
            ->addMethods(['getIsHidden'])
            ->getMockForAbstractClass();
        $product11->method('getId')->willReturn(11);
        $product11->method('getIsHidden')->willReturn(true);

        $searchResults = $this->createMock(SearchResultsInterface::class);
        $searchResults->method('getItems')->willReturn([$product10, $product11]);

        $this->productRepository->expects($this->once())
            ->method('getList')
            ->with($searchCriteria)
            ->willReturn($searchResults);

        $result = $this->processor->execute($collection);

        $this->assertNotSame($clonedCollection, $result);
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testExecuteUsesCacheOnSecondCall(): void
    {
        $wishlistItem1 = $this->getMockBuilder(\Magento\Wishlist\Model\Item::class)
            ->disableOriginalConstructor()
            ->addMethods(['getProductId'])
            ->getMock();
        $wishlistItem1->method('getProductId')->willReturn(10);

        $wishlistItem2 = $this->getMockBuilder(\Magento\Wishlist\Model\Item::class)
            ->disableOriginalConstructor()
            ->addMethods(['getProductId'])
            ->getMock();
        $wishlistItem2->method('getProductId')->willReturn(11);

        /** @var Collection|MockObject $collection1 */
        $collection1 = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getItems', 'getColumnValues', 'addFieldToFilter'])
            ->getMock();

        /** @var Collection|MockObject $cloned1 */
        $cloned1 = clone $collection1;

        $collection1->expects($this->once())
            ->method('getItems')
            ->willReturn([$wishlistItem1, $wishlistItem2]);

        $collection1->expects($this->once())
            ->method('getColumnValues')
            ->with('product_id')
            ->willReturn([21, 22]);

        $cloned1->expects($this->once())
            ->method('addFieldToFilter')
            ->with('main_table.product_id', ['in' => [21, 22]])
            ->willReturnSelf();

        $searchCriteria = $this->createMock(SearchCriteria::class);

        $this->searchCriteriaBuilder->expects($this->once())
            ->method('addFilter')
            ->with('entity_id', [21, 22], 'in')
            ->willReturnSelf();

        $this->searchCriteriaBuilder->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteria);

        $product21 = $this->getMockBuilder(ProductInterface::class)
            ->disableOriginalConstructor()
            ->addMethods(['getIsHidden'])
            ->getMockForAbstractClass();
        $product21->method('getId')->willReturn(21);
        $product21->method('getIsHidden')->willReturn(false);

        $product22 = $this->getMockBuilder(ProductInterface::class)
            ->disableOriginalConstructor()
            ->addMethods(['getIsHidden'])
            ->getMockForAbstractClass();
        $product22->method('getId')->willReturn(22);
        $product22->method('getIsHidden')->willReturn(false);

        $searchResults = $this->createMock(SearchResultsInterface::class);
        $searchResults->method('getItems')->willReturn([$product21, $product22]);

        $this->productRepository->expects($this->once())
            ->method('getList')
            ->with($searchCriteria)
            ->willReturn($searchResults);

        $this->processor->execute($collection1);

        $collection2 = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getItems', 'getColumnValues', 'addFieldToFilter'])
            ->getMock();

        /** @var Collection|MockObject $cloned2 */
        $cloned2 = clone $collection2;

        $collection2->expects($this->once())
            ->method('getItems')
            ->willReturn([$wishlistItem1, $wishlistItem2]);

        $collection2->expects($this->once())
            ->method('getColumnValues')
            ->with('product_id')
            ->willReturn([21, 22]);

        $cloned2->expects($this->once())
            ->method('addFieldToFilter')
            ->with('main_table.product_id', ['in' => [21, 22]])
            ->willReturnSelf();

        $this->processor->execute($collection2);
    }
}
