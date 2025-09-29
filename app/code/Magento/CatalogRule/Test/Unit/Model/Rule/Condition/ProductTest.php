<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogRule\Test\Unit\Model\Rule\Condition;

use PHPUnit\Framework\Attributes\DataProvider;
use Magento\Catalog\Model\ProductCategoryList;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\CatalogRule\Model\Rule\Condition\Product;
use Magento\Eav\Model\Config;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Catalog\Test\Unit\Helper\AttributeTestHelper;
use Magento\Catalog\Test\Unit\Helper\ProductTestHelperExtended;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class ProductTest extends TestCase
{
    /**
     * @var Product
     */
    protected $product;

    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var Config|MockObject
     */
    protected $config;

    /**
     * @var \Magento\Catalog\Model\Product|MockObject
     */
    protected $productModel;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product|MockObject
     */
    protected $productResource;

    /**
     * @var Attribute|MockObject
     */
    protected $eavAttributeResource;

    /**
     * @var ProductCategoryList|MockObject
     */
    private $productCategoryList;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->config = $this->createPartialMock(Config::class, ['getAttribute']);
        
        $this->productCategoryList = $this->getMockBuilder(ProductCategoryList::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->productResource = $this->getMockBuilder(\Magento\Catalog\Model\ResourceModel\Product::class)
            ->onlyMethods(
                [
                    'loadAllAttributes',
                    'getAttributesByCode',
                    'getAttribute',
                    'getConnection',
                    'getTable'
                ]
            )
            ->disableOriginalConstructor()
            ->getMock();

        $this->productModel = new ProductTestHelperExtended($this->productResource);

        $this->eavAttributeResource = new AttributeTestHelper();


        $this->productResource->expects($this->any())->method('loadAllAttributes')->willReturnSelf();
        $this->productResource->method('getAttributesByCode')->willReturn([$this->eavAttributeResource]);
        $this->eavAttributeResource->setScopeGlobal(false);
        $this->eavAttributeResource->setAttributesByCode(false);
        $this->eavAttributeResource->setAttributeCode('1');
        $this->eavAttributeResource->setFrontendLabel('attribute_label');

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->product = $this->objectManagerHelper->getObject(
            Product::class,
            [
                'config' => $this->config,
                'product' => $this->productModel,
                'productResource' => $this->productResource,
                'productCategoryList' => $this->productCategoryList
            ]
        );
    }

    /**
     * @return void
     */
    public function testValidateMeetsCategory(): void
    {
        $categoryIdList = [1, 2, 3];

        $this->productCategoryList->method('getCategoryIds')->willReturn($categoryIdList);
        $this->product->setData('attribute', 'category_ids');
        $this->product->setData('value_parsed', '1');
        $this->product->setData('operator', '{}');

        $this->assertTrue($this->product->validate($this->productModel));
    }

    /**
     * @param string $attributeValue
     * @param string|array $parsedValue
     * @param string $newValue
     * @param string $operator
     * @param array $input
     *
     * @return void
     */
    #[DataProvider('validateDataProvider')]
    public function testValidateWithDatetimeValue($attributeValue, $parsedValue, $newValue, $operator, $input): void
    {
        $this->product->setData('attribute', 'attribute_key');
        $this->product->setData('value_parsed', $parsedValue);
        $this->product->setData('operator', $operator);

        $this->config->method('getAttribute')->willReturn($this->eavAttributeResource);

        $this->eavAttributeResource->setScopeGlobal(false);
        // Set the method value based on the input
        if ($input['method'] === 'getBackendType') {
            $this->eavAttributeResource->setBackendType($input['type']);
        } elseif ($input['method'] === 'getFrontendInput') {
            $this->eavAttributeResource->setFrontendInput($input['type']);
        }

        // Set the data values for consecutive calls like the original mock
        $this->productModel->setDataValues([
            ['1' => ['1' => $attributeValue]],  // First call
            $newValue,                          // Second call
            $newValue                           // Third call
        ]);

        $this->productResource->method('getAttribute')->willReturn($this->eavAttributeResource);

        $this->product->collectValidatedAttributes($this->productModel);
        $this->assertTrue($this->product->validate($this->productModel));
    }

    /**
     * @return void
     */
    public function testValidateWithNoValue(): void
    {
        $this->product->setData('attribute', 'color');
        $this->product->setData('value_parsed', '1');
        $this->product->setData('operator', '!=');

        // Set the data directly on the TestHelper class
        $this->productModel->setAttributesByCode(['color' => null]);
        $this->assertFalse($this->product->validate($this->productModel));
    }

    /**
     * @return array
     */
    public static function validateDataProvider(): array
    {
        return [
            [
                'attributeValue' => '12:12',
                'parsedValue' => '12:12',
                'newValue' => '12:13',
                'operator' => '>=',
                'input' => ['method' => 'getBackendType', 'type' => 'input_type']
            ],
            [
                'attributeValue' => '1',
                'parsedValue' => '1',
                'newValue' => '2',
                'operator' => '>=',
                'input' => ['method' => 'getBackendType', 'type' => 'input_type']
            ],
            [
                'attributeValue' => '1',
                'parsedValue' => ['1' => '0'],
                'newValue' => ['1' => '1'],
                'operator' => '!()',
                'input' => ['method' => 'getFrontendInput', 'type' => 'multiselect']
            ]
        ];
    }
}
