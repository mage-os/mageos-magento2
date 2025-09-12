<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types = 1);

namespace Magento\Review\Test\Unit\Observer;

use Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Magento\Review\Model\ResourceModel\Review\Summary;
use Magento\Review\Model\ResourceModel\Review\SummaryFactory;
use Magento\Review\Observer\CatalogProductListCollectionAppendSummaryFieldsObserver;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Magento\Review\Observer\CatalogProductListCollectionAppendSummaryFieldsObserver
 */
class CatalogProductListCollectionAppendSummaryFieldsObserverTest extends TestCase
{
    private const STORE_ID = 1;

    /**
     * @var Event|MockObject
     */
    private $eventMock;

    /**
     * Testable Object
     *
     * @var CatalogProductListCollectionAppendSummaryFieldsObserver
     */
    private $observer;

    /**
     * @var Observer|MockObject
     */
    private $observerMock;

    /**
     * @var Collection|MockObject
     */
    private $productCollectionMock;

    /**
     * @var StoreInterface|MockObject
     */
    private $storeMock;

    /**
     * @var StoreManagerInterface|MockObject
     */
    private $storeManagerMock;

    /**
     * @var Summary|MockObject
     */
    private $sumResourceMock;

    /**
     * @var SummaryFactory|MockObject
     */
    private $sumResourceFactoryMock;

    /**
     * @inheritdoc
     */
    /**
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function setUp(): void
    {
        $this->eventMock = new class extends Event {
            /**
             * @var mixed
             */
            private $collection;
            public function __construct()
            {
            }
            public function getCollection()
            {
                return $this->collection;
            }
            public function setCollection($collection)
            {
                $this->collection = $collection;
            }
        };

        $this->observerMock = $this->createMock(Observer::class);

        $this->productCollectionMock = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->storeManagerMock = $this->createPartialMock(\Magento\Store\Model\StoreManager::class, ['getStore']);

        $this->storeMock = $this->createPartialMock(\Magento\Store\Model\Store::class, ['getId']);

        $this->sumResourceMock = $this->createPartialMock(
            Summary::class,
            ['appendSummaryFieldsToCollection']
        );

        $this->sumResourceFactoryMock = $this->getMockBuilder(SummaryFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['create'])
            ->getMock();

        $this->observer = new CatalogProductListCollectionAppendSummaryFieldsObserver(
            $this->sumResourceFactoryMock,
            $this->storeManagerMock
        );
    }

    /**
     * Product listing test
     */
    public function testAddSummaryFieldToProductsCollection() : void
    {
        $this->eventMock->setCollection($this->productCollectionMock);

        $this->observerMock
            ->expects($this->once())
            ->method('getEvent')
            ->willReturn($this->eventMock);

        $this->storeManagerMock
            ->expects($this->once())
            ->method('getStore')
            ->willReturn($this->storeMock);

        $this->storeMock
            ->expects($this->once())
            ->method('getId')
            ->willReturn(self::STORE_ID);

        $this->sumResourceFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->sumResourceMock);

        $this->sumResourceMock
            ->expects($this->once())
            ->method('appendSummaryFieldsToCollection')
            ->willReturn($this->sumResourceMock);

        $this->observer->execute($this->observerMock);
    }
}
