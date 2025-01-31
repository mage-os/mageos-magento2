<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Persistent\Test\Unit\Model\ResourceModel;

use Magento\Persistent\Model\ResourceModel\ExpiredPersistentQuotesCollection;
use Magento\Quote\Model\ResourceModel\Quote\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\ResourceModel\Quote\Collection;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Persistent\Helper\Data;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\DB\Select;
use PHPUnit\Framework\TestCase;

class ExpiredPersistentQuotesCollectionTest extends TestCase
{
    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfigMock;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $quoteCollectionFactoryMock;

    /**
     * @var ExpiredPersistentQuotesCollection
     */
    private ExpiredPersistentQuotesCollection $model;

    protected function setUp(): void
    {
        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);
        $this->quoteCollectionFactoryMock = $this->createMock(CollectionFactory::class);

        $this->model = new ExpiredPersistentQuotesCollection(
            $this->scopeConfigMock,
            $this->quoteCollectionFactoryMock
        );
    }

    public function testGetExpiredPersistentQuotes(): void
    {
        $storeMock = $this->createMock(StoreInterface::class);
        $storeMock->method('getId')->willReturn(1);
        $storeMock->method('getWebsiteId')->willReturn(1);

        $this->scopeConfigMock->method('getValue')
            ->with(Data::XML_PATH_LIFE_TIME, ScopeInterface::SCOPE_WEBSITE, 1)
            ->willReturn(60);

        $quoteCollectionMock = $this->createMock(Collection::class);

        $this->quoteCollectionFactoryMock->method('create')->willReturn($quoteCollectionMock);

        $dbSelectMock = $this->createMock(Select::class);
        $quoteCollectionMock->method('getSelect')->willReturn($dbSelectMock);
        $quoteCollectionMock->method('getTable')->willReturn('customer_log');
        $dbSelectMock->expects($this->once())
            ->method('join')
            ->with(
                $this->equalTo(['cl' => 'customer_log']),
                $this->equalTo('cl.customer_id = main_table.customer_id'),
                $this->equalTo([])
            )
            ->willReturnSelf();
        $dbSelectMock->expects($this->once())
            ->method('where')
            ->with($this->equalTo('cl.last_logout_at > cl.last_login_at'))
            ->willReturnSelf();

        $quoteCollectionMock->expects($this->exactly(3))
            ->method('addFieldToFilter')
            ->with(
                $this->callback(
                    function ($field) {
                        return in_array(
                            $field,
                            ['main_table.store_id', 'main_table.updated_at', 'main_table.is_persistent']
                        );
                    }
                )
            );

        $result = $this->model->getExpiredPersistentQuotes($storeMock);

        $this->assertSame($quoteCollectionMock, $result);
    }
}
