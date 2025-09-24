<?php
/**
 * Copyright 2016 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ProductIdLocator;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\EntityManager\EntityMetadataInterface;
use Magento\Framework\EntityManager\MetadataPool;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for ProductIdLocator class.
 */
class ProductIdLocatorTest extends TestCase
{
    /**
     * @var int
     */
    private $idsLimit;

    /**
     * @var string
     */
    private $linkField;

    /**
     * @var Collection|MockObject
     */
    private $collection;

    /**
     * @var ProductIdLocator
     */
    private $model;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $metadataPool = $this->createMock(MetadataPool::class);
        $collectionFactory = $this->getMockBuilder(CollectionFactory::class)
            ->onlyMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->idsLimit = 4;

        $this->linkField = 'entity_id';
        $metaDataInterface = $this->createMock(EntityMetadataInterface::class);
        $metaDataInterface->method('getLinkField')
            ->willReturn($this->linkField);
        $metadataPool->method('getMetadata')
            ->with(ProductInterface::class)
            ->willReturn($metaDataInterface);

        $this->collection = $this->createMock(Collection::class);
        $collectionFactory->method('create')
            ->willReturn($this->collection);

        $this->model = new ProductIdLocator($metadataPool, $collectionFactory, $this->idsLimit);
    }

    public function testRetrieveProductIdsBySkus()
    {
        $skus = ['sku_1', 'sku_2'];

        // PHPUnit 12 compatible: Replace addMethods + onlyMethods with anonymous class for interface
        /** @var ProductInterface $product */
        $product = new class {
            private $skuResult;
            private $dataResult;
            private $typeIdResult;
            
            public function __construct()
            {
            }
            
            public function getSku()
            {
                return $this->skuResult;
            }
            
            public function setSku($result)
            {
                $this->skuResult = $result;
                return $this;
            }
            
            public function getData($field)
            {
                return $this->dataResult;
            }
            
            public function setData($result)
            {
                $this->dataResult = $result;
                return $this;
            }
            
            public function getTypeId()
            {
                return $this->typeIdResult;
            }
            
            public function setTypeId($result)
            {
                $this->typeIdResult = $result;
                return $this;
            }
        };
        
        $product->setSku('sku_1');
        $product->setData(1);
        $product->setTypeId('simple');

        $this->collection->expects($this->once())
            ->method('addFieldToFilter')
            ->with(ProductInterface::SKU, ['in' => $skus])
            ->willReturnSelf();
        $this->collection->expects($this->atLeastOnce())
            ->method('getItems')
            ->willReturn([$product]);
        $this->collection->expects($this->atLeastOnce())
            ->method('setPageSize')
            ->willReturnSelf();
        $this->collection->expects($this->atLeastOnce())
            ->method('getLastPageNumber')
            ->willReturn(1);
        $this->collection->expects($this->atLeastOnce())
            ->method('setCurPage')
            ->with(1)
            ->willReturnSelf();
        $this->collection->expects($this->atLeastOnce())
            ->method('clear')
            ->willReturnSelf();

        $this->assertEquals(
            ['sku_1' => [1 => 'simple']],
            $this->model->retrieveProductIdsBySkus($skus)
        );
    }

    public function testRetrieveProductIdsWithNumericSkus()
    {
        $skus = ['111', '222', '333', '444', '555'];
        $products = [];
        foreach ($skus as $sku) {
            // PHPUnit 12 compatible: Replace addMethods + onlyMethods with anonymous class for interface
            /** @var ProductInterface $product */
            $product = new class {
                private $skuResult;
                private $dataResult;
                private $typeIdResult;
                
                public function __construct()
                {
                }
                
                public function getSku()
                {
                    return $this->skuResult;
                }
                
                public function setSku($result)
                {
                    $this->skuResult = $result;
                    return $this;
                }
                
                public function getData($field)
                {
                    return $this->dataResult;
                }
                
                public function setData($result)
                {
                    $this->dataResult = $result;
                    return $this;
                }
                
                public function getTypeId()
                {
                    return $this->typeIdResult;
                }
                
                public function setTypeId($result)
                {
                    $this->typeIdResult = $result;
                    return $this;
                }
            };
            
            $product->setSku($sku);
            $product->setData((int) $sku);
            $product->setTypeId('simple');
            $products[] = $product;
        }

        $this->collection->expects($this->atLeastOnce())
            ->method('addFieldToFilter')
            ->willReturnCallback(
                function ($arg1, $arg2) use ($skus) {
                    if ($arg1 == ProductInterface::SKU && $arg2 == ['in' => $skus]) {
                        return null;
                    } elseif ($arg1 == ProductInterface::SKU && $arg2 == ['in' => ['1']]) {
                        return null;
                    }
                }
            );
        $this->collection->expects($this->atLeastOnce())
            ->method('getItems')
            ->willReturnOnConsecutiveCalls($products, []);
        $this->collection->expects($this->atLeastOnce())
            ->method('setPageSize')
            ->willReturnSelf();
        $this->collection->expects($this->atLeastOnce())
            ->method('getLastPageNumber')
            ->willReturn(1);
        $this->collection->expects($this->atLeastOnce())
            ->method('setCurPage')
            ->with(1)
            ->willReturnSelf();
        $this->collection->expects($this->atLeastOnce())
            ->method('clear')
            ->willReturnSelf();

        $this->assertEquals(
            [
                '111' => [111 => 'simple'],
                '222' => [222 => 'simple'],
                '333' => [333 => 'simple'],
                '444' => [444 => 'simple'],
                '555' => [555 => 'simple'],
            ],
            $this->model->retrieveProductIdsBySkus($skus)
        );
        $this->assertEmpty($this->model->retrieveProductIdsBySkus(['1']));
    }
}
