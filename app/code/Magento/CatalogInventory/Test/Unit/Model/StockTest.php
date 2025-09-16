<?php
/**
 * Copyright 2016 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogInventory\Test\Unit\Model;

use PHPUnit\Framework\Attributes\DataProvider;
use Magento\CatalogInventory\Model\Stock;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class StockTest extends TestCase
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var ExtensionAttributesFactory
     */
    private $extensionFactory;

    /**
     * @var \Magento\Framework\Model\ExtensionAttributesFactory
     */
    private $customAttributeFactory;

    /**
     * @var AbstractResource
     */
    private $resource;

    /**
     * @var AbstractDb
     */
    private $resourceCollection;

    /**
     * @var MockObject
     */
    private $eventDispatcher;

    /**
     * @var Stock
     */
    private $stockModel;

    protected function setUp(): void
    {
        /** @var  MockObject */
        $this->eventDispatcher = $this->getMockBuilder(ManagerInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['dispatch'])
            ->getMock();

        $this->context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getEventDispatcher'])
            ->getMock();
        $this->context->method('getEventDispatcher')->willReturn($this->eventDispatcher);

        $this->registry = $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->extensionFactory = $this->getMockBuilder(ExtensionAttributesFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->customAttributeFactory = $this->getMockBuilder(AttributeValueFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Create anonymous class extending AbstractResource with dynamic methods
        $this->resource = new class extends AbstractResource {
            private $idFieldName = null;

            public function __construct() {
                // Skip parent constructor to avoid complex dependencies
            }

            protected function _construct() {
                // Required abstract method implementation
            }

            public function getIdFieldName() {
                return $this->idFieldName;
            }

            public function setIdFieldName($value) {
                $this->idFieldName = $value;
                return $this;
            }

            // Required AbstractResource methods - simplified implementations
            public function save(\Magento\Framework\Model\AbstractModel $object) { return $this; }
            public function delete(\Magento\Framework\Model\AbstractModel $object) { return $this; }
            public function load(\Magento\Framework\Model\AbstractModel $object, $value, $field = null) { return $this; }
            public function getConnection() { return null; }
            public function getTable($tableName) { return $tableName; }
            public function getMainTable() { return 'main_table'; }
            public function getTablePrefix() { return ''; }
        };

        $this->resourceCollection = $this->getMockBuilder(AbstractDb::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->stockModel = new Stock(
            $this->context,
            $this->registry,
            $this->extensionFactory,
            $this->customAttributeFactory,
            $this->resource,
            $this->resourceCollection
        );
    }

    /**
     * We want to ensure that property $_eventPrefix used during event dispatching
     *
     * @param $eventName
     * @param $methodName
     * @param $objectName
     */
    #[DataProvider('eventsDataProvider')]
    public function testDispatchEvents($eventName, $methodName, $objectName)
    {
        $isCalledWithRightPrefix = 0;
        $isObjectNameRight = 0;
        $this->eventDispatcher->expects($this->any())->method('dispatch')->with(
            $this->callback(function ($arg) use (&$isCalledWithRightPrefix, $eventName) {
                $isCalledWithRightPrefix |= ($arg === $eventName);
                return true;
            }),
            $this->callback(function ($data) use (&$isObjectNameRight, $objectName) {
                $isObjectNameRight |= isset($data[$objectName]);
                return true;
            })
        );

        $this->stockModel->$methodName();
        $this->assertTrue(
            ($isCalledWithRightPrefix && $isObjectNameRight),
            sprintf('Event "%s" with object name "%s" doesn\'t dispatched properly', $eventName, $objectName)
        );
    }

    /**
     * @return array
     */
    public static function eventsDataProvider()
    {
        return [
            ['cataloginventory_stock_save_before', 'beforeSave', 'stock'],
            ['cataloginventory_stock_save_after', 'afterSave', 'stock'],
        ];
    }
}
