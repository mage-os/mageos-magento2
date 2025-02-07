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
use PHPUnit\Framework\MockObject\Exception;
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

    /**
     * Test getExpiredPersistentQuotes method
     *
     * @return void
     * @throws Exception
     */
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

        $dbSelectMock->method('joinLeft')
            ->willReturnCallback(function ($table, $condition) use ($dbSelectMock) {
                static $callCount = 0;
                $callCount++;

                if ($callCount === 1) {
                    $this->assertEquals(['cl1' => 'customer_log'], $table);
                    $this->assertStringContainsString('cl1.customer_id = main_table.customer_id', $condition);
                    $this->assertStringContainsString('cl1.last_login_at < cl1.last_logout_at', $condition);
                    $this->assertStringContainsString('cl1.last_logout_at IS NOT NULL', $condition);
                }

                if ($callCount === 2) {
                    $this->assertEquals(['cl2' => 'customer_log'], $table);
                    $this->assertStringContainsString('cl2.customer_id = main_table.customer_id', $condition);
                    $this->assertStringContainsString('cl2.last_login_at <', $condition);
                    $this->assertStringContainsString(
                        'cl2.last_logout_at IS NULL OR cl2.last_login_at > cl2.last_logout_at',
                        $condition
                    );
                }

                return $dbSelectMock;
            });

        $quoteCollectionMock->method('addFieldToFilter')
            ->willReturnCallback(function ($field) use ($quoteCollectionMock) {
                static $filterCallCount = 0;
                $filterCallCount++;

                match ($filterCallCount) {
                    1 => $this->assertEquals('main_table.store_id', $field),
                    2 => $this->assertEquals('main_table.updated_at', $field),
                    3 => $this->assertEquals('main_table.is_persistent', $field),
                    4 => $this->assertEquals('main_table.entity_id', $field)
                };

                return $quoteCollectionMock;
            });

        $quoteCollectionMock->method('setOrder')
            ->with('entity_id', Collection::SORT_ORDER_ASC)
            ->willReturnSelf();

        $quoteCollectionMock->method('setPageSize')
            ->with($this->isType('integer'))
            ->willReturnSelf();

        $result = $this->model->getExpiredPersistentQuotes($storeMock, 0, 100);
        $this->assertSame($quoteCollectionMock, $result);
    }
}
