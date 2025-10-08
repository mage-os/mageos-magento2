<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\QuoteGraphQl\Test\Unit\Model;

use Magento\Quote\Model\Quote;
use Magento\QuoteGraphQl\Model\GetDiscounts;
use Magento\SalesRule\Api\Data\RuleDiscountInterface;
use Magento\SalesRule\Api\Data\DiscountDataInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Unit test for GetDiscounts class
 */
class GetDiscountsTest extends TestCase
{
    /**
     * @var GetDiscounts
     */
    private $getDiscounts;

    /**
     * @var Quote|MockObject
     */
    private $quoteMock;

    protected function setUp(): void
    {
        $this->quoteMock = $this->getMockBuilder(Quote::class)
            ->addMethods(['getQuoteCurrencyCode'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->getDiscounts = new GetDiscounts();
    }

    /**
     * Test execute method with empty discounts array
     */
    public function testExecuteWithEmptyDiscounts(): void
    {
        $result = $this->getDiscounts->execute($this->quoteMock, []);
        $this->assertNull($result);
    }

    /**
     * Test execute method with discounts
     */
    public function testExecuteWithDiscountsAndRuleLabel(): void
    {
        $discountMock = $this->createMock(RuleDiscountInterface::class);
        $discountDataMock = $this->getMockBuilder(DiscountDataInterface::class)
            ->addMethods(['getAppliedTo'])
            ->onlyMethods(['getAmount', 'getBaseAmount', 'getOriginalAmount', 'getBaseOriginalAmount'])
            ->getMock();
        $discountMock->method('getRuleLabel')->willReturn('Summer Sale');
        $discountMock->method('getDiscountData')->willReturn($discountDataMock);
        $discountDataMock->method('getAmount')->willReturn(10.50);
        $discountDataMock->method('getAppliedTo')->willReturn('TEST');
        $this->quoteMock->method('getQuoteCurrencyCode')->willReturn('USD');
        $discounts = [$discountMock];
        $expectedResult = [
            [
                'label' => 'Summer Sale',
                'applied_to' => 'TEST',
                'amount' => [
                    'value' => 10.50,
                    'currency' => 'USD'
                ],
                'discount_model' => $discountMock,
                'quote_model' => $this->quoteMock
            ]
        ];
        $result = $this->getDiscounts->execute($this->quoteMock, $discounts);
        $this->assertEquals($expectedResult, $result);
    }
}
