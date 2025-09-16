<?php
/**
 * Copyright 2016 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogInventory\Test\Unit\Model\Config\Backend;

use PHPUnit\Framework\Attributes\DataProvider;
use Magento\CatalogInventory\Model\Config\Backend\Managestock;
use Magento\CatalogInventory\Model\Indexer\Stock\Processor;
use Magento\Framework\App\Config\ScopeConfigInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ManagestockTest extends TestCase
{
    /**
     * @var ScopeConfigInterface|MockObject
     */
    private $configMock;

    /** @var  Processor|MockObject */
    protected $stockIndexerProcessor;

    /** @var Managestock */
    protected $model;

    protected function setUp(): void
    {
        $this->stockIndexerProcessor = $this->createMock(Processor::class);
        $this->configMock = $this->createMock(ScopeConfigInterface::class);

        // Create all required mocks for the Managestock constructor
        $contextMock = $this->createMock(\Magento\Framework\Model\Context::class);
        $registryMock = $this->createMock(\Magento\Framework\Registry::class);
        $cacheTypeListMock = $this->createMock(\Magento\Framework\App\Cache\TypeListInterface::class);
        $stockIndexMock = $this->createMock(\Magento\CatalogInventory\Api\StockIndexInterface::class);

        // Configure context mock to return event dispatcher
        $eventDispatcherMock = $this->createMock(\Magento\Framework\Event\ManagerInterface::class);
        $contextMock->method('getEventDispatcher')->willReturn($eventDispatcherMock);

        // Direct instantiation instead of ObjectManagerHelper
        $this->model = new Managestock(
            $contextMock,
            $registryMock,
            $this->configMock,
            $cacheTypeListMock,
            $stockIndexMock,
            $this->stockIndexerProcessor
        );
    }

    /**
     * Data provider for testSaveAndRebuildIndex
     * @return array
     */
    public static function saveAndRebuildIndexDataProvider()
    {
        return [
            [1, 1],
            [0, 0],
        ];
    }

    /**
     *
     * @param int $newStockValue new value for stock status
     * @param int $callCount count matcher
     */
    #[DataProvider('saveAndRebuildIndexDataProvider')]
    public function testSaveAndRebuildIndex($newStockValue, $callCount)
    {
        $this->model->setValue($newStockValue);
        $this->stockIndexerProcessor->expects($this->exactly($callCount))->method('markIndexerAsInvalid');
        $this->configMock->method('getValue')->willReturn(0);   // old value for stock status

        $this->model->afterSave();
    }
}
