<?php
/**
 * Copyright 2016 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogInventory\Test\Unit\Model\Indexer\Stock;

use PHPUnit\Framework\Attributes\DataProvider;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\CatalogInventory\Api\StockConfigurationInterface;
use Magento\CatalogInventory\Model\Indexer\Stock\CacheCleaner;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Indexer\CacheContext;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for CacheCleaner
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 *
 */
class CacheCleanerTest extends TestCase
{
    /**
     * @var CacheCleaner
     */
    private $unit;

    /**
     * @var ResourceConnection|MockObject
     */
    private $resourceMock;

    /**
     * @var AdapterInterface|MockObject
     */
    private $connectionMock;

    /**
     * @var ManagerInterface|MockObject
     */
    private $eventManagerMock;

    /**
     * @var CacheContext|MockObject
     */
    private $cacheContextMock;

    /**
     * @var MetadataPool|MockObject
     */
    private $metadataPoolMock;

    /**
     * @var StockConfigurationInterface|MockObject
     */
    private $stockConfigurationMock;

    /**
     * @var Select|MockObject
     */
    private $selectMock;

    protected function setUp(): void
    {
        $this->resourceMock = $this->getMockBuilder(ResourceConnection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->connectionMock = $this->getMockBuilder(AdapterInterface::class)
            ->getMock();
        // Create anonymous class for StockConfigurationInterface with getStockThresholdQty method
        $this->stockConfigurationMock = new class implements StockConfigurationInterface {
            /** @var float|null */
            private $stockThresholdQty = null;

            public function __construct()
            {
            }

            public function getStockThresholdQty()
            {
                return $this->stockThresholdQty;
            }

            public function setStockThresholdQty($stockThresholdQty)
            {
                $this->stockThresholdQty = $stockThresholdQty;
                return $this;
            }

            public function getDefaultScopeId()
            {
                return null;
            }

            public function getDefaultConfigValue($field, $storeId = null)
            {
                return null;
            }

            public function getManageStock($storeId = null)
            {
                return null;
            }

            public function getBackorders($storeId = null)
            {
                return null;
            }

            public function getMinQty($storeId = null)
            {
                return null;
            }

            public function getMinSaleQty($storeId = null, $customerGroupId = null)
            {
                return null;
            }

            public function getMaxSaleQty($storeId = null)
            {
                return null;
            }

            public function getNotifyStockQty($storeId = null)
            {
                return null;
            }

            public function getEnableQtyIncrements($storeId = null)
            {
                return null;
            }

            public function getQtyIncrements($store = null)
            {
                return null;
            }

            public function isShowOutOfStock($storeId = null)
            {
                return null;
            }

            public function isAutoReturnEnabled($storeId = null)
            {
                return null;
            }

            public function isDisplayProductStockStatus($storeId = null)
            {
                return null;
            }

            public function getItemOptions()
            {
                return null;
            }

            public function getIsQtyTypeIds($filter = null)
            {
                return null;
            }

            public function isQty($productTypeId)
            {
                return null;
            }

            public function canSubtractQty($storeId = null)
            {
                return null;
            }

            public function getCanBackInStock($storeId = null)
            {
                return null;
            }

            public function getConfigItemOptions()
            {
                return null;
            }
        };
        $this->cacheContextMock = $this->getMockBuilder(CacheContext::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->eventManagerMock = $this->getMockBuilder(ManagerInterface::class)
            ->getMock();

        // Create minimal ObjectManager mock and set it up first
        $objectManagerMock = $this->createMock(\Magento\Framework\ObjectManagerInterface::class);
        \Magento\Framework\App\ObjectManager::setInstance($objectManagerMock);

        // Create minimal mocks for MetadataPool constructor
        $sequenceFactoryMock = $this->createMock(\Magento\Framework\EntityManager\Sequence\SequenceFactory::class);
        
        // Create anonymous class for MetadataPool with getLinkField method
        $this->metadataPoolMock = new class($objectManagerMock, $sequenceFactoryMock) extends MetadataPool {
            /** @var string|null */
            private $linkField = null;
            /** @var mixed */
            protected $metadata = null;

            public function __construct($objectManager, $sequenceFactory)
            {
                parent::__construct($objectManager, $sequenceFactory, []);
            }

            public function getLinkField()
            {
                return $this->linkField;
            }

            public function setLinkField($linkField)
            {
                $this->linkField = $linkField;
                return $this;
            }

            public function getMetadata($entityType)
            {
                return $this->metadata;
            }

            public function setMetadata($metadata)
            {
                $this->metadata = $metadata;
                return $this;
            }
        };
        $this->selectMock = $this->getMockBuilder(Select::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->resourceMock->method('getConnection')->willReturn($this->connectionMock);

        // Direct instantiation instead of ObjectManagerHelper
        $this->unit = new CacheCleaner(
            $this->resourceMock,
            $this->stockConfigurationMock,
            $this->cacheContextMock,
            $this->eventManagerMock,
            $this->metadataPoolMock
        );
    }

    /**
     * Test clean cache by product ids and category ids
     *
     * @param bool $stockStatusBefore
     * @param bool $stockStatusAfter
     * @param int $qtyAfter
     * @param bool|int $stockThresholdQty
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    #[DataProvider('cleanDataProvider')]
    public function testClean($stockStatusBefore, $stockStatusAfter, $qtyAfter, $stockThresholdQty): void
    {
        $productId = 123;
        $categoryId = 3;
        $this->selectMock->expects($this->any())
            ->method('from')
            ->willReturnSelf();
        $this->selectMock->expects($this->any())
            ->method('joinLeft')
            ->willReturnSelf();
        $this->connectionMock->expects($this->exactly(3))
            ->method('select')
            ->willReturn($this->selectMock);
        $this->connectionMock->expects($this->exactly(2))
            ->method('fetchAll')
            ->willReturnOnConsecutiveCalls(
                [
                    ['product_id' => $productId, 'stock_status' => $stockStatusBefore],
                ],
                [
                    ['product_id' => $productId, 'stock_status' => $stockStatusAfter, 'qty' => $qtyAfter],
                ]
            );
        $this->connectionMock->expects($this->exactly(3))
            ->method('select')
            ->willReturn($this->selectMock);
        $this->selectMock->expects($this->exactly(7))
            ->method('where')
            ->willReturnCallback(function ($arg1, $arg2, $arg3) {
                if ($arg1 == 'product_id IN (?)') {
                    return $this->selectMock;
                } elseif ($arg1 == 'stock_id = ?') {
                    return $this->selectMock;
                } elseif ($arg1 == 'website_id = ?') {
                    return $this->selectMock;
                } elseif ($arg1 == 'product_id IN (?)') {
                    return $this->selectMock;
                } elseif ($arg1 == 'stock_id = ?') {
                    return $this->selectMock;
                } elseif ($arg1 == 'website_id = ?') {
                    return $this->selectMock;
                } elseif ($arg1 == 'product_id IN (?)' && $arg2 == [123] && $arg3 == \Zend_Db::INT_TYPE) {
                    return $this->selectMock;
                }
            });
        $this->connectionMock->expects($this->exactly(1))
            ->method('fetchCol')
            ->willReturn([$categoryId]);
        // Use setter instead of expects for the anonymous class
        $this->stockConfigurationMock->setStockThresholdQty($stockThresholdQty);
        $this->cacheContextMock->expects($this->exactly(2))
            ->method('registerEntities')
            ->willReturnCallback(function ($arg1, $arg2) use ($productId, $categoryId) {
                if ($arg1 == Product::CACHE_TAG && $arg2 == [$productId]) {
                    return null;
                } elseif ($arg1 == Category::CACHE_TAG && $arg2 == [$categoryId]) {
                    return null;
                }
            });
        $this->eventManagerMock->expects($this->exactly(2))
            ->method('dispatch')
            ->with('clean_cache_by_tags', ['object' => $this->cacheContextMock]);
        // Use setters instead of expects for the anonymous class
        $this->metadataPoolMock->setLinkField('row_id');
        $this->metadataPoolMock->setMetadata($this->metadataPoolMock);

        $callback = function () {
        };
        $this->unit->clean([], $callback);
    }

    /**
     * @return array
     */
    public static function cleanDataProvider(): array
    {
        return [
            [true, false, 1, false],
            [false, true, 1, false],
            [true, true, 1, 2],
            [false, false, 1, 2],
        ];
    }

    /**
     * @param bool $stockStatusBefore
     * @param bool $stockStatusAfter
     * @param int $qtyAfter
     * @param bool|int $stockThresholdQty
     * @return void
     */
    #[DataProvider('notCleanCacheDataProvider')]
    public function testNotCleanCache($stockStatusBefore, $stockStatusAfter, $qtyAfter, $stockThresholdQty): void
    {
        $productId = 123;
        $this->selectMock->expects($this->any())->method('from')
            ->willReturnSelf();
        $this->selectMock->expects($this->any())->method('where')
            ->willReturnSelf();
        $this->selectMock->expects($this->any())->method('joinLeft')
            ->willReturnSelf();
        $this->connectionMock->expects($this->exactly(2))
            ->method('select')
            ->willReturn($this->selectMock);
        $this->connectionMock->expects($this->exactly(2))
            ->method('fetchAll')
            ->willReturnOnConsecutiveCalls(
                [
                    ['product_id' => $productId, 'stock_status' => $stockStatusBefore],
                ],
                [
                    ['product_id' => $productId, 'stock_status' => $stockStatusAfter, 'qty' => $qtyAfter],
                ]
            );
        // Use setter instead of expects for the anonymous class
        $this->stockConfigurationMock->setStockThresholdQty($stockThresholdQty);
        $this->cacheContextMock->expects($this->never())
            ->method('registerEntities');
        $this->eventManagerMock->expects($this->never())
            ->method('dispatch');
        // Use setters instead of expects for the anonymous class
        $this->metadataPoolMock->setLinkField('row_id');
        $this->metadataPoolMock->setMetadata($this->metadataPoolMock);

        $callback = function () {
        };
        $this->unit->clean([], $callback);
    }

    /**
     * @return array
     */
    public static function notCleanCacheDataProvider(): array
    {
        return [
            [true, true, 1, false],
            [false, false, 1, false],
            [true, true, 3, 2],
        ];
    }
}
