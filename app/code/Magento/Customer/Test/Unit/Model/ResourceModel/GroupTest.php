<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Model\ResourceModel;

use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\Customer\Collection;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Customer\Model\ResourceModel\Group;
use Magento\Customer\Model\Vat;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Adapter\Pdo\Mysql;
use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Model\ResourceModel\Db\ObjectRelationProcessor;
use Magento\Framework\Model\ResourceModel\Db\TransactionManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\MockCreationTrait;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GroupTest extends TestCase
{
    use MockCreationTrait;

    /** @var Group */
    protected $groupResourceModel;

    /** @var ResourceConnection|MockObject */
    protected $resource;

    /** @var Vat|MockObject */
    protected $customerVat;

    /** @var \Magento\Customer\Model\Group|MockObject */
    protected $groupModel;

    /** @var MockObject */
    protected $customersFactory;

    /** @var MockObject */
    protected $groupManagement;

    /** @var MockObject */
    protected $relationProcessorMock;

    /**
     * @var Snapshot|MockObject
     */
    private $snapshotMock;

    /**
     * Setting up dependencies.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->resource = $this->createMock(ResourceConnection::class);
        $this->customerVat = $this->createMock(Vat::class);
        $this->customersFactory = $this->createPartialMock(
            CollectionFactory::class,
            ['create']
        );
        $this->groupManagement = $this->createPartialMock(
            GroupManagementInterface::class,
            ['isReadOnly', 'getDefaultGroup', 'getNotLoggedInGroup', 'getLoggedInGroups', 'getAllCustomersGroup']
        );

        $this->groupModel = $this->createMock(\Magento\Customer\Model\Group::class);

        $transactionManagerMock = $this->createMock(TransactionManagerInterface::class);
        $transactionManagerMock->expects($this->any())
            ->method('start')
            ->willReturn($this->createMock(AdapterInterface::class));
        
        $contextMock = $this->createMock(Context::class);
        $contextMock->expects($this->once())->method('getResources')->willReturn($this->resource);
        $contextMock->expects($this->once())
            ->method('getTransactionManager')
            ->willReturn($transactionManagerMock);

        $this->relationProcessorMock = $this->createMock(
            ObjectRelationProcessor::class
        );

        $this->snapshotMock = $this->createMock(
            Snapshot::class
        );

        $contextMock->expects($this->once())
            ->method('getObjectRelationProcessor')
            ->willReturn($this->relationProcessorMock);

        $this->groupResourceModel = (new ObjectManagerHelper($this))->getObject(
            Group::class,
            [
                'context' => $contextMock,
                'groupManagement' => $this->groupManagement,
                'customersFactory' => $this->customersFactory,
                'entitySnapshot' => $this->snapshotMock
            ]
        );
    }

    /**
     * Test for save() method when we try to save entity with system's reserved ID.
     * @return void
     */
    public function testSaveWithReservedId()
    {
        $expectedId = 55;
        $this->snapshotMock->expects($this->once())->method('isModified')->willReturn(true);
        $this->snapshotMock->expects($this->once())->method('registerSnapshot')->willReturnSelf();

        $this->groupModel->expects($this->any())->method('getId')
            ->willReturn(\Magento\Customer\Model\Group::CUST_GROUP_ALL);
        $this->groupModel->expects($this->any())->method('getData')
            ->willReturn([]);
        $this->groupModel->expects($this->any())->method('isSaveAllowed')
            ->willReturn(true);
        $this->groupModel->expects($this->any())->method('getStoredData')
            ->willReturn([]);
        $this->groupModel->expects($this->once())->method('setId')
            ->with($expectedId);
        $this->groupModel->expects($this->once())->method('getCode')
            ->willReturn('customer_group_code');

        // Create a logger mock first
        $loggerMock = $this->createPartialMockWithReflection(
            \Magento\Framework\DB\LoggerInterface::class,
            ['startTimer', 'logStats', 'log', 'critical']
        );
        $loggerMock->method('startTimer')->willReturn(null);
        $loggerMock->method('logStats')->willReturn(null);
        
        // Mock database adapter with all necessary methods to avoid DB connection
        $dbAdapter = $this->getMockBuilder(\Magento\Framework\DB\Adapter\Pdo\Mysql::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['describeTable', 'update', 'lastInsertId', 'select', '_connect', 'query',
                          'beginTransaction', 'commit', 'rollBack'])
            ->getMock();
        
        // Inject the logger mock using reflection
        $reflection = new \ReflectionClass($dbAdapter);
        $loggerProperty = $reflection->getProperty('logger');
        $loggerProperty->setAccessible(true);
        $loggerProperty->setValue($dbAdapter, $loggerMock);
        
        // Mock _transactionLevel to prevent real transactions
        $transactionLevelProperty = $reflection->getProperty('_transactionLevel');
        $transactionLevelProperty->setAccessible(true);
        $transactionLevelProperty->setValue($dbAdapter, 1); // Set to 1 to bypass DB connection in beginTransaction
        
        // Create a statement mock for query results
        $statementMock = $this->createPartialMockWithReflection(
            \Zend_Db_Statement_Interface::class,
            ['fetch', 'fetchAll', 'closeCursor', 'columnCount', 'errorCode', 'errorInfo',
             'execute', 'fetchColumn', 'fetchObject', 'getAttribute', 'nextRowset', 'rowCount',
             'setAttribute', 'setFetchMode', 'bindColumn', 'bindParam', 'bindValue']
        );
        $statementMock->method('fetch')->willReturn(false);
        $statementMock->method('fetchAll')->willReturn([]);
        $statementMock->method('bindColumn')->willReturn(true);
        $statementMock->method('bindParam')->willReturn(true);
        $statementMock->method('bindValue')->willReturn(true);
        
        $dbAdapter->expects($this->any())->method('describeTable')->willReturn(['customer_group_id' => []]);
        $dbAdapter->expects($this->any())->method('update')->willReturn(1);
        $dbAdapter->expects($this->any())->method('lastInsertId')->willReturn(1);
        $dbAdapter->expects($this->any())->method('_connect')->willReturn(null);
        $dbAdapter->expects($this->any())->method('query')->willReturn($statementMock);
        $dbAdapter->expects($this->any())->method('beginTransaction')->willReturn(true);
        $dbAdapter->expects($this->any())->method('commit')->willReturn(true);
        $dbAdapter->expects($this->any())->method('rollBack')->willReturn(true);
        $selectMock = $this->createMock(Select::class);
        $selectMock->expects($this->any())->method('from')->willReturnSelf();
        $dbAdapter->expects($this->any())->method('select')->willReturn($selectMock);
        $this->resource->expects($this->any())->method('getConnection')->willReturn($dbAdapter);

        $this->groupResourceModel->save($this->groupModel);
    }

    /**
     * Test for delete() method when we try to save entity with system's reserved ID.
     *
     * @return void
     */
    public function testDelete()
    {
        $dbAdapter = $this->createMock(AdapterInterface::class);
        $this->resource->expects($this->any())->method('getConnection')->willReturn($dbAdapter);

        $customer = $this->createPartialMockWithReflection(
            Customer::class,
            [
                'getStoreId',
                'setGroupId',
                '__wakeup',
                'load',
                'getId',
                'save'
            ]
        );
        $customerId = 1;
        $customer->expects($this->once())->method('getId')->willReturn($customerId);
        $customer->expects($this->once())->method('load')->with($customerId)->willReturnSelf();
        $defaultCustomerGroup = $this->createPartialMock(\Magento\Customer\Model\Group::class, ['getId']);
        $this->groupManagement->expects($this->once())->method('getDefaultGroup')
            ->willReturn($defaultCustomerGroup);
        $defaultCustomerGroup->expects($this->once())->method('getId')
            ->willReturn(1);
        $customer->expects($this->once())->method('setGroupId')->with(1);
        $customerCollection = $this->createMock(Collection::class);
        $customerCollection->expects($this->once())->method('addAttributeToFilter')->willReturnSelf();
        $customerCollection->expects($this->once())->method('load')->willReturn([$customer]);
        $this->customersFactory->expects($this->once())->method('create')
            ->willReturn($customerCollection);

        $this->relationProcessorMock->expects($this->once())->method('delete');
        $this->groupModel->expects($this->any())->method('getData')->willReturn(['data' => 'value']);
        $this->groupResourceModel->delete($this->groupModel);
    }
}
