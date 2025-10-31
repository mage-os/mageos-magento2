<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\WishlistGraphQl\Test\Unit\Model\Resolver;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Wishlist\Model\Item;
use Magento\Wishlist\Model\ResourceModel\Item\Collection as WishlistItemCollection;
use Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory as WishlistItemCollectionFactory;
use Magento\Wishlist\Model\Wishlist;
use Magento\Wishlist\Model\WishlistItemPermissionsCollectionProcessor;
use Magento\WishlistGraphQl\Model\Resolver\WishlistItemsResolver;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class WishlistItemsResolverTest extends TestCase
{
    /** @var WishlistItemCollectionFactory|MockObject */
    private WishlistItemCollectionFactory $collectionFactory;

    /** @var StoreManagerInterface|MockObject */
    private StoreManagerInterface $storeManager;

    /** @var WishlistItemPermissionsCollectionProcessor|MockObject */
    private WishlistItemPermissionsCollectionProcessor $permissionsProcessor;

    /**
     * @var WishlistItemsResolver
     */
    private WishlistItemsResolver $resolver;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->collectionFactory = $this->createMock(WishlistItemCollectionFactory::class);
        $this->storeManager = $this->createMock(StoreManagerInterface::class);
        $this->permissionsProcessor = $this->createMock(WishlistItemPermissionsCollectionProcessor::class);

        $this->resolver = new WishlistItemsResolver(
            $this->collectionFactory,
            $this->storeManager,
            $this->permissionsProcessor
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testResolveThrowsWhenModelMissing(): void
    {
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Missing key "model" in Wishlist value data');

        $field = $this->createMock(Field::class);
        $info = $this->createMock(ResolveInfo::class);

        $this->resolver->resolve($field, null, $info, []);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testResolveBuildsCollectionAndAppliesPermissions(): void
    {
        $field = $this->createMock(Field::class);
        $info = $this->createMock(ResolveInfo::class);

        $wishlist = $this->createMock(Wishlist::class);

        $store1 = $this->createMock(StoreInterface::class);
        $store1->method('getId')->willReturn(1);
        $store2 = $this->createMock(StoreInterface::class);
        $store2->method('getId')->willReturn(2);

        $this->storeManager->expects($this->once())
            ->method('getStores')
            ->willReturn([$store1, $store2]);

        /** @var WishlistItemCollection|MockObject $collection */
        $collection = $this->getMockBuilder(WishlistItemCollection::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'addWishlistFilter',
                'addStoreFilter',
                'setVisibilityFilter',
                'getItems'
            ])
            ->getMock();

        $this->collectionFactory->expects($this->once())
            ->method('create')
            ->willReturn($collection);

        $collection->expects($this->once())
            ->method('addWishlistFilter')
            ->with($wishlist)
            ->willReturnSelf();

        $collection->expects($this->once())
            ->method('addStoreFilter')
            ->with([1, 2])
            ->willReturnSelf();

        $collection->expects($this->once())
            ->method('setVisibilityFilter')
            ->willReturnSelf();

        $this->permissionsProcessor->expects($this->once())
            ->method('execute')
            ->with($collection);

        $item1 = $this->getMockBuilder(Item::class)
            ->disableOriginalConstructor()
            ->addMethods(['getDescription', 'getAddedAt'])
            ->onlyMethods(['getId', 'getData', 'getProduct'])
            ->getMock();
        $item1->method('getId')->willReturn(10);
        $item1->method('getData')->with('qty')->willReturn(3);
        $item1->method('getDescription')->willReturn('desc 1');
        $item1->method('getAddedAt')->willReturn('2025-10-31 10:00:00');
        $item1->method('getProduct')->willReturn('product_10');

        $item2 = $this->getMockBuilder(Item::class)
            ->disableOriginalConstructor()
            ->addMethods(['getDescription', 'getAddedAt'])
            ->onlyMethods(['getId', 'getData', 'getProduct'])
            ->getMock();
        $item2->method('getId')->willReturn(11);
        $item2->method('getData')->with('qty')->willReturn(1);
        $item2->method('getDescription')->willReturn('desc 2');
        $item2->method('getAddedAt')->willReturn('2025-10-31 11:00:00');
        $item2->method('getProduct')->willReturn('product_11');

        $collection->expects($this->once())
            ->method('getItems')
            ->willReturn([$item1, $item2]);

        $result = $this->resolver->resolve(
            $field,
            null,
            $info,
            ['model' => $wishlist],
            []
        );

        $this->assertSame(
            [
                [
                    'id' => 10,
                    'qty' => 3,
                    'description' => 'desc 1',
                    'added_at' => '2025-10-31 10:00:00',
                    'model' => 'product_10',
                ],
                [
                    'id' => 11,
                    'qty' => 1,
                    'description' => 'desc 2',
                    'added_at' => '2025-10-31 11:00:00',
                    'model' => 'product_11',
                ],
            ],
            $result
        );
    }
}
