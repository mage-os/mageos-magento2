<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Model\Layer\Filter;

use PHPUnit\Framework\Attributes\DataProvider;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Filter\DataProvider\Price;
use Magento\Catalog\Model\Layer\Filter\DataProvider\PriceFactory;
use Magento\Catalog\Model\Layer\Filter\Dynamic\AlgorithmFactory;
use Magento\Catalog\Model\Layer\Filter\Dynamic\Auto;
use Magento\Catalog\Model\Layer\Filter\Item;
use Magento\Catalog\Model\Layer\Filter\Item\DataBuilder;
use Magento\Catalog\Model\Layer\Filter\ItemFactory;
use Magento\Catalog\Model\Layer\State;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Escaper;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Magento\Catalog\Model\Layer\Filter\Price
 * @SuppressWarnings(PHPMD.UnusedPrivateField)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PriceTest extends TestCase
{
    /**
     * @var DataBuilder|MockObject
     */
    private $itemDataBuilder;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Layer\Filter\Price|MockObject
     */
    private $resource;

    /**
     * @var Auto|MockObject
     */
    private $algorithm;

    /**
     * @var Layer|MockObject
     */
    private $layer;

    /**
     * @var Price|MockObject
     */
    private $dataProvider;

    /**
     * @var \Magento\Catalog\Model\Layer\Filter\Price
     */
    private $target;

    /**
     * @var RequestInterface|MockObject
     */
    private $request;

    /**
     * @var  ItemFactory|MockObject
     */
    private $filterItemFactory;

    /**
     * @var  State|MockObject
     */
    private $state;

    /**
     * @var  Attribute|MockObject
     */
    private $attribute;

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function setUp(): void
    {
        $this->request = $this->createMock(RequestInterface::class);

        $dataProviderFactory = $this->getMockBuilder(PriceFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['create'])
            ->getMock();

        $this->dataProvider = new class extends Price {
            private $priceId = null;
            private $price = null;
            private $resource = null;
            
            public function __construct()
            {
                // Don't call parent constructor to avoid dependencies
            }
            
            public function setPriceId($priceId)
            {
                $this->priceId = $priceId;
                return $this;
            }
            
            public function getPrice()
            {
                return $this->price;
            }
            
            public function setPrice($price)
            {
                $this->price = $price;
                return $this;
            }
            
            public function getResource()
            {
                return $this->resource;
            }
            
            public function setResource($resource)
            {
                $this->resource = $resource;
                return $this;
            }
        };
        $this->resource = $this->getMockBuilder(\Magento\Catalog\Model\ResourceModel\Layer\Filter\Price::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['applyPriceRange'])
            ->getMock();
        $this->dataProvider->setResource($this->resource);

        $dataProviderFactory->expects($this->once())
            ->method('create')
            ->willReturn($this->dataProvider);

        $this->layer = $this->getMockBuilder(Layer::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getState'])
            ->getMock();

        $this->state = $this->getMockBuilder(State::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['addFilter'])
            ->getMock();
        $this->layer->method('getState')->willReturn($this->state);

        $this->itemDataBuilder = $this->getMockBuilder(DataBuilder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['addItemData', 'build'])
            ->getMock();

        $this->filterItemFactory = $this->getMockBuilder(ItemFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['create'])
            ->getMock();

        $filterItem = new class extends Item {
            private $filter = null;
            private $label = null;
            private $value = null;
            private $count = null;
            
            public function __construct()
            {
                // Don't call parent constructor to avoid dependencies
            }
            
            public function setFilter($filter)
            {
                $this->filter = $filter;
                return $this;
            }
            
            public function setLabel($label)
            {
                $this->label = $label;
                return $this;
            }
            
            public function setValue($value)
            {
                $this->value = $value;
                return $this;
            }
            
            public function setCount($count)
            {
                $this->count = $count;
                return $this;
            }
        };
        $this->filterItemFactory->method('create')->willReturn($filterItem);

        $escaper = $this->getMockBuilder(Escaper::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['escapeHtml'])
            ->getMock();
        $escaper->expects($this->any())
            ->method('escapeHtml')
            ->willReturnArgument(0);

        $this->attribute = new class extends Attribute {
            private $isFilterable = null;
            private $attributeCode = null;
            private $frontend = null;
            
            public function __construct()
            {
                // Don't call parent constructor to avoid dependencies
            }
            
            public function getIsFilterable()
            {
                return $this->isFilterable;
            }
            
            public function setIsFilterable($isFilterable)
            {
                $this->isFilterable = $isFilterable;
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
            
            public function getFrontend()
            {
                return $this->frontend;
            }
            
            public function setFrontend($frontend)
            {
                $this->frontend = $frontend;
                return $this;
            }
        };
        $algorithmFactory = $this->getMockBuilder(AlgorithmFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['create'])
            ->getMock();

        $this->algorithm = $this->getMockBuilder(Auto::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getItemsData'])
            ->getMock();

        $algorithmFactory->method('create')->willReturn($this->algorithm);

        $objectManagerHelper = new ObjectManagerHelper($this);
        $this->target = $objectManagerHelper->getObject(
            \Magento\Catalog\Model\Layer\Filter\Price::class,
            [
                'dataProviderFactory' => $dataProviderFactory,
                'layer' => $this->layer,
                'itemDataBuilder' => $this->itemDataBuilder,
                'filterItemFactory' => $this->filterItemFactory,
                'escaper' => $escaper,
                'algorithmFactory' => $algorithmFactory
            ]
        );
    }

    /**
     * @param $requestValue
     * @param $idValue
     *
     * @return void
     */
    #[DataProvider('applyWithEmptyRequestDataProvider')]
    public function testApplyWithEmptyRequest($requestValue, $idValue): void
    {
        $requestField = 'test_request_var';
        $idField = 'id';

        $this->target->setRequestVar($requestField);

        $this->request
            ->method('getParam')
            ->with($requestField)
            ->willReturnCallback(
                function ($field) use ($requestField, $idField, $requestValue, $idValue) {
                    switch ($field) {
                        case $requestField:
                            return $requestValue;
                        case $idField:
                            return $idValue;
                    }
                }
            );

        $result = $this->target->apply($this->request);
        $this->assertSame($this->target, $result);
    }

    /**
     * @return array
     */
    public static function applyWithEmptyRequestDataProvider(): array
    {
        return [
            [
                'requestValue' => null,
                'idValue' => 0
            ],
            [
                'requestValue' => 0,
                'idValue' => false
            ],
            [
                'requestValue' => 0,
                'idValue' => null
            ]
        ];
    }

    /**
     * @return void
     */
    public function testApply(): void
    {
        $priceId = '15-50';
        $requestVar = 'test_request_var';

        $this->target->setRequestVar($requestVar);
        $this->request->expects($this->exactly(1))
            ->method('getParam')
            ->willReturnCallback(
                function ($field) use ($requestVar, $priceId) {
                    $this->assertContains($field, [$requestVar, 'id']);
                    return $priceId;
                }
            );

        $this->target->apply($this->request);
    }

    /**
     * @return void
     */
    public function testGetItems(): void
    {
        $this->target->setAttributeModel($this->attribute);
        $attributeCode = 'attributeCode';
        $this->attribute->setAttributeCode($attributeCode);
        $this->algorithm->method('getItemsData')->willReturn([]);
        $this->target->getItems();
    }
}
