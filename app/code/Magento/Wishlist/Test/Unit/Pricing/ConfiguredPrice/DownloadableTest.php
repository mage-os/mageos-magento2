<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
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
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DownloadableTest extends TestCase
{
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
        $this->priceInfoMock = $this->getMockBuilder(PriceInfoInterface::class)
            ->getMockForAbstractClass();

        $this->saleableItem = new class() extends \Magento\Catalog\Model\Product {
            /**
             * @var PriceInfoInterface
             */
            private $priceInfo;
            /**
             * @var bool
             */
            private $linksPurchasedSeparately = true;
            /**
             * @var array
             */
            private $customOptions = [];
            /**
             * @var TypeInstanceInterface
             */
            private $typeInstance;
            
            public function __construct()
            {
            }
            
            public function setPriceInfo($priceInfo)
            {
                $this->priceInfo = $priceInfo;
                $_ = [$priceInfo];
                unset($_);
                return $this;
            }
            
            public function getPriceInfo()
            {
                return $this->priceInfo;
            }
            
            public function getLinksPurchasedSeparately()
            {
                return $this->linksPurchasedSeparately;
            }
            
            public function setLinksPurchasedSeparately($value)
            {
                $this->linksPurchasedSeparately = $value;
                return $this;
            }
            
            public function getCustomOption($key)
            {
                return $this->customOptions[$key] ?? null;
            }
            
            public function setCustomOption($key, $value)
            {
                $this->customOptions[$key] = $value;
                return $this;
            }
            
            public function setTypeInstance($typeInstance)
            {
                $this->typeInstance = $typeInstance;
                $_ = [$typeInstance];
                unset($_);
                return $this;
            }
            
            public function getTypeInstance()
            {
                return $this->typeInstance;
            }
        };
        
        $this->saleableItem->setPriceInfo($this->priceInfoMock);

        $this->calculator = $this->getMockBuilder(CalculatorInterface::class)
            ->getMockForAbstractClass();

        $this->priceCurrency = $this->getMockBuilder(PriceCurrencyInterface::class)
            ->getMockForAbstractClass();

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

        $wishlistItemOptionMock = $this->createMock(Option::class);
        $wishlistItemOptionMock->expects($this->once())
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

        $priceMock = $this->getMockBuilder(PriceInterface::class)
            ->getMockForAbstractClass();
        $priceMock->expects($this->once())
            ->method('getValue')
            ->willReturn($priceValue);

        $this->priceInfoMock->expects($this->once())
            ->method('getPrice')
            ->with(BasePrice::PRICE_CODE)
            ->willReturn($priceMock);

        $this->saleableItem->setCustomOption('downloadable_link_ids', $wishlistItemOptionMock);
        $this->saleableItem->setTypeInstance($productTypeMock);

        $this->assertEquals(20, $this->model->getValue());
    }

    public function testGetValueNoLinksPurchasedSeparately()
    {
        $priceValue = 10;

        $priceMock = $this->getMockBuilder(PriceInterface::class)
            ->getMockForAbstractClass();
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

        $priceMock = $this->getMockBuilder(PriceInterface::class)
            ->getMockForAbstractClass();
        $priceMock->expects($this->once())
            ->method('getValue')
            ->willReturn($priceValue);

        $this->priceInfoMock->expects($this->once())
            ->method('getPrice')
            ->with(BasePrice::PRICE_CODE)
            ->willReturn($priceMock);

        $wishlistItemOptionMock = $this->createMock(Option::class);
        $wishlistItemOptionMock->expects($this->once())
            ->method('getValue')
            ->willReturn(null);

        $productTypeMock = $this->createMock(Type::class);
        $productTypeMock->expects($this->once())
            ->method('getLinks')
            ->with($this->saleableItem)
            ->willReturn([]);

        $this->saleableItem->setCustomOption('downloadable_link_ids', $wishlistItemOptionMock);
        $this->saleableItem->setTypeInstance($productTypeMock);

        $this->assertEquals($priceValue, $this->model->getValue());
    }

    public function testGetValueWithNoCustomOption()
    {
        $priceMock = $this->getMockBuilder(PriceInterface::class)
            ->getMockForAbstractClass();
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
