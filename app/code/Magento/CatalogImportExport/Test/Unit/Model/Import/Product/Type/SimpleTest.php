<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogImportExport\Test\Unit\Model\Import\Product\Type;

use PHPUnit\Framework\Attributes\DataProvider;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as AttributeCollectionFactory;
use Magento\CatalogImportExport\Model\Import\Product;
use Magento\CatalogImportExport\Model\Import\Product\RowValidatorInterface;
use Magento\CatalogImportExport\Model\Import\Product\Type\Simple;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection as AttributeCollection;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory as AttributeSetCollectionFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Adapter\Pdo\Mysql;
use Magento\Framework\DB\Select;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SimpleTest extends TestCase
{
    /**
     * @var Product|MockObject
     */
    private $entityModel;

    /**
     * @var Simple
     */
    private $simpleType;

    /**
     * @var ObjectManagerHelper
     */
    private $objectManagerHelper;

    /**
     * @var ResourceConnection|MockObject
     */
    private $resource;

    /**
     * @var AdapterInterface|MockObject
     */
    private $connection;

    /**
     * @var Select|MockObject
     */
    private $select;

    protected function setUp(): void
    {
        $this->entityModel = $this->createMock(Product::class);
        $attrSetColFactory = $this->createMock(AttributeSetCollectionFactory::class);
        $attrColFactory = $this->createMock(AttributeCollectionFactory::class);
        $attrCollection = $this->createMock(AttributeCollection::class);
        $attribute = new class extends Attribute {
            private $isVisible = false;
            private $isGlobal = false;
            private $isRequired = false;
            private $isUnique = false;
            private $frontendLabel = '';
            private $applyTo = [];
            private $defaultValue = '';
            private $usesSource = false;
            
            public function __construct() {}
            
            public function getAttributeCode() { return null; }
            public function getId() { return null; }
            public function getIsRequired() { return $this->isRequired; }
            public function getIsUnique() { return $this->isUnique; }
            public function isStatic() { return null; }
            public function getDefaultValue() { return $this->defaultValue; }
            public function usesSource() { return $this->usesSource; }
            public function getFrontendInput() { return null; }
            public function getIsVisible() { return $this->isVisible; }
            public function getApplyTo() { return $this->applyTo; }
            public function getIsGlobal() { return $this->isGlobal; }
            public function getFrontendLabel() { return $this->frontendLabel; }
            
            public function setIsVisible($value) { $this->isVisible = $value; return $this; }
            public function setIsGlobal($value) { $this->isGlobal = $value; return $this; }
            public function setIsRequired($value) { $this->isRequired = $value; return $this; }
            public function setIsUnique($value) { $this->isUnique = $value; return $this; }
            public function setFrontendLabel($value) { $this->frontendLabel = $value; return $this; }
            public function setApplyTo($value) { $this->applyTo = $value; return $this; }
            public function setDefaultValue($value) { $this->defaultValue = $value; return $this; }
            public function setUsesSource($value) { $this->usesSource = $value; return $this; }
        };
        // Set up the anonymous class methods to return expected values
        $attribute->setIsVisible(true);
        $attribute->setIsGlobal(true);
        $attribute->setIsRequired(true);
        $attribute->setIsUnique(true);
        $attribute->setFrontendLabel('frontend_label');
        $attribute->setApplyTo(['simple']);
        $attribute->setDefaultValue('default_value');
        $attribute->setUsesSource(true);
        $entityAttributes = [
            [
                'attribute_id' => '1',
                'attribute_set_name' => 'attributeSetName',
            ],
            [
                'attribute_id' => '2',
                'attribute_set_name' => 'attributeSetName'
            ],
            [
                'attribute_id' => '3',
                'attribute_set_name' => 'attributeSetName'
            ],
        ];
        $attribute1 = new class extends Attribute {
            private $id = '1';
            private $attributeCode = 'attr_code';
            private $frontendInput = 'multiselect';
            private $isStatic = true;
            
            public function __construct() {}
            
            public function getId() { return $this->id; }
            public function getAttributeCode() { return $this->attributeCode; }
            public function getFrontendInput() { return $this->frontendInput; }
            public function isStatic() { return $this->isStatic; }
            
            // Inherit other methods from parent
            public function getIsRequired() { return true; }
            public function getIsUnique() { return true; }
            public function getDefaultValue() { return 'default_value'; }
            public function usesSource() { return true; }
            public function getIsVisible() { return true; }
            public function getApplyTo() { return ['simple']; }
            public function getIsGlobal() { return true; }
            public function getFrontendLabel() { return 'frontend_label'; }
        };
        
        $attribute2 = new class extends Attribute {
            private $id = '2';
            private $attributeCode = 'boolean_attribute';
            private $frontendInput = 'boolean';
            private $isStatic = false;
            
            public function __construct() {}
            
            public function getId() { return $this->id; }
            public function getAttributeCode() { return $this->attributeCode; }
            public function getFrontendInput() { return $this->frontendInput; }
            public function isStatic() { return $this->isStatic; }
            
            // Inherit other methods from parent
            public function getIsRequired() { return true; }
            public function getIsUnique() { return true; }
            public function getDefaultValue() { return 'default_value'; }
            public function usesSource() { return true; }
            public function getIsVisible() { return true; }
            public function getApplyTo() { return ['simple']; }
            public function getIsGlobal() { return true; }
            public function getFrontendLabel() { return 'frontend_label'; }
        };
        
        $attribute3 = new class extends Attribute {
            private $id = '3';
            private $attributeCode = 'Text_attribute';
            private $frontendInput = 'text';
            private $isStatic = false;
            
            public function __construct() {}
            
            public function getId() { return $this->id; }
            public function getAttributeCode() { return $this->attributeCode; }
            public function getFrontendInput() { return $this->frontendInput; }
            public function isStatic() { return $this->isStatic; }
            
            // Inherit other methods from parent
            public function getIsRequired() { return true; }
            public function getIsUnique() { return true; }
            public function getDefaultValue() { return 'default_value'; }
            public function usesSource() { return true; }
            public function getIsVisible() { return true; }
            public function getApplyTo() { return ['simple']; }
            public function getIsGlobal() { return true; }
            public function getFrontendLabel() { return 'frontend_label'; }
        };
        $this->entityModel->method('getEntityTypeId')
            ->willReturn(3);
        $this->entityModel->method('getAttributeOptions')
            ->willReturnCallback(function () use (&$callCount) {
                $callCount++;
                if ($callCount === 1) {
                    return ['option1', 'option2'];
                } elseif ($callCount === 2) {
                    return ['yes' => 1, 'no' => 0];
                }
            });
        $attrColFactory->method('create')
            ->willReturn($attrCollection);
        $attrCollection->method('setAttributeSetFilter')
            ->willReturn([$attribute1, $attribute2, $attribute3]);
        $attrCollection->method('addFieldToFilter')
            ->willReturnSelf();
        $attrCollection->method('getItems')
            ->willReturnOnConsecutiveCalls([$attribute1, $attribute2, $attribute3], []);

        $this->connection = new class extends Mysql {
            private $selectResult = null;
            private $fetchAllResult = null;
            
            public function __construct() {}
            
            public function select() { return $this->selectResult; }
            public function fetchAll($sql = '', $bind = [], $fetchMode = null) { return $this->fetchAllResult; }
            public function fetchPairs($sql = '', $bind = []) { return null; }
            public function insertOnDuplicate($table, array $data, array $fields = []) { return $this; }
            public function delete($table, $where = '') { return $this; }
            public function quoteInto($text, $value, $type = null, $count = null) { return ''; }
            public function joinLeft($name, $cond, $cols = '*', $schema = null) { return $this; }
            
            public function setSelect($select) { $this->selectResult = $select; return $this; }
            public function setFetchAll($result) { $this->fetchAllResult = $result; return $this; }
        };
        $this->select = $this->createPartialMock(
            Select::class,
            [
                'from',
                'where',
                'joinLeft',
                'getConnection',
            ]
        );
        $this->select->method('from')
            ->willReturnSelf();
        $this->select->method('where')
            ->willReturnSelf();
        $this->select->method('joinLeft')
            ->willReturnSelf();
        // Set up the anonymous class methods to return expected values
        $this->connection->setSelect($this->select);
        $connection = $this->createMock(Mysql::class);
        $connection->method('quoteInto')
            ->willReturn('query');
        $this->select->method('getConnection')
            ->willReturn($connection);
        $this->connection->setFetchAll($entityAttributes);
        $this->resource = $this->createPartialMock(
            ResourceConnection::class,
            [
                'getConnection',
                'getTableName',
            ]
        );
        $this->resource->method('getConnection')
            ->willReturn($this->connection);
        $this->resource->method('getTableName')
            ->willReturn('tableName');
        // Create minimal ObjectManager mock
        $objectManagerMock = $this->createMock(\Magento\Framework\ObjectManagerInterface::class);
        \Magento\Framework\App\ObjectManager::setInstance($objectManagerMock);
        
        // Instantiate Simple class directly with dependencies
        $this->simpleType = new Simple(
            $attrSetColFactory,
            $attrColFactory,
            $this->resource,
            [$this->entityModel, 'simple']
        );
    }

    /**
     * Because AbstractType has static member variables,  we must clean them in between tests.
     * Luckily they are publicly accessible.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        Simple::$commonAttributesCache = [];
        Simple::$invAttributesCache = [];
        Simple::$attributeCodeToId = [];
    }

    #[DataProvider('addAttributeOptionDataProvider')]
    public function testAddAttributeOption($code, $optionKey, $optionValue, $initAttributes, $resultAttributes)
    {
        $this->setPropertyValue($this->simpleType, '_attributes', $initAttributes);

        $this->simpleType->addAttributeOption($code, $optionKey, $optionValue);

        $this->assertEquals($resultAttributes, $this->getPropertyValue($this->simpleType, '_attributes'));
    }

    public function testAddAttributeOptionReturn()
    {
        $code = 'attr set name value key';
        $optionKey = 'option key';
        $optionValue = 'option value';

        $result = $this->simpleType->addAttributeOption($code, $optionKey, $optionValue);

        $this->assertEquals($result, $this->simpleType);
    }

    public function testGetCustomFieldsMapping()
    {
        $expectedResult = ['value'];
        $this->setPropertyValue($this->simpleType, '_customFieldsMapping', $expectedResult);

        $result = $this->simpleType->getCustomFieldsMapping();

        $this->assertEquals($expectedResult, $result);
    }

    public function testIsRowValidSuccess()
    {
        $rowData = ['_attribute_set' => 'attribute_set_name'];
        $rowNum = 1;
        $this->entityModel->method('getRowScope')
            ->willReturn(null);
        $this->entityModel->expects($this->never())
            ->method('addRowError');
        $this->setPropertyValue(
            $this->simpleType,
            '_attributes',
            [
                $rowData[Product::COL_ATTR_SET] => [],
            ]
        );
        $this->assertTrue($this->simpleType->isRowValid($rowData, $rowNum));
    }

    public function testIsRowValidError()
    {
        $rowData = [
            '_attribute_set' => 'attribute_set_name',
            'sku' => 'sku',
            'attr_code' => 'test'
        ];
        $rowNum = 1;
        $this->entityModel->method('getRowScope')
            ->willReturn(1);
        $this->entityModel->method('addRowError')
            ->with(
                RowValidatorInterface::ERROR_VALUE_IS_REQUIRED,
                1,
                'attr_code'
            )
            ->willReturnSelf();
        $this->setPropertyValue(
            $this->simpleType,
            '_attributes',
            [
                $rowData[Product::COL_ATTR_SET] => [
                    'attr_code' => [
                        'is_required' => true,
                    ],
                ],
            ]
        );

        $this->assertFalse($this->simpleType->isRowValid($rowData, $rowNum));
    }

    /**
     * @return array
     */
    public static function addAttributeOptionDataProvider()
    {
        return [
            [
                'code' => 'attr set name value key',
                'optionKey' => 'option key',
                'optionValue' => 'option value',
                'initAttributes' => [
                    'attr set name' => [
                        'attr set name value key' => [],
                    ],
                ],
                'resultAttributes' => [
                    'attr set name' => [
                        'attr set name value key' => [
                            'options' => [
                                'option key' => 'option value'
                            ]
                        ]
                    ],
                ],
            ],
            [
                'code' => 'attr set name value key',
                'optionKey' => 'option key',
                'optionValue' => 'option value',
                'initAttributes' => [
                    'attr set name' => [
                        'not equal to code value' => [],
                    ],
                ],
                'resultAttributes' => [
                    'attr set name' => [
                        'not equal to code value' => [],
                    ],
                ]
            ],
        ];
    }

    /**
     * @param $object
     * @param $property
     * @return mixed
     */
    protected function getPropertyValue(&$object, $property)
    {
        $reflection = new \ReflectionClass(get_class($object));
        $reflectionProperty = $reflection->getProperty($property);
        $reflectionProperty->setAccessible(true);

        return $reflectionProperty->getValue($object);
    }

    /**
     * @param $object
     * @param $property
     * @param $value
     */
    protected function setPropertyValue(&$object, $property, $value)
    {
        $reflection = new \ReflectionClass(get_class($object));
        $reflectionProperty = $reflection->getProperty($property);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($object, $value);
        return $object;
    }

    public function testPrepareAttributesWithDefaultValueForSave()
    {
        $rowData = [
            '_attribute_set' => 'attributeSetName',
            'boolean_attribute' => 'Yes',
        ];
        $expected = [
            'boolean_attribute' => 1,
            'text_attribute' => 'default_value'
        ];
        $result = $this->simpleType->prepareAttributesWithDefaultValueForSave($rowData);
        $this->assertEquals($expected, $result);
    }
}
