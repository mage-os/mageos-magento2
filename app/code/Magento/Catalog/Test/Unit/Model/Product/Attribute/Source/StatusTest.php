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
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class StatusTest extends TestCase
{
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
        $this->collection = new class extends Collection {
            private $joinLeftResult = null;
            private $orderResult = null;
            private $checkSql = null;
            private $select = null;
            private $storeId = null;
            private $connection = null;
            
            public function __construct()
            {
                // Don't call parent constructor to avoid dependencies
            }
            
            public function joinLeft($table, $condition, $columns = '*')
            {
                return $this->joinLeftResult ?: $this;
            }
            
            public function setJoinLeftResult($result)
            {
                $this->joinLeftResult = $result;
                return $this;
            }
            
            public function order($field, $direction = 'ASC')
            {
                return $this->orderResult ?: $this;
            }
            
            public function setOrderResult($result)
            {
                $this->orderResult = $result;
                return $this;
            }
            
            public function getCheckSql($condition, $true, $false)
            {
                return $this->checkSql;
            }
            
            public function setCheckSql($checkSql)
            {
                $this->checkSql = $checkSql;
                return $this;
            }
            
            public function getSelect()
            {
                return $this->select ?: $this;
            }
            
            public function setSelect($select)
            {
                $this->select = $select;
                return $this;
            }
            
            public function getStoreId()
            {
                return $this->storeId;
            }
            
            public function setStoreId($storeId)
            {
                $this->storeId = $storeId;
                return $this;
            }
            
            public function getConnection()
            {
                return $this->connection ?: $this;
            }
            
            public function setConnection($connection)
            {
                $this->connection = $connection;
                return $this;
            }
        };
        $this->attributeModel = new class extends Attribute {
            private $isScopeGlobal = null;
            private $attribute = null;
            private $attributeCode = null;
            private $backend = null;
            private $id = null;
            private $entity = null;
            
            public function __construct()
            {
                // Don't call parent constructor to avoid dependencies
            }
            
            public function isScopeGlobal()
            {
                return $this->isScopeGlobal;
            }
            
            public function setIsScopeGlobal($isScopeGlobal)
            {
                $this->isScopeGlobal = $isScopeGlobal;
                return $this;
            }
            
            public function getAttribute()
            {
                return $this->attribute ?: $this;
            }
            
            public function setAttribute($attribute)
            {
                $this->attribute = $attribute;
                return $this;
            }
            
            public function getAttributeCode()
            {
                return $this->attributeCode;
            }
            
            public function setAttributeCode($attributeCode)
            {
                $this->attributeCode = $attributeCode;
                return $this;
            }
            
            public function getBackend()
            {
                return $this->backend;
            }
            
            public function setBackend($backend)
            {
                $this->backend = $backend;
                return $this;
            }
            
            public function getId()
            {
                return $this->id;
            }
            
            public function setId($id)
            {
                $this->id = $id;
                return $this;
            }
            
            public function getEntity()
            {
                return $this->entity;
            }
            
            public function setEntity($entity)
            {
                $this->entity = $entity;
                return $this;
            }
        };
        $this->backendAttributeModel = $this->createPartialMock(
            Sku::class,
            [ 'getTable']
        );
        $this->status = $this->objectManagerHelper->getObject(
            Status::class
        );

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
