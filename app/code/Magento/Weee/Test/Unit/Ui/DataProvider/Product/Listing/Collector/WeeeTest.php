<?php
/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Weee\Test\Unit\Ui\DataProvider\Product\Listing\Collector;

use Magento\Catalog\Api\Data\ProductRender\PriceInfoExtensionInterfaceFactory;
use Magento\Catalog\Api\Data\ProductRenderInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductRender\FormattedPriceInfoBuilder;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Catalog\Test\Unit\Helper\PriceInfoExtensionInterfaceTestHelper;
use Magento\Framework\Pricing\Amount\AmountInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Pricing\PriceInfo\Base;
use Magento\Framework\Pricing\Test\Unit\Helper\PriceInfoBaseTestHelper;
use Magento\Weee\Api\Data\ProductRender\WeeeAdjustmentAttributeInterface;
use Magento\Weee\Api\Data\ProductRender\WeeeAdjustmentAttributeInterfaceFactory;
use Magento\Weee\Helper\Data;
use Magento\Weee\Model\ProductRender\WeeeAdjustmentAttribute;
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
        $this->weeeHelperMock = $this->createMock(Data::class);
        $this->priceCurrencyMock = $this->createMock(PriceCurrencyInterface::class);

        $this->weeeAdjustmentAttributeFactory = $this->createPartialMock(
            WeeeAdjustmentAttributeInterfaceFactory::class,
            ['create']
        );

        $this->extensionAttributes = new PriceInfoExtensionInterfaceTestHelper();

        $this->priceInfoExtensionFactory = $this->createPartialMock(
            PriceInfoExtensionInterfaceFactory::class,
            ['create']
        );

        $this->formattedPriceInfoBuilder = $this->createMock(FormattedPriceInfoBuilder::class);

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
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function testCollect()
    {
        $productMock = $this->createMock(Product::class);
        $productRender = $this->createMock(ProductRenderInterface::class);
        $weeAttribute = $this->createWeeeAdjustmentAttributeMock();
        $this->weeeAdjustmentAttributeFactory->expects($this->atLeastOnce())
            ->method('create')
            ->willReturn($weeAttribute);
        $priceInfo = $this->createPriceInfoMock();
        $price = $this->createMock(FinalPrice::class);
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
            ->willReturnCallback(function ($value) use (&$callCount) {
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
        $weeAttribute = $this->createPartialMock(WeeeAdjustmentAttribute::class, ['getData']);
        $weeAttribute->method('getData')->willReturnCallback(function ($key = null) {
            if ($key === null) {
                return [
                    'code' => 'test_code',
                    'amount' => 12.1,
                    'tax_amount' => 12.1,
                    'amount_excl_tax' => 12.1
                ];
            }
            // Return 12.1 for all numeric fields to match test expectations
            if (in_array($key, ['amount', 'tax_amount', 'amount_excl_tax'])) {
                return 12.1;
            }
            if ($key === 'code') {
                return 'test_code';
            }
            return null;
        });
        return $weeAttribute;
    }

    /**
     * Create a mock for PriceInfo Base
     *
     * @return Base
     */
    private function createPriceInfoMock(): Base
    {
        return new PriceInfoBaseTestHelper();
    }
}
