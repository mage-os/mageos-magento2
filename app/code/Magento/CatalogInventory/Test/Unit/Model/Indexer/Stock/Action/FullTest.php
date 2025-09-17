<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogInventory\Test\Unit\Model\Indexer\Stock\Action;

use Magento\Catalog\Model\Product\Type;
use Magento\CatalogInventory\Model\Indexer\Stock\Action\Full;
use Magento\CatalogInventory\Model\ResourceModel\Indexer\StockFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Exception\LocalizedException;

use PHPUnit\Framework\TestCase;

class FullTest extends TestCase
{
    public function testExecuteWithAdapterErrorThrowsException()
    {
        $indexerFactoryMock = $this->createMock(
            StockFactory::class
        );
        $resourceMock = $this->createMock(ResourceConnection::class);
        $productTypeMock = $this->createMock(Type::class);
        $connectionMock = $this->createMock(AdapterInterface::class);

        $productTypeMock
            ->method('getTypesByPriority')
            ->willReturn([]);

        $exceptionMessage = 'exception message';

        $resourceMock->method('getConnection')->willReturn($connectionMock);

        $resourceMock->expects($this->any())
            ->method('getTableName')
            ->willThrowException(new \Exception($exceptionMessage));

        // Create minimal ObjectManager mock and set it up first
        $objectManagerMock = $this->createMock(\Magento\Framework\ObjectManagerInterface::class);
        \Magento\Framework\App\ObjectManager::setInstance($objectManagerMock);

        // Create additional required mocks
        $cacheContextMock = $this->createMock(\Magento\Framework\Indexer\CacheContext::class);
        $eventManagerMock = $this->createMock(\Magento\Framework\Event\ManagerInterface::class);
        $metadataPoolMock = $this->createMock(\Magento\Framework\EntityManager\MetadataPool::class);
        $batchProviderMock = $this->createMock(\Magento\Framework\Indexer\BatchProviderInterface::class);
        $batchSizeManagementMock = $this->createMock(\Magento\Framework\Indexer\BatchSizeManagementInterface::class);
        $activeTableSwitcherMock = $this->createMock(
            \Magento\Catalog\Model\ResourceModel\Indexer\ActiveTableSwitcher::class
        );

        // Configure ObjectManager mock to return the required instances
        $objectManagerMock->method('get')
            ->willReturnMap([
                [\Magento\Framework\EntityManager\MetadataPool::class, $metadataPoolMock],
                [\Magento\Framework\Indexer\BatchProviderInterface::class, $batchProviderMock],
                [\Magento\Framework\Indexer\BatchSizeManagementInterface::class, $batchSizeManagementMock],
                [\Magento\Catalog\Model\ResourceModel\Indexer\ActiveTableSwitcher::class, $activeTableSwitcherMock]
            ]);

        // Direct instantiation instead of ObjectManagerHelper
        $model = new Full(
            $resourceMock,
            $indexerFactoryMock,
            $productTypeMock,
            $cacheContextMock,
            $eventManagerMock
        );

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage($exceptionMessage);

        $model->execute();
    }
}
