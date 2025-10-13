<?php
/**
 * Copyright 2023 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\QuoteGraphQl\Test\Unit\Model\Resolver\ShippingAddress;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\GraphQl\Model\Query\Context;
use Magento\Quote\Model\Quote;
use Magento\QuoteGraphQl\Model\Resolver\ShippingAddress\SelectedShippingMethod;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Magento\Quote\Model\Cart\ShippingMethodConverter;
use Magento\Quote\Model\Quote\Address\Rate;
use Magento\Quote\Test\Unit\Helper\AddressShippingMethodGraphQlTestHelper;
use Magento\Quote\Test\Unit\Helper\ShippingMethodDataTestHelper;
use Magento\Quote\Test\Unit\Helper\QuoteCurrencyCodeTestHelper;
use Magento\Quote\Test\Unit\Helper\RateTestHelper;

/**
 * @see SelectedShippingMethod
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SelectedShippingMethodTest extends TestCase
{
    /**
     * @var SelectedShippingMethod
     */
    private $selectedShippingMethod;

    /**
     * @var ShippingMethodConverter|MockObject
     */
    private $shippingMethodConverterMock;

    /**
     * @var ScopeConfigInterface|MockObject
     */
    private $scopeConfigMock;

    /**
     * @var Address|MockObject
     */
    private $addressMock;

    /**
     * @var Rate|MockObject
     */
    private $rateMock;

    /**
     * @var Field|MockObject
     */
    private $fieldMock;

    /**
     * @var ResolveInfo|MockObject
     */
    private $resolveInfoMock;

    /**
     * @var Context|MockObject
     */
    private $contextMock;

    /**
     * @var Quote|MockObject
     */
    private $quoteMock;

    /**
     * @var array
     */
    private $valueMock = [];

    protected function setUp(): void
    {
        $this->shippingMethodConverterMock = $this->createMock(ShippingMethodConverter::class);
        $this->contextMock = $this->createMock(Context::class);
        $this->fieldMock = $this->createMock(Field::class);
        $this->resolveInfoMock = $this->createMock(ResolveInfo::class);
        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);
        $this->addressMock = new AddressShippingMethodGraphQlTestHelper();
        $this->rateMock = (new RateTestHelper())
            ->setMethod('shipping_method')
            ->setCarrier('shipping_carrier')
            ->setCode('shipping_carrier');
        $this->quoteMock = new QuoteCurrencyCodeTestHelper('USD');
        $this->selectedShippingMethod = new SelectedShippingMethod(
            $this->shippingMethodConverterMock
        );
    }

    public function testResolveWithoutModelInValueParameter(): void
    {
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('"model" value should be specified');
        $this->selectedShippingMethod->resolve(
            $this->fieldMock,
            $this->contextMock,
            $this->resolveInfoMock,
            $this->valueMock
        );
    }

    public function testResolve(): void
    {
        $this->valueMock = ['model' => $this->addressMock];
        $this->addressMock
            ->setShippingAmountValue('shipping_amount')
            ->setMethodValue('shipping_method')
            ->setQuote($this->quoteMock)
            ->setAllShippingRates([$this->rateMock]);
        $this->shippingMethodConverterMock->method('modelToDataObject')
            ->willReturn(new ShippingMethodDataTestHelper());
        $this->selectedShippingMethod->resolve(
            $this->fieldMock,
            $this->contextMock,
            $this->resolveInfoMock,
            $this->valueMock
        );
    }
}
