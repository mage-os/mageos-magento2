<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Persistent\Test\Unit\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Persistent\Helper\Data;
use Magento\Persistent\Model\DeleteExpiredQuote;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Website;
use Magento\Store\Api\Data\StoreInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class DeleteExpiredQuoteTest extends TestCase
{
    /**
     * @var DeleteExpiredQuote
     */
    private DeleteExpiredQuote $model;

    /**
     * @var MockObject|StoreManagerInterface
     */
    private StoreManagerInterface|MockObject $storeManagerMock;

    /**
     * @var MockObject|ScopeConfigInterface
     */
    private ScopeConfigInterface|MockObject $scopeConfigMock;

    /**
     * @var MockObject|ResourceConnection
     */
    private ResourceConnection|MockObject $resourceConnectionMock;

    /**
     * @var MockObject|StoreInterface
     */
    private StoreInterface|MockObject $storeMock;

    protected function setUp(): void
    {
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);
        $this->resourceConnectionMock = $this->createMock(ResourceConnection::class);
        $this->storeMock = $this->createMock(StoreInterface::class);

        $this->model = new DeleteExpiredQuote(
            $this->storeManagerMock,
            $this->scopeConfigMock,
            $this->resourceConnectionMock
        );
    }

    /**
     * Test deleting expired quotes.
     */
    public function testDeleteExpiredQuote(): void
    {
        $websiteId = 1;
        $storeIds = [1, 2];
        $websiteMock = $this->createMock(Website::class);
        $websiteMock->method('getStoreIds')->willReturn($storeIds);
        $this->storeManagerMock->method('getWebsite')->with($websiteId)->willReturn($websiteMock);
        $storeMock = $this->createMock(\Magento\Store\Model\Store::class);
        $storeMock->method('getWebsiteId')->willReturn($websiteId);
        $this->storeManagerMock->method('getStore')->willReturn($storeMock);

        // Mock the scope config to return a specific lifetime
        $lifetime = 60; // 1 hour in seconds
        $this->scopeConfigMock->method('getValue')
            ->with(Data::XML_PATH_LIFE_TIME, 'website', $websiteId)
            ->willReturn($lifetime);

        // Mock the database connection
        $connectionMock = $this->createMock(\Magento\Framework\DB\Adapter\AdapterInterface::class);
        $this->resourceConnectionMock->method('getConnection')->willReturn($connectionMock);
        $connectionMock->expects($this->once())
            ->method('delete')
            ->with(
                $this->resourceConnectionMock->getTableName('quote'),
                $this->callback(function ($condition) use ($storeIds, $lifetime) {
                    // Validate the delete condition
                    $expiredBefore = gmdate('Y-m-d H:i:s', time() - $lifetime);
                    return isset($condition['store_id in (?)']) &&
                        $condition['store_id in (?)'] === $storeIds &&
                        isset($condition['updated_at < ?']) &&
                        $condition['updated_at < ?'] === $expiredBefore &&
                        isset($condition['is_persistent']) &&
                        $condition['is_persistent'] === 1;
                })
            );

        // Call the method to test
        $this->model->deleteExpiredQuote($websiteId);
    }

    /**
     * Test deleting expired quotes when no websiteId is provided.
     */
    public function testDeleteExpiredQuoteWithNullWebsiteId(): void
    {
        // Mock the store manager and store data
        $websiteId = 1;
        $storeIds = [1, 2];
        $websiteMock = $this->createMock(Website::class);
        $websiteMock->method('getStoreIds')->willReturn($storeIds);
        $this->storeManagerMock->method('getWebsite')->with($websiteId)->willReturn($websiteMock);
        $this->storeManagerMock->method('getStore')->willReturn($this->storeMock);
        $this->storeMock->method('getWebsiteId')->willReturn($websiteId);

        // Mock the scope config to return a specific lifetime
        $lifetime = 60; // 1 hour in seconds
        $this->scopeConfigMock->method('getValue')
            ->with(Data::XML_PATH_LIFE_TIME, 'website', $websiteId)
            ->willReturn($lifetime);

        // Mock the database connection
        $connectionMock = $this->createMock(\Magento\Framework\DB\Adapter\AdapterInterface::class);
        $this->resourceConnectionMock->method('getConnection')->willReturn($connectionMock);
        $connectionMock->expects($this->once())
            ->method('delete')
            ->with(
                $this->resourceConnectionMock->getTableName('quote'),
                $this->callback(function ($condition) use ($storeIds, $lifetime) {
                    // Validate the delete condition
                    $expiredBefore = gmdate('Y-m-d H:i:s', time() - $lifetime);
                    return isset($condition['store_id in (?)']) &&
                        $condition['store_id in (?)'] === $storeIds &&
                        isset($condition['updated_at < ?']) &&
                        $condition['updated_at < ?'] === $expiredBefore &&
                        isset($condition['is_persistent']) &&
                        $condition['is_persistent'] === 1;
                })
            );

        // Call the method to test with null websiteId
        $this->model->deleteExpiredQuote(null);
    }

    /**
     * Test when there is no lifetime configured.
     */
    public function testDeleteExpiredQuoteWithNoLifetime(): void
    {
        // Mock the store manager and store data
        $websiteId = 1;
        $storeIds = [1, 2];
        $websiteMock = $this->createMock(Website::class);
        $websiteMock->method('getStoreIds')->willReturn($storeIds);
        $this->storeManagerMock->method('getWebsite')->with($websiteId)->willReturn($websiteMock);

        // Mock the scope config to return no lifetime
        $this->scopeConfigMock->method('getValue')
            ->with(Data::XML_PATH_LIFE_TIME, 'website', $websiteId)
            ->willReturn(0); // No lifetime

        // Mock the database connection
        $connectionMock = $this->createMock(\Magento\Framework\DB\Adapter\AdapterInterface::class);
        $this->resourceConnectionMock->method('getConnection')->willReturn($connectionMock);
        $connectionMock->expects($this->never()) // No delete should be called
        ->method('delete');

        // Call the method to test with no lifetime
        $this->model->deleteExpiredQuote($websiteId);
    }
}
