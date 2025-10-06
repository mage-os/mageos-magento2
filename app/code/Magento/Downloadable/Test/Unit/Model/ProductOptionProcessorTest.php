<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Downloadable\Test\Unit\Model;

use PHPUnit\Framework\Attributes\DataProvider;
use Magento\Catalog\Api\Data\ProductOptionExtensionInterface;
use Magento\Catalog\Api\Data\ProductOptionInterface;
use Magento\Downloadable\Api\Data\DownloadableOptionInterface;
use Magento\Downloadable\Model\DownloadableOptionFactory;
use Magento\Downloadable\Model\ProductOptionProcessor;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\DataObject;
use Magento\Framework\DataObject\Factory as DataObjectFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

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
     * @var DataObjectHelper|MockObject
     */
    protected $dataObjectHelper;

    /**
     * @var DownloadableOptionFactory|MockObject
     */
    protected $downloadableOptionFactory;

    /**
     * @var DownloadableOptionInterface|MockObject
     */
    protected $downloadableOption;

    protected function setUp(): void
    {
        $this->dataObject = $this->createPartialMock(
            \Magento\Downloadable\Test\Unit\Helper\DataObjectTestHelper::class,
            ['getLinks', 'addData']
        );

        $this->dataObjectFactory = $this->getMockBuilder(DataObjectFactory::class)
            ->onlyMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->dataObjectFactory->method('create')->willReturn($this->dataObject);

        $this->dataObjectHelper = $this->getMockBuilder(DataObjectHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->downloadableOption = $this->createMock(DownloadableOptionInterface::class);

        $this->downloadableOptionFactory = $this->getMockBuilder(
            DownloadableOptionFactory::class
        )
            ->onlyMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->downloadableOptionFactory->method('create')->willReturn($this->downloadableOption);

        $this->processor = new ProductOptionProcessor(
            $this->dataObjectFactory,
            $this->dataObjectHelper,
            $this->downloadableOptionFactory
        );
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
        $productOptionMock = $this->createMock(ProductOptionInterface::class);

        $productOptionExtensionMock = $this->createPartialMock(
            \Magento\Quote\Test\Unit\Helper\ProductOptionExtensionInterfaceTestHelper::class,
            ['getDownloadableOption']
        );

        $productOptionMock->method('getExtensionAttributes')->willReturn($productOptionExtensionMock);

        $productOptionExtensionMock->method('getDownloadableOption')->willReturn($this->downloadableOption);

        $this->downloadableOption->method('getDownloadableLinks')->willReturn($options);

        $this->dataObject->expects($this->any())
            ->method('addData')
            ->with($requestData)
            ->willReturnSelf();

        $this->assertEquals($this->dataObject, $this->processor->convertToBuyRequest($productOptionMock));
    }

    /**
     * @return array
     */
    public static function dataProviderConvertToBuyRequest()
    {
        return [
            [
                [1, 2, 3],
                [
                    'links' => [1, 2, 3],
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
        $this->dataObject->method('getLinks')->willReturn($options);

        $this->dataObjectHelper->expects($this->any())
            ->method('populateWithArray')
            ->with(
                $this->downloadableOption,
                ['downloadable_links' => $options],
                DownloadableOptionInterface::class
            )
            ->willReturnSelf();

        $result = $this->processor->convertToProductOption($this->dataObject);

        if (!empty($expected)) {
            $this->assertArrayHasKey($expected, $result);
            $this->assertSame($this->downloadableOption, $result[$expected]);
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
                'options' => [1, 2, 3],
                'expected' => 'downloadable_option',
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
