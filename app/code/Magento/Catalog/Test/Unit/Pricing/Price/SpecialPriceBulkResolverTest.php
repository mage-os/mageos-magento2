<?php
/**
 * Copyright 2023 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Pricing\Price;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Pricing\Price\SpecialPriceBulkResolver;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Test\Unit\Helper\SelectTestHelper;
use Magento\Framework\EntityManager\EntityMetadataInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Session\Test\Unit\Helper\SessionManagerTestHelper;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SpecialPriceBulkResolverTest extends TestCase
{
    /**
     * @var ResourceConnection|MockObject
     */
    private ResourceConnection $resource;

    /**
     * @var MetadataPool|MockObject
     */
    private MetadataPool $metadataPool;

    /**
     * @var SpecialPriceBulkResolver|MockObject
     */
    private SpecialPriceBulkResolver $specialPriceBulkResolver;

    /**
     * @var SessionManagerInterface|MockObject
     */
    private SessionManagerInterface $customerSession;

    /**
     * @var StoreManagerInterface|MockObject
     */
    private StoreManagerInterface $storeManager;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->resource = $this->createMock(ResourceConnection::class);
        $this->metadataPool = $this->createMock(MetadataPool::class);
        $this->storeManager = $this->createMock(StoreManagerInterface::class);
        $this->customerSession = new SessionManagerTestHelper();

        $this->specialPriceBulkResolver = new SpecialPriceBulkResolver(
            $this->resource,
            $this->metadataPool,
            $this->customerSession,
            $this->storeManager
        );
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testGenerateSpecialPriceMapNoCollection(): void
    {
        $this->assertEmpty($this->specialPriceBulkResolver->generateSpecialPriceMap(1, null));
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testGenerateSpecialPriceMapCollection(): void
    {
        $storeId = 2;
        $websiteId = 1;
        $customerGroupId = 3;
        $product = $this->createMock(Product::class);

        $this->customerSession->setCustomerGroupIdReturn(1);
        
        // Mock store and website
        $store = $this->createMock(StoreInterface::class);
        $store->method('getWebsiteId')->willReturn($websiteId);
        $this->storeManager->method('getStore')->willReturn($store);
        
        $collection = $this->getMockBuilder(AbstractCollection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAllIds', 'getIterator'])
            ->getMock();
        $collection->expects($this->once())->method('getAllIds')->willReturn([1]);
        $collection->method('getIterator')->willReturn(new \ArrayIterator([$product]));

        $metadata = $this->createMock(EntityMetadataInterface::class);
        $metadata->expects($this->exactly(2))->method('getLinkField')->willReturn('row_id');
        $this->metadataPool->expects($this->once())
            ->method('getMetadata')
            ->with(ProductInterface::class)
            ->willReturn($metadata);
        /** @var AdapterInterface $connection */
        $connection = new SelectTestHelper();
        // Set the expected values using setters
        $connection->from(['e' => 'catalog_product_entity']);
        $connection->where('e.entity_id IN (1)');
        $connection->columns([
            'link.product_id',
            '(price.final_price < price.price) AS hasSpecialPrice',
            'e.row_id AS identifier',
            'e.entity_id'
        ]);
        $connection->setFetchAllResult([
            [
                'product_id' => 2,
                'hasSpecialPrice' => 1,
                'identifier' => 2,
                'entity_id' => 1
            ]
        ]);
        $this->resource->expects($this->once())->method('getConnection')->willReturn($connection);
        $this->resource->expects($this->exactly(4))
            ->method('getTableName')
            ->willReturnOnConsecutiveCalls(
                'catalog_product_entity',
                'catalog_product_super_link',
                'catalog_product_website',
                'catalog_product_index_price'
            );
        $result = $this->specialPriceBulkResolver->generateSpecialPriceMap($storeId, $collection);
        $expectedResult = [1 => true];
        $this->assertEquals($expectedResult, $result);
    }
}
