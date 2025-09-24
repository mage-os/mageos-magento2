<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Model;

use PHPUnit\Framework\Attributes\DataProvider;
use Magento\Catalog\Api\Data\CustomOptionInterface;
use Magento\Catalog\Api\Data\ProductOptionExtensionInterface;
use Magento\Catalog\Api\Data\ProductOptionInterface;
use Magento\Catalog\Model\CustomOptions\CustomOption;
use Magento\Catalog\Model\CustomOptions\CustomOptionFactory;
use Magento\Catalog\Model\Product\Option\UrlBuilder;
use Magento\Catalog\Model\ProductOptionProcessor;
use Magento\Framework\DataObject;
use Magento\Framework\DataObject\Factory as DataObjectFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ProductOptionProcessorTest extends TestCase
{
    /**
     * @var ProductOptionProcessor
     */
    protected $processor;

    /**
     * @var DataObject|MockObject
     */
    protected $dataObject;

    /**
     * @var DataObjectFactory|MockObject
     */
    protected $dataObjectFactory;

    /**
     * @var CustomOptionFactory|MockObject
     */
    protected $customOptionFactory;

    /**
     * @var CustomOptionInterface|MockObject
     */
    protected $customOption;

    protected function setUp(): void
    {
        // PHPUnit 12 compatible: Replace addMethods + onlyMethods with anonymous class for concrete class
        $this->dataObject = new class extends DataObject {
            private $optionsResult;
            
            public function __construct()
            {
            }
            
            public function getOptions()
            {
                return $this->optionsResult;
            }
            
            public function setOptions($result)
            {
                $this->optionsResult = $result;
                return $this;
            }
            
            public function addData($data)
            {
                return $this;
            }
        };

        $this->dataObjectFactory = $this->getMockBuilder(DataObjectFactory::class)
            ->onlyMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->dataObjectFactory->method('create')->willReturn($this->dataObject);

        // PHPUnit 12 compatible: Replace addMethods with anonymous class for interface
        /** @var CustomOptionInterface $customOption */
        $this->customOption = new class {
            private $downloadableLinksResult;
            
            public function __construct()
            {
            }
            
            public function getDownloadableLinks()
            {
                return $this->downloadableLinksResult;
            }
            
            public function setDownloadableLinks($result)
            {
                $this->downloadableLinksResult = $result;
                return $this;
            }
            
            public function setOptionId($optionId)
            {
                return $this;
            }
            
            public function setOptionValue($optionValue)
            {
                return $this;
            }
        };

        $this->customOptionFactory = $this->getMockBuilder(
            CustomOptionFactory::class
        )
            ->onlyMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->customOptionFactory->method('create')->willReturn($this->customOption);

        $this->processor = new ProductOptionProcessor(
            $this->dataObjectFactory,
            $this->customOptionFactory
        );

        $urlBuilder = $this->getMockBuilder(UrlBuilder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUrl'])
            ->getMock();
        $urlBuilder->method('getUrl')->willReturn('http://built.url/string/');

        $reflection = new \ReflectionClass(get_class($this->processor));
        $reflectionProperty = $reflection->getProperty('urlBuilder');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($this->processor, $urlBuilder);
    }

    /**
     * @param array|string $options
     * @param array $requestData
     */
    #[DataProvider('dataProviderConvertToBuyRequest')]
    public function testConvertToBuyRequest(
        $options,
        $requestData
    ) {
        if (!empty($options)) {
            $options[0] = $options[0]($this);
        }
        $productOptionMock = $this->createMock(ProductOptionInterface::class);

        // PHPUnit 12 compatible: Replace addMethods with anonymous class for interface
        /** @var ProductOptionExtensionInterface $productOptionExtensionMock */
        $productOptionExtensionMock = new class {
            private $customOptionsResult;
            
            public function __construct()
            {
            }
            
            public function getCustomOptions()
            {
                return $this->customOptionsResult;
            }
            
            public function setCustomOptions($result)
            {
                $this->customOptionsResult = $result;
                return $this;
            }
        };

        $productOptionMock->method('getExtensionAttributes')->willReturn($productOptionExtensionMock);

        $productOptionExtensionMock->setCustomOptions($options);

        $this->dataObject->addData($requestData);

        $this->assertEquals($this->dataObject, $this->processor->convertToBuyRequest($productOptionMock));
    }

    protected function getOptionsDataForprovider()
    {
        $objectManager = new ObjectManager($this);

        /** @var CustomOption $option */
        $option = $objectManager->getObject(CustomOption::class);
        $option->setOptionId(1);
        $option->setOptionValue(1);
        return $option;
    }

    /**
     * @return array
     */
    public static function dataProviderConvertToBuyRequest()
    {

        /** @var CustomOption $option */
        $option = static fn (self $testCase) => $testCase->getOptionsDataForprovider();

        return [
            [
                [$option],
                [
                    'options' => [
                        1 => 1,
                    ],
                ],
            ],
            [[], []],
            ['', []],
        ];
    }

    /**
     * @param array|string $options
     * @param string|null $expected
     */
    #[DataProvider('dataProviderConvertToProductOption')]
    public function testConvertToProductOption(
        $options,
        $expected
    ) {
        $this->dataObject->setOptions($options);

        if (!empty($options) && is_array($options)) {
            // Set up the custom option behavior
            $this->customOption->setOptionId(1);
            $this->customOption->setOptionId(2);
            $this->customOption->setOptionValue(1);
            $this->customOption->setOptionValue(2);
        }

        $result = $this->processor->convertToProductOption($this->dataObject);

        if (!empty($expected)) {
            $this->assertArrayHasKey($expected, $result);
            $this->assertIsArray($result);
            $this->assertSame($this->customOption, $result['custom_options'][0]);
        } else {
            $this->assertEmpty($result);
        }
    }

    /**
     * @return array
     */
    public static function dataProviderConvertToProductOption()
    {
        return [
            [
                'options' => [
                    1 => 'value',
                    2 => [
                        1,
                        2,
                        'url' => [
                            'route' => 'route',
                            'params' => ['id' => 20, 'key' => '8175c7c36ef69432347e']
                        ]
                    ],
                ],
                'expected' => 'custom_options',
            ],
            [
                'options' => [],
                'expected' => null,
            ],
            [
                'options' => 'is not array',
                'expected' => null,
            ],
        ];
    }
}
