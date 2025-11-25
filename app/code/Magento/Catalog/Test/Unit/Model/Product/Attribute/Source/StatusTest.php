<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Model\Product\Attribute\Source;

use PHPUnit\Framework\Attributes\DataProvider;
use Magento\Catalog\Model\Entity\Attribute;
use Magento\Catalog\Model\Product\Attribute\Backend\Sku;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Eav\Model\Entity\AbstractEntity;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\TestFramework\Unit\Helper\MockCreationTrait;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class StatusTest extends TestCase
{
    use MockCreationTrait;
    /** @var Status */
    protected $status;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var AbstractCollection|MockObject */
    protected $collection;

    /** @var AbstractAttribute|MockObject */
    protected $attributeModel;

    /** @var AbstractBackend|MockObject */
    protected $backendAttributeModel;

    /**
     * @var AbstractEntity|MockObject
     */
    protected $entity;

    protected function setUp(): void
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        
        $this->collection = $this->createPartialMockWithReflection(
            AbstractCollection::class,
            ['setStoreId', 'getStoreId', 'setCheckSql', 'getSelect', 'getConnection']
        );
        $collectionData = [];
        $collection = $this->collection;
        $this->collection->method('setStoreId')->willReturnCallback(function ($value) use (&$collectionData, $collection) {
            $collectionData['store_id'] = $value;
            return $collection;
        });
        $this->collection->method('getStoreId')->willReturnCallback(function () use (&$collectionData) {
            return $collectionData['store_id'] ?? null;
        });
        $this->collection->method('setCheckSql')->willReturnCallback(function ($value) use (&$collectionData, $collection) {
            $collectionData['check_sql'] = $value;
            return $collection;
        });
        $select = $this->createMock(\Magento\Framework\DB\Select::class);
        $select->method('joinLeft')->willReturnSelf();
        $this->collection->method('getSelect')->willReturn($select);
        $connection = $this->createMock(\Magento\Framework\DB\Adapter\AdapterInterface::class);
        $connection->method('getCheckSql')->willReturnCallback(function ($condition, $true, $false) {
            return "CASE WHEN $condition THEN $true ELSE $false END";
        });
        $this->collection->method('getConnection')->willReturn($connection);
        
        $this->attributeModel = $this->createPartialMockWithReflection(
            AbstractAttribute::class,
            ['setAttributeCode', 'getAttributeCode', 'setId', 'getId', 'setBackend', 'getBackend',
             'setIsScopeGlobal', 'isScopeGlobal', 'setEntity', 'getEntity']
        );
        $attrData = [];
        $attribute = $this->attributeModel;
        $this->attributeModel->method('setAttributeCode')->willReturnCallback(function ($value) use (&$attrData, $attribute) {
            $attrData['attribute_code'] = $value;
            return $attribute;
        });
        $this->attributeModel->method('getAttributeCode')->willReturnCallback(function () use (&$attrData) {
            return $attrData['attribute_code'] ?? null;
        });
        $this->attributeModel->method('setId')->willReturnCallback(function ($value) use (&$attrData, $attribute) {
            $attrData['id'] = $value;
            return $attribute;
        });
        $this->attributeModel->method('getId')->willReturnCallback(function () use (&$attrData) {
            return $attrData['id'] ?? null;
        });
        $this->attributeModel->method('setBackend')->willReturnCallback(function ($value) use (&$attrData, $attribute) {
            $attrData['backend'] = $value;
            return $attribute;
        });
        $this->attributeModel->method('getBackend')->willReturnCallback(function () use (&$attrData) {
            return $attrData['backend'] ?? null;
        });
        $this->attributeModel->method('setIsScopeGlobal')->willReturnCallback(function ($value) use (&$attrData, $attribute) {
            $attrData['is_scope_global'] = $value;
            return $attribute;
        });
        $this->attributeModel->method('isScopeGlobal')->willReturnCallback(function () use (&$attrData) {
            return $attrData['is_scope_global'] ?? false;
        });
        $this->attributeModel->method('setEntity')->willReturnCallback(function ($value) use (&$attrData, $attribute) {
            $attrData['entity'] = $value;
            return $attribute;
        });
        $this->attributeModel->method('getEntity')->willReturnCallback(function () use (&$attrData) {
            return $attrData['entity'] ?? null;
        });
        
        $this->backendAttributeModel = $this->createPartialMock(Sku::class, ['getTable']);
        
        $this->status = $this->objectManagerHelper->getObject(Status::class);

        $this->attributeModel->setAttributeCode('attribute_code');
        $this->attributeModel->setId('1');
        $this->attributeModel->setBackend($this->backendAttributeModel);
        $this->backendAttributeModel->method('getTable')->willReturn('table_name');

        $this->entity = $this->createMock(AbstractEntity::class);
    }

    public function testAddValueSortToCollectionGlobal()
    {
        $this->attributeModel->setIsScopeGlobal(true);
        $this->attributeModel->setEntity($this->entity);
        $this->entity->expects($this->once())->method('getLinkField')->willReturn('entity_id');

        $this->status->setAttribute($this->attributeModel);
        $this->status->addValueSortToCollection($this->collection);
    }

    public function testAddValueSortToCollectionNotGlobal()
    {
        $this->attributeModel->setIsScopeGlobal(false);
        $this->collection->setStoreId(1);
        $this->collection->setCheckSql('check_sql');
        $this->attributeModel->setEntity($this->entity);
        $this->entity->expects($this->once())->method('getLinkField')->willReturn('entity_id');

        $this->status->setAttribute($this->attributeModel);
        $this->status->addValueSortToCollection($this->collection);
    }

    public function testGetVisibleStatusIds()
    {
        $this->assertEquals([0 => 1], $this->status->getVisibleStatusIds());
    }

    public function testGetSaleableStatusIds()
    {
        $this->assertEquals([0 => 1], $this->status->getSaleableStatusIds());
    }

    public function testGetOptionArray()
    {
        $this->assertEquals([1 => 'Enabled', 2 => 'Disabled'], $this->status->getOptionArray());
    }

    /**
     * @param string $text
     * @param string $id
     */
    #[DataProvider('getOptionTextDataProvider')]
    public function testGetOptionText($text, $id)
    {
        $this->assertEquals($text, $this->status->getOptionText($id));
    }

    /**
     * @return array
     */
    public static function getOptionTextDataProvider()
    {
        return [
            [
                'text' => 'Enabled',
                'id' => '1',
            ],
            [
                'text' => 'Disabled',
                'id' => '2'
            ]
        ];
    }
}
