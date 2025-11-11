<?php
/**
 * Copyright 2016 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Wishlist\Test\Unit\Pricing\ConfiguredPrice;

use Magento\Catalog\Pricing\Price\BasePrice;
use Magento\Downloadable\Model\Link;
use Magento\Downloadable\Model\Product\Type;
use Magento\Framework\Pricing\Adjustment\CalculatorInterface;
use Magento\Framework\Pricing\Price\PriceInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Pricing\PriceInfoInterface;
use Magento\Framework\Pricing\SaleableInterface;
use Magento\Wishlist\Model\Item\Option;
use Magento\Wishlist\Pricing\ConfiguredPrice\Downloadable;
use Magento\Catalog\Model\Product;
use Magento\Framework\TestFramework\Unit\Helper\MockCreationTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DownloadableTest extends TestCase
{
    use MockCreationTrait;

    /**
     * @var SaleableInterface|MockObject
     */
    private $saleableItem;

    /**
     * @var CalculatorInterface|MockObject
     */
    private $calculator;

    /**
     * @var PriceCurrencyInterface|MockObject
     */
    private $priceCurrency;

    /**
     * @var Downloadable
     */
    private $model;

    /**
     * @var PriceInfoInterface|MockObject
     */
    private $priceInfoMock;

    protected function setUp(): void
    {
        $this->priceInfoMock = $this->createMock(PriceInfoInterface::class);

        $this->saleableItem = $this->createPartialMockWithReflection(
            Product::class,
            [
                'getPriceInfo',
                'setPriceInfo',
                'getCustomOption',
                'setCustomOption',
                'getTypeInstance',
                'setTypeInstance',
                'getLinksPurchasedSeparately',
                'setLinksPurchasedSeparately'
            ]
        );
        
        $customOptions = [];
        $this->saleableItem->method('getCustomOption')->willReturnCallback(
            function ($key) use (&$customOptions) {
                return $customOptions[$key] ?? null;
            }
        );
        $this->saleableItem->method('setCustomOption')->willReturnCallback(
            function ($key, $value) use (&$customOptions) {
                $customOptions[$key] = $value;
                return $this->saleableItem;
            }
        );
        
        $typeInstance = null;
        $this->saleableItem->method('getTypeInstance')->willReturnCallback(function () use (&$typeInstance) {
            return $typeInstance;
        });
        $this->saleableItem->method('setTypeInstance')->willReturnCallback(function ($instance) use (&$typeInstance) {
            $typeInstance = $instance;
            return $this->saleableItem;
        });
        
        $linksPurchased = false;
        $this->saleableItem->method('getLinksPurchasedSeparately')->willReturnCallback(
            function () use (&$linksPurchased) {
                return $linksPurchased;
            }
        );
        $this->saleableItem->method('setLinksPurchasedSeparately')->willReturnCallback(
            function ($value) use (&$linksPurchased) {
                $linksPurchased = $value;
                return $this->saleableItem;
            }
        );
        
        $this->saleableItem->method('getPriceInfo')->willReturn($this->priceInfoMock);

        $this->calculator = $this->createMock(CalculatorInterface::class);

        $this->priceCurrency = $this->createMock(PriceCurrencyInterface::class);

        $this->model = new Downloadable(
            $this->saleableItem,
            null,
            $this->calculator,
            $this->priceCurrency
        );
    }

    public function testGetValue()
    {
        $priceValue = 10;

        $optionMock = $this->createMock(Option::class);
        $optionMock->expects($this->once())
            ->method('getValue')
            ->willReturn('1,2');

        $linkMock = $this->createMock(Link::class);
        $linkMock->expects($this->once())
            ->method('getPrice')
            ->willReturn(10);

        $productTypeMock = $this->createMock(Type::class);
        $productTypeMock->expects($this->once())
            ->method('getLinks')
            ->with($this->saleableItem)
            ->willReturn([1 => $linkMock]);

        $priceMock = $this->createMock(PriceInterface::class);
        $priceMock->expects($this->once())
            ->method('getValue')
            ->willReturn($priceValue);

        $this->priceInfoMock->expects($this->once())
            ->method('getPrice')
            ->with(BasePrice::PRICE_CODE)
            ->willReturn($priceMock);

        $this->saleableItem->setCustomOption('downloadable_link_ids', $optionMock);
        $this->saleableItem->setTypeInstance($productTypeMock);
        $this->saleableItem->setLinksPurchasedSeparately(true);

        $this->assertEquals(20, $this->model->getValue());
    }

    public function testGetValueNoLinksPurchasedSeparately()
    {
        $priceValue = 10;

        $priceMock = $this->createMock(PriceInterface::class);
        $priceMock->expects($this->once())
            ->method('getValue')
            ->willReturn($priceValue);

        $this->priceInfoMock->expects($this->once())
            ->method('getPrice')
            ->with(BasePrice::PRICE_CODE)
            ->willReturn($priceMock);

        $this->assertEquals($priceValue, $this->model->getValue());
    }

    public function testGetValueNoOptions()
    {
        $priceValue = 10;

        $priceMock = $this->createMock(PriceInterface::class);
        $priceMock->expects($this->once())
            ->method('getValue')
            ->willReturn($priceValue);

        $this->priceInfoMock->expects($this->once())
            ->method('getPrice')
            ->with(BasePrice::PRICE_CODE)
            ->willReturn($priceMock);

        $optionMock = $this->createMock(Option::class);
        $optionMock->expects($this->once())
            ->method('getValue')
            ->willReturn(null);

        $productTypeMock = $this->createMock(Type::class);
        $productTypeMock->expects($this->once())
            ->method('getLinks')
            ->with($this->saleableItem)
            ->willReturn([]);

        $this->saleableItem->setCustomOption('downloadable_link_ids', $optionMock);
        $this->saleableItem->setTypeInstance($productTypeMock);
        $this->saleableItem->setLinksPurchasedSeparately(true);

        $this->assertEquals($priceValue, $this->model->getValue());
    }

    public function testGetValueWithNoCustomOption()
    {
        $priceMock = $this->createMock(PriceInterface::class);
        $priceMock->expects($this->once())
            ->method('getValue')
            ->willReturn(0);

        $this->priceInfoMock->expects($this->once())
            ->method('getPrice')
            ->with(BasePrice::PRICE_CODE)
            ->willReturn($priceMock);

        $this->saleableItem->setCustomOption('downloadable_link_ids', null);

        $this->assertEquals(0, $this->model->getValue());
    }
}
