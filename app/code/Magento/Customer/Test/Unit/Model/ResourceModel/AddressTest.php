<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Model\ResourceModel;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Address;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Model\ResourceModel\Address\DeleteRelation;
use Magento\Customer\Test\Unit\Helper\AddressResourceTestHelper;
use Magento\Customer\Test\Unit\Helper\AddressTestHelper;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Context;
use Magento\Eav\Model\ResourceModel\OrphanedMultiselectCleaner;
use Magento\Eav\Model\Entity\AbstractEntity;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;
use Magento\Eav\Model\Entity\Attribute\Set;
use Magento\Eav\Model\Entity\AttributeLoaderInterface;
use Magento\Eav\Model\Entity\Type;
use Magento\Eav\Model\ResourceModel\Helper;
use Magento\Framework\Locale\FormatInterface;
use Magento\Framework\Model\ResourceModel\Db\ObjectRelationProcessor;
use Magento\Framework\Model\ResourceModel\Db\TransactionManagerInterface;
use Magento\Framework\Validator\UniversalFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\Pdo\Mysql;
use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationComposite;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Framework\Validator;
use Magento\Framework\Validator\Factory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AddressTest extends TestCase
{
    /** @var AddressResourceTestHelper */
    protected $addressResource;

    /** @var Type */
    protected $eavConfigType;

    /** @var  Snapshot|MockObject */
    protected $entitySnapshotMock;

    /** @var  RelationComposite|MockObject */
    protected $entityRelationCompositeMock;

    protected function setUp(): void
    {
        $this->entitySnapshotMock = $this->createMock(Snapshot::class);
        $this->entityRelationCompositeMock = $this->createMock(RelationComposite::class);

        // Prepare dependencies
        $resourceConnection = $this->prepareResource();
        $eavConfig = $this->prepareEavConfig();
        $validatorFactory = $this->prepareValidatorFactory();
        
        // Create mocks for Context dependencies
        $objectRelationProcessor = $this->createMock(ObjectRelationProcessor::class);
        $transactionManager = $this->createMock(TransactionManagerInterface::class);
        
        // Create mocks for optional dependencies
        $orphanedMultiselectCleanerMock = $this->createMock(OrphanedMultiselectCleaner::class);
        $deleteRelationMock = $this->createMock(DeleteRelation::class);
        $customerRegistryMock = $this->createMock(CustomerRegistry::class);
        $customerRepositoryMock = $this->createMock(CustomerRepositoryInterface::class);

        // Create address resource using helper that bypasses parent constructor
        $this->addressResource = new AddressResourceTestHelper([
            '_resource' => $resourceConnection,
            'entitySnapshot' => $this->entitySnapshotMock,
            'entityRelationComposite' => $this->entityRelationCompositeMock,
            '_eavConfig' => $eavConfig,
            '_validatorFactory' => $validatorFactory,
            'customerRepository' => $customerRepositoryMock,
            'orphanedMultiselectCleaner' => $orphanedMultiselectCleanerMock,
            'deleteRelation' => $deleteRelationMock,
            'customerRegistry' => $customerRegistryMock,
            'objectRelationProcessor' => $objectRelationProcessor,
            'transactionManager' => $transactionManager
        ]);
    }

    /**
     * @param int|null $addressId
     * @param bool $isDefaultBilling
     * @param bool $isDefaultShipping
     */
    #[DataProvider('getSaveDataProvider')]
    public function testSave(?int $addressId, bool $isDefaultBilling, bool $isDefaultShipping): void
    {
        /** @var $address AddressTestHelper|\PHPUnit\Framework\MockObject\MockObject */
        $address = $this->createPartialMock(
            AddressTestHelper::class,
            [
                'getIsDefaultBilling',
                'getIsDefaultShipping',
                'getId',
                'getEntityTypeId',
                'hasDataChanges',
                'validateBeforeSave',
                'beforeSave',
                'afterSave',
                'isSaveAllowed'
            ]
        );
        $this->entitySnapshotMock->expects($this->once())->method('isModified')->willReturn(true);
        $this->entityRelationCompositeMock->expects($this->once())->method('processRelations');
        $address->expects($this->once())->method('isSaveAllowed')->willReturn(true);
        $address->expects($this->once())->method('validateBeforeSave');
        $address->expects($this->once())->method('beforeSave');
        $address->expects($this->once())->method('afterSave');
        $address->expects($this->any())->method('getEntityTypeId')->willReturn('3');
        $address->expects($this->any())->method('getId')->willReturn($addressId);
        $address->expects($this->any())->method('getIsDefaultShipping')->willReturn($isDefaultShipping);
        $address->expects($this->any())->method('getIsDefaultBilling')->willReturn($isDefaultBilling);
        $this->addressResource->setType('customer_address');

        $attributeLoaderMock = $this->createMock(AttributeLoaderInterface::class);

        $this->addressResource->setAttributeLoader($attributeLoaderMock);
        $this->addressResource->save($address);
    }

    /**
     * Data provider for testSave method
     *
     * @return array
     */
    public static function getSaveDataProvider(): array
    {
        return [
            [null, true, true],
            [1, true, true],
            [1, true, false],
            [1, false, true],
            [1, false, false],
        ];
    }

    /**
     * Prepare resource mock object
     *
     * @return ResourceConnection|MockObject
     */
    protected function prepareResource(): ResourceConnection|MockObject
    {
        $dbSelect = $this->createMock(Select::class);
        $dbSelect->expects($this->any())->method('from')->willReturnSelf();
        $dbSelect->expects($this->any())->method('where')->willReturnSelf();

        $dbAdapter = $this->createMock(Mysql::class);

        $dbAdapter->expects($this->any())
            ->method('describeTable')
            ->with('customer_address_entity')
            ->willReturn(
                [
                    'entity_type_id',
                    'attribute_set_id',
                    'created_at',
                    'updated_at',
                    'parent_id',
                    'increment_id',
                    'entity_id',
                ]
            );
        $dbAdapter->expects($this->any())->method('lastInsertId');
        $dbAdapter->expects($this->any())->method('select')->willReturn($dbSelect);

        $resource = $this->createMock(ResourceConnection::class);

        $resource->expects($this->any())->method('getConnection')->willReturn($dbAdapter);
        $resource->expects($this->any())->method('getTableName')->willReturn('customer_address_entity');

        return $resource;
    }

    /**
     * Prepare Eav config mock object
     *
     * @return Config|MockObject
     */
    protected function prepareEavConfig(): Config|MockObject
    {
        $attributeMock = $this->createPartialMock(
            AbstractAttribute::class,
            ['getAttributeCode', 'getBackend', '__wakeup']
        );
        $attributeMock->expects($this->any())
            ->method('getAttributeCode')
            ->willReturn('entity_id');
        $attributeMock->expects($this->any())
            ->method('getBackend')
            ->willReturn(
                $this->createMock(AbstractBackend::class)
            );

        $this->eavConfigType = $this->createPartialMock(
            Type::class,
            ['getEntityIdField', 'getId', 'getEntityTable', '__wakeup']
        );
        $this->eavConfigType->expects($this->any())->method('getEntityIdField')->willReturn(false);
        $this->eavConfigType->expects($this->any())->method('getId')->willReturn(false);
        $this->eavConfigType->expects($this->any())->method('getEntityTable')->willReturn('customer_address_entity');

        $eavConfig = $this->createPartialMock(
            Config::class,
            ['getEntityType', 'getEntityAttributeCodes', 'getAttribute']
        );
        $eavConfig->expects($this->any())
            ->method('getEntityType')
            ->with('customer_address')
            ->willReturn($this->eavConfigType);
        $eavConfig->expects($this->any())
            ->method('getEntityAttributeCodes')
            ->with($this->eavConfigType)
            ->willReturn(
                [
                    'entity_type_id',
                    'attribute_set_id',
                    'created_at',
                    'updated_at',
                    'parent_id',
                    'increment_id',
                    'entity_id',
                ]
            );
        $eavConfig->expects($this->any())
            ->method('getAttribute')
            ->willReturnMap(
                [
                    [$this->eavConfigType, 'entity_type_id', $attributeMock],
                    [$this->eavConfigType, 'attribute_set_id', $attributeMock],
                    [$this->eavConfigType, 'created_at', $attributeMock],
                    [$this->eavConfigType, 'updated_at', $attributeMock],
                    [$this->eavConfigType, 'parent_id', $attributeMock],
                    [$this->eavConfigType, 'increment_id', $attributeMock],
                    [$this->eavConfigType, 'entity_id', $attributeMock],
                ]
            );

        return $eavConfig;
    }

    /**
     * Prepare validator mock object
     *
     * @return Factory|MockObject
     */
    protected function prepareValidatorFactory(): Factory|MockObject
    {
        $validatorMock = $this->createPartialMock(Validator::class, ['isValid']);
        $validatorMock->expects($this->any())
            ->method('isValid')
            ->willReturn(true);

        $validatorFactory = $this->createPartialMock(Factory::class, ['createValidator']);
        $validatorFactory->expects($this->any())
            ->method('createValidator')
            ->with('customer_address', 'save')
            ->willReturn($validatorMock);

        return $validatorFactory;
    }

    public function testGetType(): void
    {
        $this->assertSame($this->eavConfigType, $this->addressResource->getEntityType());
    }
}
