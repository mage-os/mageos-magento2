<?php

/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */

declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Model\ResourceModel\Collection;

use Magento\Catalog\Test\Unit\Model\ResourceModel\Collection\Stub\ConcreteCollection;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\EntityFactory as EavEntityFactory;
use Magento\Eav\Model\Entity\AbstractEntity;
use Magento\Eav\Model\ResourceModel\Helper;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactory;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Validator\UniversalFactory;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AbstractCollectionTest extends TestCase
{
    /**
     * @var ConcreteCollection
     */
    private ConcreteCollection $collection;

    /**
     * @var MockObject&StoreManagerInterface
     */
    private StoreManagerInterface $storeManagerMock;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $connectionMock = $this->createMock(AdapterInterface::class);
        $selectMock = $this->createMock(Select::class);
        $entityMock = $this->createMock(AbstractEntity::class);
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $universalFactory = $this->createMock(UniversalFactory::class);

        $universalFactory->method('create')->willReturn($entityMock);
        $entityMock->method('getConnection')->willReturn($connectionMock);
        $entityMock->method('getDefaultAttributes')->willReturn([]);
        $entityMock->method('getTable')->willReturnArgument(0);
        $entityMock->method('getEntityTable')->willReturn('catalog_product_entity');
        $entityMock->method('getTypeId')->willReturn(null);

        $connectionMock->method('select')->willReturn($selectMock);
        $selectMock->method('from')->willReturnSelf();
        $selectMock->method('join')->willReturnSelf();
        $selectMock->method('joinLeft')->willReturnSelf();
        $selectMock->method('where')->willReturnSelf();
        $selectMock->method('columns')->willReturnSelf();

        $storeMock = $this->createMock(Store::class);
        $storeMock->method('getId')->willReturn(1);
        $this->storeManagerMock->method('getStore')->willReturn($storeMock);

        $collection = $objectManager->getObject(
            ConcreteCollection::class,
            [
                'entityFactory'    => $this->createMock(EntityFactory::class),
                'logger'           => $this->createMock(LoggerInterface::class),
                'fetchStrategy'    => $this->createMock(FetchStrategyInterface::class),
                'eventManager'     => $this->createMock(ManagerInterface::class),
                'eavConfig'        => $this->createMock(Config::class),
                'resource'         => $this->createMock(ResourceConnection::class),
                'eavEntityFactory' => $this->createMock(EavEntityFactory::class),
                'resourceHelper'   => $this->createMock(Helper::class),
                'universalFactory' => $universalFactory,
                'storeManager'     => $this->storeManagerMock,
                'connection'       => $connectionMock,
            ]
        );
        /** @var ConcreteCollection $collection */
        $this->collection = $collection;
    }

    #[DataProvider('setStoreIdDataProvider')]
    public function testSetStoreIdAcceptsIntegerAndReturnsCollection(int $storeId): void
    {
        $result = $this->collection->setStoreId($storeId);

        $this->assertSame($this->collection, $result, 'setStoreId() must return $this for fluent interface');
        $this->assertSame($storeId, $this->collection->getStoreId());
    }

    /**
     * @return array<string, array<int>>
     */
    public static function setStoreIdDataProvider(): array
    {
        return [
            'default store' => [0],
            'store view 1'  => [1],
            'store view 5'  => [5],
        ];
    }

    public function testSetStoreIdCastsToInt(): void
    {
        $this->collection->setStoreId(3);

        $this->assertIsInt($this->collection->getStoreId());
        $this->assertSame(3, $this->collection->getStoreId());
    }

    public function testSetStoreIdAcceptsStoreInterface(): void
    {
        $storeMock = $this->createMock(StoreInterface::class);
        $storeMock->method('getId')->willReturn('2');

        $this->collection->setStoreId($storeMock);

        $this->assertSame(2, $this->collection->getStoreId());
    }

    public function testGetStoreIdFallsBackToStoreManagerWhenNotSet(): void
    {
        // Reset _storeId to null to force the lazy-load fallback path
        $reflection = new \ReflectionProperty($this->collection, '_storeId');
        $reflection->setValue($this->collection, null);

        // setUp configures storeManager->getStore()->getId() to return 1
        $this->assertSame(1, $this->collection->getStoreId());
    }

    public function testGetDefaultStoreIdReturnsZero(): void
    {
        $this->assertSame(Store::DEFAULT_STORE_ID, $this->collection->getDefaultStoreId());
        $this->assertSame(0, $this->collection->getDefaultStoreId());
    }

    public function testSetStoreDelegatesToStoreManager(): void
    {
        // setUp configures storeManager->getStore(any)->getId() to return 1
        $result = $this->collection->setStore('my_store_code');

        $this->assertSame($this->collection, $result, 'setStore() must return $this for fluent interface');
        $this->assertSame(1, $this->collection->getStoreId());
    }
}
