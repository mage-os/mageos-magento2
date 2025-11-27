<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Model\Product\Type;

use PHPUnit\Framework\Attributes\DataProvider;
use Magento\Catalog\Model\Entity\Attribute;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Type\Simple;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\TestFramework\Unit\Helper\MockCreationTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AbstractTypeTest extends TestCase
{
    use MockCreationTrait;
    /**
     * @var ObjectManager
     */
    private $objectManagerHelper;

    /**
     * @var Simple|MockObject
     */
    private $model;

    /**
     * @var Product|MockObject
     */
    private $product;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product|MockObject
     */
    private $productResource;

    /**
     * @var Attribute|MockObject
     */
    private $attribute;

    protected function setUp(): void
    {
        $this->objectManagerHelper = new ObjectManager($this);
        $this->model = $this->objectManagerHelper->getObject(Simple::class);

        $this->product = $this->createPartialMockWithReflection(
            Product::class,
            [
                'setStatus', 'getStatus', 'setData', 'getData', 'setResource',
                'getResource', 'setHasOptions', 'getHasOptions'
            ]
        );
        $productData = [];
        $this->product->method('setStatus')->willReturnCallback(function ($v) use (&$productData) {
            $productData['status'] = $v;
            return $this->product;
        });
        $this->product->method('getStatus')->willReturnCallback(function () use (&$productData) {
            return $productData['status'] ?? null;
        });
        $this->product->method('setData')->willReturnCallback(function ($key, $value = null) use (&$productData) {
            if (is_array($key)) {
                $productData = array_merge($productData, $key);
            } else {
                $productData[$key] = $value;
            }
            return $this->product;
        });
        $this->product->method('getData')->willReturnCallback(function ($key = null) use (&$productData) {
            if ($key === null) {
                return $productData;
            }
            return $productData[$key] ?? null;
        });
        $this->product->method('setHasOptions')->willReturnCallback(function ($v) use (&$productData) {
            $productData['has_options'] = $v;
            return $this->product;
        });
        $this->product->method('getHasOptions')->willReturnCallback(function () use (&$productData) {
            return $productData['has_options'] ?? null;
        });
        
        $this->productResource = $this->createMock(\Magento\Catalog\Model\ResourceModel\Product::class);
        $this->product->method('setResource')->willReturnCallback(function ($v) use (&$productData) {
            $productData['resource'] = $v;
            return $this->product;
        });
        $this->product->method('getResource')->willReturnCallback(function () use (&$productData) {
            return $productData['resource'] ?? $this->productResource;
        });

        $this->product->setResource($this->productResource);

        $this->attribute = $this->createPartialMockWithReflection(
            Attribute::class,
            ['setId', 'getId', 'setSortPath', 'getSortPath', 'setGroupSortPath', 'getGroupSortPath']
        );
        $attributeData = [];
        $this->attribute->method('setId')->willReturnCallback(function ($v) use (&$attributeData) {
            $attributeData['id'] = $v;
            return $this->attribute;
        });
        $this->attribute->method('getId')->willReturnCallback(function () use (&$attributeData) {
            return $attributeData['id'] ?? null;
        });
        $this->attribute->method('setSortPath')->willReturnCallback(function ($v) use (&$attributeData) {
            $attributeData['sort_path'] = $v;
            return $this->attribute;
        });
        $this->attribute->method('getSortPath')->willReturnCallback(function () use (&$attributeData) {
            return $attributeData['sort_path'] ?? null;
        });
        $this->attribute->method('setGroupSortPath')->willReturnCallback(function ($v) use (&$attributeData) {
            $attributeData['group_sort_path'] = $v;
            return $this->attribute;
        });
        $this->attribute->method('getGroupSortPath')->willReturnCallback(function () use (&$attributeData) {
            return $attributeData['group_sort_path'] ?? null;
        });
    }

    public function testIsSalable()
    {
        $this->product->setStatus(Status::STATUS_ENABLED);
        $this->product->setData('is_salable', 3);
        $this->assertTrue($this->model->isSalable($this->product));
    }

    public function testGetAttributeById()
    {
        $this->productResource->method('loadAllAttributes')->willReturn(
            $this->productResource
        );
        $this->productResource->method('getSortedAttributes')->willReturn(
            [$this->attribute]
        );
        $this->attribute->setId(1);

        $this->assertEquals($this->attribute, $this->model->getAttributeById(1, $this->product));
        $this->assertNull($this->model->getAttributeById(0, $this->product));
    }

    #[DataProvider('attributeCompareProvider')]
    public function testAttributesCompare($attr1, $attr2, $expectedResult)
    {
        $attribute = $this->createPartialMockWithReflection(
            Attribute::class,
            ['setId', 'getId', 'setSortPath', 'getSortPath', 'setGroupSortPath', 'getGroupSortPath']
        );
        $attributeData = ['sort_path' => 1, 'group_sort_path' => $attr1];
        $attribute->method('setSortPath')->willReturnCallback(function ($v) use (&$attributeData, $attribute) {
            $attributeData['sort_path'] = $v;
            return $attribute;
        });
        $attribute->method('getSortPath')->willReturnCallback(function () use (&$attributeData) {
            return $attributeData['sort_path'] ?? null;
        });
        $attribute->method('setGroupSortPath')->willReturnCallback(function ($v) use (&$attributeData, $attribute) {
            $attributeData['group_sort_path'] = $v;
            return $attribute;
        });
        $attribute->method('getGroupSortPath')->willReturnCallback(function () use (&$attributeData) {
            return $attributeData['group_sort_path'] ?? null;
        });

        $attribute2 = $this->createPartialMockWithReflection(
            Attribute::class,
            ['setId', 'getId', 'setSortPath', 'getSortPath', 'setGroupSortPath', 'getGroupSortPath']
        );
        $attribute2Data = ['sort_path' => 1, 'group_sort_path' => $attr2];
        $attribute2->method('setSortPath')->willReturnCallback(function ($v) use (&$attribute2Data, $attribute2) {
            $attribute2Data['sort_path'] = $v;
            return $attribute2;
        });
        $attribute2->method('getSortPath')->willReturnCallback(function () use (&$attribute2Data) {
            return $attribute2Data['sort_path'] ?? null;
        });
        $attribute2->method('setGroupSortPath')->willReturnCallback(function ($v) use (&$attribute2Data, $attribute2) {
            $attribute2Data['group_sort_path'] = $v;
            return $attribute2;
        });
        $attribute2->method('getGroupSortPath')->willReturnCallback(function () use (&$attribute2Data) {
            return $attribute2Data['group_sort_path'] ?? null;
        });

        $this->assertEquals($expectedResult, $this->model->attributesCompare($attribute, $attribute2));
    }

    /**
     * @return array
     */
    public static function attributeCompareProvider()
    {
        return [
            [2, 2, 0],
            [2, 1, 1],
            [1, 2, -1]
        ];
    }

    public function testGetSetAttributes()
    {
        $this->productResource->expects($this->any())->method('loadAllAttributes')->willReturn(
            $this->productResource
        );
        $this->productResource->expects($this->any())->method('getSortedAttributes')->willReturn(5);
        $this->assertEquals(5, $this->model->getSetAttributes($this->product));
        //Call the method for a second time, the cached copy should be used
        $this->assertEquals(5, $this->model->getSetAttributes($this->product));
    }

    public function testHasOptions()
    {
        $this->product->setHasOptions(true);
        $this->assertTrue($this->model->hasOptions($this->product));
    }
}
