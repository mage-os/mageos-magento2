<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\ConfigurableImportExport\Test\Unit\Model\Export;

use PHPUnit\Framework\Attributes\DataProvider;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\CatalogImportExport\Model\Import\Product as ImportProduct;
use Magento\ConfigurableImportExport\Model\Export\RowCustomizer as ExportRowCustomizer;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableProductType;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\ImportExport\Model\Import;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RowCustomizerTest extends TestCase
{
    /**
     * @var ExportRowCustomizer
     */
    private $exportRowCustomizer;

    /**
     * @var ObjectManagerHelper
     */
    private $objectManagerHelper;

    /**
     * @var ProductCollection|MockObject
     */
    private $productCollectionMock;

    /**
     * @var ConfigurableProductType|MockObject
     */
    private $configurableProductTypeMock;

    /**
     * @var int
     */
    private static $productId = 11;

    protected function setUp(): void
    {
        $this->productCollectionMock = $this->createMock(ProductCollection::class);
        $this->configurableProductTypeMock = $this->createMock(ConfigurableProductType::class);

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->exportRowCustomizer = $this->objectManagerHelper->getObject(ExportRowCustomizer::class);
    }

    public function testAddHeaderColumns()
    {
        $this->initConfigurableData();

        $this->assertEquals(
            [
                'column_1',
                'column_2',
                'column_3',
                'configurable_variations',
                'configurable_variation_labels',
            ],
            $this->exportRowCustomizer->addHeaderColumns(['column_1', 'column_2', 'column_3'])
        );
    }

    /**
     * @param array $expected
     * @param array $data
     */
    #[DataProvider('addDataDataProvider')]
    public function testAddData(array $expected, array $data)
    {
        $this->initConfigurableData();

        $this->assertEquals($expected, $this->exportRowCustomizer->addData($data['data_row'], $data['product_id']));
    }

    /**
     * @return array
     */
    public static function addDataDataProvider()
    {
        $expectedConfigurableData = self::getExpectedConfigurableData();
        $data = $expectedConfigurableData[self::$productId];

        return [
            [
                'expected' => [
                    'key_1' => 'value_1',
                    'key_2' => 'value_2',
                    'key_3' => 'value_3'
                ],
                'data' => [
                    'data_row' => [
                        'key_1' => 'value_1',
                        'key_2' => 'value_2',
                        'key_3' => 'value_3'
                    ],
                    'product_id' => 1
                ]
            ],
            [
                'expected' => [
                    'key_1' => 'value_1',
                    'key_2' => 'value_2',
                    'key_3' => 'value_3',
                    'configurable_variations' => $data['configurable_variations'],
                    'configurable_variation_labels' => $data['configurable_variation_labels']
                ],
                'data' => [
                    'data_row' => [
                        'key_1' => 'value_1',
                        'key_2' => 'value_2',
                        'key_3' => 'value_3'
                    ],
                    'product_id' => self::$productId
                ]
            ]
        ];
    }

    /**
     * @param array $expected
     * @param array $data
     */
    #[DataProvider('getAdditionalRowsCountDataProvider')]
    public function testGetAdditionalRowsCount(array $expected, array $data)
    {
        $this->initConfigurableData();

        $this->assertEquals(
            $expected,
            $this->exportRowCustomizer->getAdditionalRowsCount($data['row_count'], $data['product_id'])
        );
    }

    /**
     * @return array
     */
    public static function getAdditionalRowsCountDataProvider()
    {
        return [
            [
                [1, 2, 3],
                [
                    'row_count' => [1, 2, 3],
                    'product_id' => 1
                ]
            ],
            [
                [1, 2, 3],
                [
                    'row_count' => [1, 2, 3],
                    'product_id' => 11
                ]
            ],
            [
                [],
                [
                    'row_count' => [],
                    'product_id' => 11
                ]
            ]
        ];
    }

    private function initConfigurableData()
    {
        $productIds = [1, 2, 3];
        $expectedConfigurableData = $this->getExpectedConfigurableData();
        $productMock = $this->createProductMock();

        $superAttributes = [
            $this->createSuperAttributeMock('code_of_attribute', 'Super attribute label'),
            $this->createSuperAttributeMock('code_of_attribute_2', 'Super attribute label 2')
        ];
        $childProducts = [
            $this->createChildProductMock(
                '_sku_',
                ['code_of_attribute' => 'Option Title', 'code_of_attribute_2' => 'Option Title 2']
            ),
            $this->createChildProductMock(
                '_sku_2',
                ['code_of_attribute' => 'Option Title A', 'code_of_attribute_2' => 'Option Title B']
            )
        ];

        $productMock->expects(static::any())
            ->method('getId')
            ->willReturn(self::$productId);
        $productMock->expects(static::any())
            ->method('getTypeInstance')
            ->willReturn($this->configurableProductTypeMock);
        $this->configurableProductTypeMock->expects(static::any())
            ->method('getUsedProductAttributes')
            ->with($productMock)
            ->willReturn($superAttributes);
        $this->configurableProductTypeMock->expects(static::any())
            ->method('getUsedProducts')
            ->with($productMock)
            ->willReturn($childProducts);
        $this->productCollectionMock->expects(static::atLeastOnce())
            ->method('addAttributeToFilter')
            ->willReturnMap(
                [
                    ['entity_id', ['in' => $productIds], 'inner', $this->productCollectionMock],
                    ['type_id', ['eq' => ConfigurableProductType::TYPE_CODE], 'inner', $this->productCollectionMock]
                ]
            );
        $this->productCollectionMock->expects(static::atLeastOnce())
            ->method('fetchItem')
            ->willReturnOnConsecutiveCalls($productMock, false);

        $this->exportRowCustomizer->prepareData($this->productCollectionMock, $productIds);
        $this->assertEquals(
            $expectedConfigurableData,
            $this->getPropertyValue($this->exportRowCustomizer, 'configurableData')
        );
    }

    /**
     * @param string $code
     * @param string $label
     * @return AbstractAttribute|MockObject
     */
    private function createSuperAttributeMock(string $code, string $label)
    {
        $attribute = $this->createMock(AbstractAttribute::class);
        $attribute->method('getAttributeCode')->willReturn($code);
        $attribute->method('getDefaultFrontendLabel')->willReturn($label);
        return $attribute;
    }

    /**
     * @param string $sku
     * @param array<string, string> $attributeTextMap
     * @return Product|MockObject
     */
    private function createChildProductMock(string $sku, array $attributeTextMap)
    {
        $product = $this->createMock(Product::class);
        $product->method('getSku')->willReturn($sku);
        $product->method('getAttributeText')
            ->willReturnCallback(
                static fn (string $code): string => $attributeTextMap[$code] ?? ''
            );
        return $product;
    }

    /**
     * Return expected configurable data
     *
     * @return array
     */
    private static function getExpectedConfigurableData()
    {
        return [
            self::$productId => [
                'configurable_variations' => implode(
                    ImportProduct::PSEUDO_MULTI_LINE_SEPARATOR,
                    [
                        '_sku_' => 'sku=_sku_' . Import::DEFAULT_GLOBAL_MULTI_VALUE_SEPARATOR
                            . implode(
                                Import::DEFAULT_GLOBAL_MULTI_VALUE_SEPARATOR,
                                ['code_of_attribute=Option Title', 'code_of_attribute_2=Option Title 2']
                            ),
                        '_sku_2' => 'sku=_sku_2' . Import::DEFAULT_GLOBAL_MULTI_VALUE_SEPARATOR
                            . implode(
                                Import::DEFAULT_GLOBAL_MULTI_VALUE_SEPARATOR,
                                ['code_of_attribute=Option Title A', 'code_of_attribute_2=Option Title B']
                            )
                    ]
                ),
                'configurable_variation_labels' => implode(
                    Import::DEFAULT_GLOBAL_MULTI_VALUE_SEPARATOR,
                    [
                        'code_of_attribute' => 'code_of_attribute=Super attribute label',
                        'code_of_attribute_2' => 'code_of_attribute_2=Super attribute label 2'
                    ]
                )
            ]
        ];
    }

    /**
     * Create product mock object
     *
     * @return Product|MockObject
     */
    private function createProductMock()
    {
        return $this->createMock(Product::class);
    }

    /**
     * Get value of protected property
     *
     * @param object $object
     * @param string $property
     * @return mixed
     */
    private function getPropertyValue($object, $property)
    {
        $reflection = new \ReflectionClass(get_class($object));
        $reflectionProperty = $reflection->getProperty($property);
        return $reflectionProperty->getValue($object);
    }
}
