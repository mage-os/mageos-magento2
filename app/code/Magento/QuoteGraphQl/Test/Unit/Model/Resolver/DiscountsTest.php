<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\QuoteGraphQl\Test\Unit\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote;
use Magento\QuoteGraphQl\Model\GetDiscounts;
use Magento\Quote\Api\Data\AddressExtensionInterface;
use Magento\Quote\Model\Quote\Address;
use Magento\QuoteGraphQl\Model\Resolver\Discounts;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Unit test for Discounts resolver
 */
class DiscountsTest extends TestCase
{
    /**
     * @var GetDiscounts|MockObject
     */
    private $getDiscountsMock;

    /**
     * @var Discounts
     */
    private $discountsResolver;

    /**
     * @var Field|MockObject
     */
    private $fieldMock;

    /**
     * @var ResolveInfo|MockObject
     */
    private $resolveInfoMock;

    /**
     * @var ContextInterface|MockObject
     */
    private $contextMock;

    /**
     * @var Quote|MockObject
     */
    private $quoteMock;

    /**
     * @var Address|MockObject
     */
    private $billingAddressMock;

    /**
     * @var Address|MockObject
     */
    private $shippingAddressMock;

    /**
     * @var AddressExtensionInterface|MockObject
     */
    private $extensionAttributesMock;

    protected function setUp(): void
    {
        $this->getDiscountsMock = $this->createMock(GetDiscounts::class);
        $this->discountsResolver = new Discounts($this->getDiscountsMock);
        $this->fieldMock = $this->createMock(Field::class);
        $this->resolveInfoMock = $this->createMock(ResolveInfo::class);
        $this->contextMock = $this->createMock(ContextInterface::class);
        $this->quoteMock = $this->createMock(Quote::class);
        $this->billingAddressMock = $this->createMock(Address::class);
        $this->shippingAddressMock = $this->createMock(Address::class);
        $this->extensionAttributesMock = $this->getMockBuilder(AddressExtensionInterface::class)
            ->addMethods(['getDiscounts'])
            ->getMockForAbstractClass();
        $this->extensionAttributesMock->method('getDiscounts')->willReturn(['discount1', 'discount2']);
    }

    /**
     * Test resolve method when model is not provided
     */
    public function testResolveThrowsExceptionWhenModelIsMissing(): void
    {
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('"model" value should be specified');
        $this->discountsResolver->resolve($this->fieldMock, $this->contextMock, $this->resolveInfoMock);
    }

    /**
     * Test resolve method for virtual quote
     */
    public function testResolveForVirtualQuote(): void
    {
        $this->quoteMock->method('getIsVirtual')->willReturn(true);
        $this->quoteMock->method('getBillingAddress')->willReturn($this->billingAddressMock);
        $this->billingAddressMock->method('getExtensionAttributes')->willReturn($this->extensionAttributesMock);

        $discounts = ['discount1', 'discount2'];
        $expectedResult = ['formatted_discounts'];
        $this->getDiscountsMock->expects($this->once())
            ->method('execute')
            ->with($this->quoteMock, $discounts)
            ->willReturn($expectedResult);

        $result = $this->discountsResolver->resolve(
            $this->fieldMock,
            $this->contextMock,
            $this->resolveInfoMock,
            ['model' => $this->quoteMock],
            null
        );

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test resolve method for non-virtual quote
     */
    public function testResolveForNonVirtualQuote(): void
    {
        $this->quoteMock->method('getIsVirtual')->willReturn(false);
        $this->quoteMock->method('getShippingAddress')->willReturn($this->shippingAddressMock);
        $this->shippingAddressMock->method('getExtensionAttributes')->willReturn($this->extensionAttributesMock);

        $discounts = ['discount1', 'discount2'];
        $expectedResult = ['formatted_discounts'];
        $this->getDiscountsMock->expects($this->once())
            ->method('execute')
            ->with($this->quoteMock, $discounts)
            ->willReturn($expectedResult);

        $result = $this->discountsResolver->resolve(
            $this->fieldMock,
            $this->contextMock,
            $this->resolveInfoMock,
            ['model' => $this->quoteMock],
            null
        );

        $this->assertEquals($expectedResult, $result);
    }
}
