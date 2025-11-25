<?php
/**
 * Copyright 2016 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Ui\DataProvider\Product;

use PHPUnit\Framework\Attributes\DataProvider;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Ui\DataProvider\Product\ProductCustomOptionsDataProvider;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DB\Select as DbSelect;
use Magento\Framework\EntityManager\EntityMetadataInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Framework\TestFramework\Unit\Helper\MockCreationTrait;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ProductCustomOptionsDataProviderTest extends TestCase
{
    use MockCreationTrait;
    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var ProductCustomOptionsDataProvider
     */
    protected $dataProvider;

    /**
     * @var CollectionFactory|MockObject
     */
    protected $collectionFactoryMock;

    /**
     * @var RequestInterface|MockObject
     */
    protected $requestMock;

    /**
     * @var AbstractCollection|MockObject
     */
    protected $collectionMock;

    /**
     * @var DbSelect|MockObject
     */
    protected $dbSelectMock;

    /**
     * @var MetadataPool|MockObject
     */
    private $metadataPool;

    /**
     * @var EntityMetadataInterface|MockObject
     */
    private $entityMetadata;

    /**
     * @var PoolInterface|MockObject
     */
    private $modifiersPool;

    /**
     * @var array
     */
    private $collectionData = [];

    protected function setUp(): void
    {
        $this->collectionFactoryMock = $this->createPartialMock(CollectionFactory::class, ['create']);
        $this->requestMock = $this->createMock(RequestInterface::class);
        
        $this->collectionMock = $this->createPartialMockWithReflection(
            AbstractCollection::class,
            ['isLoaded', 'load', 'getSelect', 'getTable', 'getIterator', 'getData', 'getSize', 'toArray', 'setStoreId']
        );
        $this->collectionMock->method('isLoaded')->willReturnCallback(
            function () {
                return $this->collectionData['isLoaded'] ?? false;
            }
        );
        $this->collectionMock->method('load')->willReturnCallback(
            function () {
                $this->collectionData['isLoaded'] = true;
                return $this->collectionData['loadResult'] ?? $this->collectionMock;
            }
        );
        $this->collectionMock->method('getSelect')->willReturnCallback(
            function () {
                return $this->collectionData['select'] ?? null;
            }
        );
        $this->collectionMock->method('getTable')->willReturnCallback(
            function () {
                return $this->collectionData['table'] ?? null;
            }
        );
        $this->collectionMock->method('getIterator')->willReturnCallback(
            function () {
                return $this->collectionData['iterator'] ?? new \ArrayIterator([]);
            }
        );
        $this->collectionMock->method('getData')->willReturnCallback(
            function () {
                return $this->collectionData['arrayData'] ?? [];
            }
        );
        $this->collectionMock->method('getSize')->willReturnCallback(
            function () {
                return $this->collectionData['size'] ?? 0;
            }
        );
        $this->collectionMock->method('toArray')->willReturnCallback(
            function () {
                return $this->collectionData['arrayData'] ?? [];
            }
        );
        $this->collectionMock->method('setStoreId')->willReturnSelf();
        
        $this->dbSelectMock = $this->createMock(DbSelect::class);

        $this->collectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->collectionMock);

        $this->modifiersPool = $this->createMock(PoolInterface::class);
        $this->entityMetadata = $this->createMock(EntityMetadataInterface::class);
        $this->entityMetadata->method('getLinkField')->willReturn('entity_id');
        $this->metadataPool = $this->createPartialMock(MetadataPool::class, ['getMetadata']);
        $this->metadataPool->method('getMetadata')->willReturn($this->entityMetadata);

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->dataProvider = $this->objectManagerHelper->getObject(
            ProductCustomOptionsDataProvider::class,
            [
                'collectionFactory' => $this->collectionFactoryMock,
                'request' => $this->requestMock,
                'modifiersPool' => $this->modifiersPool,
                'metadataPool' => $this->metadataPool
            ]
        );
    }

    /**
     * @param int $amount
     * @param array $collectionArray
     * @param array $result
     */
    #[DataProvider('getDataDataProvider')]
    public function testGetDataCollectionIsLoaded($amount, array $collectionArray, array $result)
    {
        // load() is never called in this test

        $this->setCommonExpectations(true, $amount, $collectionArray);

        $this->assertSame($result, $this->dataProvider->getData());
    }

    /**
     * @param int $amount
     * @param array $collectionArray
     * @param array $result
     */
    #[DataProvider('getDataDataProvider')]
    public function testGetData($amount, array $collectionArray, array $result)
    {
        $tableName = 'catalog_product_option_table';

        $this->collectionData['isLoaded'] = false;
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('current_product_id', null)
            ->willReturn(0);
        $this->collectionData['select'] = $this->dbSelectMock;
        $this->dbSelectMock->expects($this->any())
            ->method('distinct')
            ->willReturnSelf();
        $this->collectionData['table'] = $tableName;
        $this->dbSelectMock->expects($this->once())
            ->method('join')
            ->with(['opt' => $tableName], 'opt.product_id = e.entity_id', null)
            ->willReturnSelf();
        $this->collectionData['loadResult'] = $this->collectionMock;
        $this->collectionData['iterator'] = new \ArrayIterator([]);

        $this->setCommonExpectations(false, $amount, $collectionArray);

        $this->assertSame($result, $this->dataProvider->getData());
    }

    /**
     * @return array
     */
    public static function getDataDataProvider()
    {
        return [
            0 => [
                'amount' => 2,
                'collectionArray' => [
                    '12' => ['id' => '12', 'value' => 'test1'],
                    '25' => ['id' => '25', 'value' => 'test2']
                ],
                'result' => [
                    'totalRecords' => 2,
                    'items' => [
                        ['id' => '12', 'value' => 'test1'],
                        ['id' => '25', 'value' => 'test2']
                    ]
                ]
            ]
        ];
    }

    /**
     * Set common expectations
     *
     * @param bool $isLoaded
     * @param int $amount
     * @param array $collectionArray
     * @return void
     */
    protected function setCommonExpectations($isLoaded, $amount, array $collectionArray)
    {
        $this->collectionData['isLoaded'] = $isLoaded;
        $this->collectionData['arrayData'] = $collectionArray;
        $this->collectionData['size'] = $amount;
    }
}
