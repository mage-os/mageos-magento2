<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Elasticsearch\Test\Unit\Model\Adapter\FieldMapper\Product;

use Magento\Elasticsearch\Model\Adapter\FieldMapper\Product\AttributeAdapter;
use Magento\Framework\Api\CustomAttributesDataInterface;
use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @SuppressWarnings(PHPMD)
 */
class AttributeAdapterTest extends TestCase
{
    /**
     * @var AttributeAdapter
     */
    private $adapter;

    /**
     * @var AbstractExtensibleModel
     */
    private $attribute;

    /**
     * Set up test environment
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->attribute = new \Magento\Framework\Api\Test\Unit\Helper\CustomAttributesDataInterfaceTestHelper();

        $objectManager = new ObjectManagerHelper($this);

        $this->adapter = $objectManager->getObject(
            AttributeAdapter::class,
            [
                'attribute' => $this->attribute,
                'attributeCode' => 'code',
            ]
        );
    }

    /**
     */
    #[DataProvider('isFilterableProvider')]
    public function testIsFilterable($isFilterable, $isFilterableInSearch, $expected)
    {
        $this->attribute->setIsFilterable($isFilterable);
        $this->attribute->setIsFilterableInSearch($isFilterableInSearch);
        $this->assertEquals(
            $expected,
            $this->adapter->isFilterable()
        );
    }

    /**
     */
    #[DataProvider('isSearchableProvider')]
    public function testIsSearchable(
        $isSearchable,
        $isVisibleInAdvancedSearch,
        $isFilterable,
        $isFilterableInSearch,
        $expected
    ) {
        $this->attribute->setIsSearchable($isSearchable);
        $this->attribute->setIsVisibleInAdvancedSearch($isVisibleInAdvancedSearch);
        $this->attribute->setIsFilterable($isFilterable);
        $this->attribute->setIsFilterableInSearch($isFilterableInSearch);
        $this->assertEquals(
            $expected,
            $this->adapter->isSearchable()
        );
    }

    /**
     */
    #[DataProvider('isAlwaysIndexableProvider')]
    public function testIsAlwaysIndexable($expected)
    {
        $this->assertEquals(
            $expected,
            $this->adapter->isAlwaysIndexable()
        );
    }

    /**
     */
    #[DataProvider('isDateTimeTypeProvider')]
    public function testIsDateTimeType($backendType, $expected)
    {
        $this->attribute->setBackendType($backendType);
        $this->assertEquals(
            $expected,
            $this->adapter->isDateTimeType()
        );
    }

    /**
     */
    #[DataProvider('isFloatTypeProvider')]
    public function testIsFloatType($backendType, $expected)
    {
        $this->attribute->setBackendType($backendType);
        $this->assertEquals(
            $expected,
            $this->adapter->isFloatType()
        );
    }

    /**
     */
    #[DataProvider('isIntegerTypeProvider')]
    public function testIsIntegerType($backendType, $expected)
    {
        $this->attribute->setBackendType($backendType);
        $this->assertEquals(
            $expected,
            $this->adapter->isIntegerType()
        );
    }

    /**
     */
    #[DataProvider('isBooleanTypeProvider')]
    public function testIsBooleanType($frontendInput, $backendType, $expected)
    {
        $this->attribute->setBackendType($backendType);
        $this->attribute->setFrontendInput($frontendInput);
        $this->assertEquals(
            $expected,
            $this->adapter->isBooleanType()
        );
    }

    /**
     */
    #[DataProvider('isComplexTypeProvider')]
    public function testIsComplexType($frontendInput, $usesSource, $expected)
    {
        $this->attribute->setUsesSource($usesSource);
        $this->attribute->setFrontendInput($frontendInput);
        $this->assertEquals(
            $expected,
            $this->adapter->isComplexType()
        );
    }

    /**
     */
    #[DataProvider('isEavAttributeProvider')]
    public function testIsEavAttribute($expected)
    {
        $this->assertEquals(
            $expected,
            $this->adapter->isEavAttribute()
        );
    }

    /**
     * @return array
     */
    public static function isEavAttributeProvider()
    {
        return [
            [false],
        ];
    }

    /**
     * @return array
     */
    public static function isComplexTypeProvider()
    {
        return [
            ['select', true, true],
            ['multiselect', true, true],
            ['multiselect', false, true],
            ['int', false, false],
            ['int', true, true],
            ['boolean', true, false],
        ];
    }

    /**
     * @return array
     */
    public static function isBooleanTypeProvider()
    {
        return [
            ['select', 'int', true],
            ['boolean', 'int', true],
            ['boolean', 'varchar', false],
            ['select', 'varchar', false],
            ['int', 'varchar', false],
            ['int', 'int', false],
        ];
    }

    /**
     * @return array
     */
    public static function isIntegerTypeProvider()
    {
        return [
            ['smallint', true],
            ['int', true],
            ['string', false],
        ];
    }

    /**
     * @return array
     */
    public static function isFloatTypeProvider()
    {
        return [
            ['decimal', true],
            ['int', false],
        ];
    }

    /**
     * @return array
     */
    public static function isDateTimeTypeProvider()
    {
        return [
            ['timestamp', true],
            ['datetime', true],
            ['int', false],
        ];
    }

    /**
     * @return array
     */
    public static function isAlwaysIndexableProvider()
    {
        return [
            [false]
        ];
    }

    /**
     * @return array
     */
    public static function isSearchableProvider()
    {
        return [
            [true, false, false, false, true],
            [false, false, false, false, false],
            [false, true, false, false, true],
            [false, false, true, false, true],
            [true, true, true, false, true],
            [true, true, false, false, true],
        ];
    }

    /**
     * @return array
     */
    public static function isFilterableProvider()
    {
        return [
            [true, false, true],
            [true, false, true],
            [false, false, false]
        ];
    }

    /**
     * @return array
     */
    public static function isStringServiceFieldTypeProvider()
    {
        return [
            ['string', 'text', false],
            ['text', 'text', true]
        ];
    }

    /**
     * @return array
     */
    public static function getFieldNameProvider()
    {
        return [
            ['name', [], 'name']
        ];
    }

    /**
     * @return array
     */
    public static function getFieldTypeProvider()
    {
        return [
            ['type', 'type']
        ];
    }

    /**
     * @return array
     */
    public static function getFieldIndexProvider()
    {
        return [
            ['type', 'no', 'no']
        ];
    }
}
