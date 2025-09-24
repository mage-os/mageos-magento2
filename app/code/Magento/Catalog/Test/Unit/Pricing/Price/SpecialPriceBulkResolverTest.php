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
use Magento\Framework\EntityManager\EntityMetadataInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Session\SessionManagerInterface;
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
        $this->customerSession = new class implements SessionManagerInterface {
            private $customerGroupIdReturn = null;
            
            public function __construct()
            {
                // empty constructor
            }
            
            public function setCustomerGroupIdReturn($return)
            {
                $this->customerGroupIdReturn = $return;
                return $this;
            }
            
            public function getCustomerGroupId()
            {
                return $this->customerGroupIdReturn;
            }
            
            // Required SessionManagerInterface methods
            public function start()
            {
                return $this;
            }
            public function writeClose()
            {
            }
            public function isSessionExists()
            {
                return false;
            }
            public function getSessionId()
            {
                return '';
            }
            public function setName($name)
            {
                return $this;
            }
            public function getName()
            {
                return '';
            }
            public function destroy($options = null)
            {
            }
            public function regenerateId($destroyOldSession = true)
            {
                return $this;
            }
            public function expireSessionCookie()
            {
            }
            public function getCookieParams()
            {
                return [];
            }
            public function setCookieParams($params)
            {
                return $this;
            }
            public function getCookieLifetime()
            {
                return 0;
            }
            public function setCookieLifetime($lifetime)
            {
                return $this;
            }
            public function getCookiePath()
            {
                return '';
            }
            public function setCookiePath($path)
            {
                return $this;
            }
            public function getCookieDomain()
            {
                return '';
            }
            public function setCookieDomain($domain)
            {
                return $this;
            }
            public function getCookieSecure()
            {
                return false;
            }
            public function setCookieSecure($secure)
            {
                return $this;
            }
            public function getCookieHttpOnly()
            {
                return false;
            }
            public function setCookieHttpOnly($httpOnly)
            {
                return $this;
            }
            public function getUseCookies()
            {
                return false;
            }
            public function setUseCookies($flag)
            {
                return $this;
            }
            public function getUseOnlyCookies()
            {
                return false;
            }
            public function setUseOnlyCookies($flag)
            {
                return $this;
            }
            public function getRefererCheck()
            {
                return '';
            }
            public function setRefererCheck($domain)
            {
                return $this;
            }
            public function getHttpOnly()
            {
                return false;
            }
            public function setHttpOnly($httpOnly)
            {
                return $this;
            }
            public function getUseTransSid()
            {
                return false;
            }
            public function setUseTransSid($flag)
            {
                return $this;
            }
            public function getTransSidHosts()
            {
                return '';
            }
            public function setTransSidHosts($hosts)
            {
                return $this;
            }
            public function getTransSidTags()
            {
                return '';
            }
            public function setTransSidTags($tags)
            {
                return $this;
            }
            public function getCacheLimiter()
            {
                return '';
            }
            public function setCacheLimiter($cacheLimiter)
            {
                return $this;
            }
            public function getCacheExpire()
            {
                return 0;
            }
            public function setCacheExpire($lifetime)
            {
                return $this;
            }
            public function getUseStrictMode()
            {
                return false;
            }
            public function setUseStrictMode($flag)
            {
                return $this;
            }
            
            // Missing abstract methods
            public function clearStorage()
            {
            }
            public function setSessionId($sessionId)
            {
                return $this;
            }
            public function getSessionIdForHost($host)
            {
                return '';
            }
            public function setSessionIdForHost($host, $sessionId)
            {
                return $this;
            }
            public function isValidForHost($host)
            {
                return true;
            }
            public function isValidForPath($path)
            {
                return true;
            }
        };

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
        $connection = new class {
            private $fromValue = null;
            private $joinInnerValue = null;
            private $whereValue = null;
            private $columnsValue = null;
            private $joinLeftValue = null;
            private $fetchAllResult = [];
            
            public function __construct()
            {
            }
            
            public function from($table)
            {
                $this->fromValue = $table;
                return $this;
            }
            public function getFrom()
            {
                return $this->fromValue;
            }
            
            public function joinInner($table, $condition, $columns = '*')
            {
                $this->joinInnerValue = [$table, $condition, $columns];
                return $this;
            }
            public function getJoinInner()
            {
                return $this->joinInnerValue;
            }
            
            public function where($condition)
            {
                $this->whereValue = $condition;
                return $this;
            }
            public function getWhere()
            {
                return $this->whereValue;
            }
            
            public function columns($columns)
            {
                $this->columnsValue = $columns;
                return $this;
            }
            public function getColumns()
            {
                return $this->columnsValue;
            }
            
            public function joinLeft($table, $condition, $columns = '*')
            {
                $this->joinLeftValue = [$table, $condition, $columns];
                return $this;
            }
            public function getJoinLeft()
            {
                return $this->joinLeftValue;
            }
            
            public function setFetchAllResult($result)
            {
                $this->fetchAllResult = $result;
                return $this;
            }
            public function fetchAll()
            {
                return $this->fetchAllResult;
            }
            
            public function select()
            {
                return $this;
            }
        };
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
