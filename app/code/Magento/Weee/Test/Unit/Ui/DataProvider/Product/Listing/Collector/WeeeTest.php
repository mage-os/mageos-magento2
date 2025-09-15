<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Weee\Test\Unit\Ui\DataProvider\Product\Listing\Collector;

use Magento\Catalog\Api\Data\ProductRender\PriceInfoExtensionInterface;
use Magento\Catalog\Api\Data\ProductRender\PriceInfoExtensionInterfaceFactory;
use Magento\Catalog\Api\Data\ProductRenderInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductRender\FormattedPriceInfoBuilder;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Framework\Pricing\Amount\AmountInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Pricing\PriceInfo\Base;
use Magento\Weee\Api\Data\ProductRender\WeeeAdjustmentAttributeInterface;
use Magento\Weee\Api\Data\ProductRender\WeeeAdjustmentAttributeInterfaceFactory;
use Magento\Weee\Helper\Data;
use Magento\Weee\Ui\DataProvider\Product\Listing\Collector\Weee;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
class WeeeTest extends TestCase
{
    /** @var Weee */
    protected $model;

    /** @var Data|MockObject */
    protected $weeeHelperMock;

    /** @var PriceCurrencyInterface|MockObject */
    protected $priceCurrencyMock;

    /** @var PriceInfoExtensionInterface|MockObject */
    private $extensionAttributes;

    /** @var WeeeAdjustmentAttributeInterfaceFactory|MockObject */
    private $weeeAdjustmentAttributeFactory;

    /** @var PriceInfoExtensionInterfaceFactory|MockObject */
    private $priceInfoExtensionFactory;

    /** @var FormattedPriceInfoBuilder|MockObject */
    private $formattedPriceInfoBuilder;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->weeeHelperMock = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->priceCurrencyMock = $this->createMock(PriceCurrencyInterface::class);

        $this->weeeAdjustmentAttributeFactory = $this->getMockBuilder(WeeeAdjustmentAttributeInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['create'])
            ->getMock();

        $this->extensionAttributes = $this->createMock(PriceInfoExtensionInterface::class);

        $this->priceInfoExtensionFactory = $this->getMockBuilder(PriceInfoExtensionInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['create'])
            ->getMock();

        $this->formattedPriceInfoBuilder = $this->getMockBuilder(FormattedPriceInfoBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = new Weee(
            $this->weeeHelperMock,
            $this->priceCurrencyMock,
            $this->weeeAdjustmentAttributeFactory,
            $this->priceInfoExtensionFactory,
            $this->formattedPriceInfoBuilder
        );
    }

    /**
     * @return void
     */
    public function testCollect()
    {
        $productMock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->getMock();
        $productRender = $this->createMock(ProductRenderInterface::class);
        $weeAttribute = $this->createWeeeAdjustmentAttributeMock();
        $this->weeeAdjustmentAttributeFactory->expects($this->atLeastOnce())
            ->method('create')
            ->willReturn($weeAttribute);
        $priceInfo = $this->createPriceInfoMock();
        $price = $this->getMockBuilder(FinalPrice::class)
            ->disableOriginalConstructor()
            ->getMock();
        $weeAttribute->setAttributeCode('');
        $productRender->method('getPriceInfo')->willReturn($priceInfo);
        $priceInfo->setExtensionAttributes($this->extensionAttributes);
        $productMock->method('getPriceInfo')->willReturn($priceInfo);
        $priceInfo->setPrice($price);
        $amount = $this->createMock(AmountInterface::class);
        $productRender->expects($this->exactly(5))
            ->method('getStoreId')
            ->willReturn(1);
        $productRender->expects($this->exactly(5))
            ->method('getCurrencyCode')
            ->willReturn('USD');
        $price->expects($this->once())
            ->method('getAmount')
            ->willReturn($amount);
        $amount->expects($this->once())
            ->method('getValue')
            ->willReturn(12.1);
        $weeAttributes = ['weee_1' => $weeAttribute];
        $callCount = 0;
        $this->priceCurrencyMock->expects($this->exactly(5))
            ->method('format')
            ->with(12.1, true, 2, 1, 'USD')
            ->willReturnCallback(function () use (&$callCount) {
                $callCount++;
                $values = [
                    '<span>$12</span>',
                    '<span>$12</span>',
                    '<span>$71</span>',
                    '<span>$83</span>',
                    '<span>$12</span>'
                ];
                return $values[$callCount - 1];
            });
        $this->weeeHelperMock->expects($this->once())
            ->method('getProductWeeeAttributesForDisplay')
            ->with($productMock)
            ->willReturn($weeAttributes);

        $this->model->collect($productMock, $productRender);
    }

    /**
     * Create a mock for WeeeAdjustmentAttributeInterface
     *
     * @return WeeeAdjustmentAttributeInterface
     */
    private function createWeeeAdjustmentAttributeMock(): WeeeAdjustmentAttributeInterface
    {
        return new class implements WeeeAdjustmentAttributeInterface {
            /**
             * @var mixed
             */
            private $data = null;
            /**
             * @var int
             */
            private $callCount = 0;

            public function getData($key = null)
            {
                if ($this->callCount == 0) {
                    $this->callCount++;
                    return [
                        'amount' => 12.1,
                        'tax_amount' => 12,
                        'amount_excl_tax' => 71
                    ];
                } elseif ($this->callCount == 1 && $key == 'amount') {
                    $this->callCount++;
                    return 12.1;
                } elseif ($this->callCount == 2 && $key == 'tax_amount') {
                    $this->callCount++;
                    return 12.1;
                } elseif ($this->callCount == 3 && $key == 'amount_excl_tax') {
                    $this->callCount++;
                    return 12.1;
                } elseif ($this->callCount == 4) {
                    $this->callCount++;
                    return 12.1;
                }
                return null;
            }

            public function setData($value)
            {
                $this->data = $value;
                return $this;
            }

            public function setAmount($amount)
            {
                return $this;
            }

            public function getAmount()
            {
                return null;
            }

            public function getTaxAmount()
            {
                return null;
            }

            public function setTaxAmount($taxAmount)
            {
                return $this;
            }

            public function setAmountExclTax($amountExclTax)
            {
                return $this;
            }

            public function setTaxAmountInclTax($taxAmountInclTax)
            {
                return $this;
            }

            public function getTaxAmountInclTax()
            {
                return null;
            }

            public function getAmountExclTax()
            {
                return null;
            }

            public function setAttributeCode($attributeCode)
            {
                return $this;
            }

            public function getAttributeCode()
            {
                return null;
            }

            public function getExtensionAttributes()
            {
                return null;
            }

            public function setExtensionAttributes($extensionAttributes)
            {
                return $this;
            }
        };
    }

    /**
     * Create a mock for PriceInfo Base
     *
     * @return Base
     */
    private function createPriceInfoMock(): Base
    {
        return new class extends Base {
            /**
             * @var mixed
             */
            private $extensionAttributes = null;
            /**
             * @var mixed
             */
            private $price = null;

            public function __construct()
            {
            }

            public function getPrice($priceCode)
            {
                return $this->price;
            }

            public function setPrice($value)
            {
                $this->price = $value;
                return $this;
            }

            public function getExtensionAttributes()
            {
                return $this->extensionAttributes;
            }

            public function setExtensionAttributes($value)
            {
                $this->extensionAttributes = $value;
                return $this;
            }
        };
    }
}
