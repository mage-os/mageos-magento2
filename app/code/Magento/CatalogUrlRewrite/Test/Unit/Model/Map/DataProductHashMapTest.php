<?php
/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogUrlRewrite\Test\Unit\Model\Map;

use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\CatalogUrlRewrite\Model\Map\DataCategoryHashMap;
use Magento\CatalogUrlRewrite\Model\Map\DataProductHashMap;
use Magento\CatalogUrlRewrite\Model\Map\HashMapPool;
use Magento\CatalogUrlRewrite\Test\Unit\Mock\ProductCollectionMock;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DataProductHashMapTest extends TestCase
{
    /** @var HashMapPool|MockObject */
    private $hashMapPoolMock;

    /** @var DataCategoryHashMap|MockObject */
    private $dataCategoryMapMock;

    /**
     * @var CollectionFactory|MockObject
     */
    private $collectionFactoryMock;

    /**
     * @var ProductCollection|MockObject
     */
    private $productCollectionMock;

    /** @var DataProductHashMap|MockObject */
    private $model;

    protected function setUp(): void
    {
        $this->hashMapPoolMock = $this->createMock(HashMapPool::class);
        $this->dataCategoryMapMock = $this->createMock(DataCategoryHashMap::class);
        $this->collectionFactoryMock = $this->createPartialMock(CollectionFactory::class, ['create']);
        $this->productCollectionMock = $this->getMockBuilder(ProductCollectionMock::class)
            ->onlyMethods(['getSelect', 'getConnection', 'getAllIds'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->collectionFactoryMock->method('create')->willReturn($this->productCollectionMock);

        $this->hashMapPoolMock->method('getDataMap')->willReturn($this->dataCategoryMapMock);

        $this->model = (new ObjectManager($this))->getObject(
            DataProductHashMap::class,
            [
                'collectionFactory' => $this->collectionFactoryMock,
                'hashMapPool' => $this->hashMapPoolMock
            ]
        );
    }

    /**
     * Tests getAllData, getData and resetData functionality
     */
    public function testGetAllData()
    {
        $productIds = ['1' => [1, 2, 3], '2' => [2, 3], '3' => 3];
        $productIdsOther = ['2' => [2, 3, 4]];

        $connectionMock = $this->createMock(AdapterInterface::class);
        $selectMock = $this->createMock(Select::class);

        $callCount = 0;
        $this->productCollectionMock->expects($this->exactly(3))
            ->method('getAllIds')
            ->willReturnCallback(function() use (&$callCount, $productIds, $productIdsOther) {
                $callCount++;
                if ($callCount === 1) {
                    return $productIds;
                } elseif ($callCount === 2) {
                    return $productIdsOther;
                } else {
                    return $productIds;
                }
            });
        $this->productCollectionMock->method('getConnection')->willReturn($connectionMock);
        $connectionMock->method('getTableName')->willReturnArgument(0);
        $this->productCollectionMock->method('getSelect')->willReturn($selectMock);
        $selectMock->expects($this->any())
            ->method('from')
            ->willReturnSelf();
        $selectMock->expects($this->any())
            ->method('joinInner')
            ->willReturnSelf();
        $selectMock->expects($this->any())
            ->method('where')
            ->willReturnSelf();
        $this->dataCategoryMapMock->method('getAllData')->willReturn([]);
        $this->hashMapPoolMock->expects($this->any())
            ->method('resetMap')
            ->with(DataCategoryHashMap::class, 1);
        $this->assertEquals($productIds, $this->model->getAllData(1));
        $this->assertEquals($productIds[2], $this->model->getData(1, 2));
        $this->assertEquals($productIdsOther, $this->model->getAllData(2));
        $this->assertEquals($productIdsOther[2], $this->model->getData(2, 2));
        $this->model->resetData(1);
        $this->assertEquals($productIds[2], $this->model->getData(1, 2));
        $this->assertEquals($productIds, $this->model->getAllData(1));
    }
}
