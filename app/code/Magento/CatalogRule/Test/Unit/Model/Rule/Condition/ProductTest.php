<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogRule\Test\Unit\Model\Rule\Condition;

use Magento\Catalog\Model\Product as ProductModel;
use Magento\Catalog\Model\ProductCategoryList;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\CatalogRule\Model\Rule\Condition\Product;
use Magento\Eav\Model\Config;
use Magento\Framework\TestFramework\Unit\Helper\MockCreationTrait;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    use MockCreationTrait;

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
     * @var ProductModel|MockObject
     */
    protected $productModel;

    /**
     * @var ProductResource|MockObject
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
        $this->productModel = $this->createPartialMockWithReflection(
            ProductModel::class,
            [
                'addAttributeToSelect',
                'getAttributesByCode',
                '__wakeup',
                'hasData',
                'getData',
                'getId',
                'getStoreId',
                'getResource'
            ]
        );

        $this->productCategoryList = $this->createMock(ProductCategoryList::class);

        $this->productResource = $this->createPartialMock(
            ProductResource::class,
            [
                'loadAllAttributes',
                'getAttributesByCode',
                'getAttribute',
                'getConnection',
                'getTable'
            ]
        );

        $this->eavAttributeResource = $this->createPartialMockWithReflection(
            Attribute::class,
            [
                'getFrontendLabel',
                'getAttributesByCode',
                '__wakeup',
                'isAllowedForRuleCondition',
                'getDataUsingMethod',
                'getAttributeCode',
                'isScopeGlobal',
                'getBackendType',
                'getFrontendInput'
            ]
        );

        $this->productResource->expects($this->any())->method('loadAllAttributes')->willReturnSelf();
        $this->productResource->expects($this->any())->method('getAttributesByCode')
            ->willReturn([$this->eavAttributeResource]);
        $this->eavAttributeResource->expects($this->any())->method('isAllowedForRuleCondition')
            ->willReturn(false);
        $this->eavAttributeResource->expects($this->any())->method('getAttributesByCode')
            ->willReturn(false);
        $this->eavAttributeResource->expects($this->any())->method('getAttributeCode')
            ->willReturn('1');
        $this->eavAttributeResource->expects($this->any())->method('getFrontendLabel')
            ->willReturn('attribute_label');

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

        $this->config->expects($this->any())->method('getAttribute')
            ->willReturn($this->eavAttributeResource);

        $this->eavAttributeResource->expects($this->any())->method('isScopeGlobal')
            ->willReturn(false);
        $this->eavAttributeResource->expects($this->any())->method($input['method'])
            ->willReturn($input['type']);

        $this->productModel->expects($this->any())->method('hasData')
            ->willReturn(true);
        
        $callCount = 0;
        $this->productModel
            ->method('getData')
            ->willReturnCallback(function () use (&$callCount, $attributeValue, $newValue) {
                $callCount++;
                if ($callCount === 1) {
                    return ['1' => ['1' => $attributeValue]];
                }
                return $newValue;
            });
        
        $this->productModel->expects($this->any())->method('getId')
            ->willReturn('1');
        $this->productModel->expects($this->once())->method('getStoreId')
            ->willReturn('1');
        $this->productModel->expects($this->any())->method('getResource')
            ->willReturn($this->productResource);

        $this->productResource->expects($this->any())->method('getAttribute')
            ->willReturn($this->eavAttributeResource);

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

        $this->productModel->expects($this->once())
            ->method('getData')
            ->with('color')
            ->willReturn(null);
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
