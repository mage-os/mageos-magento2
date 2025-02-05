<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\SalesRuleGraphQl\Test\Unit\Model\Resolver;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Quote\Api\Data\CartInterface;
use Magento\SalesRule\Api\Data\CouponInterface;
use Magento\SalesRule\Api\Data\RuleDiscountInterface;
use Magento\SalesRule\Model\GetCoupons;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\SalesRule\Model\Quote\GetCouponCodes;
use Magento\SalesRuleGraphQl\Model\Resolver\Coupon;
use PHPUnit\Framework\TestCase;

class CouponTest extends TestCase
{
    /**
     * @var GetCouponCodes|MockObject
     */
    private $getCouponCodesMock;

    /**
     * @var GetCoupons|MockObject
     */
    private $getCouponsMock;

    /**
     * @var Coupon|MockObject
     */
    private $resolver;

    protected function setUp(): void
    {
        $this->getCouponCodesMock = $this->createMock(GetCouponCodes::class);
        $this->getCouponsMock = $this->createMock(GetCoupons::class);
        $this->resolver = new Coupon(
            $this->getCouponCodesMock,
            $this->getCouponsMock
        );
    }

    public function testResolveWithOrderModel(): void
    {
        $orderModel = $this->createMock(OrderInterface::class);
        $orderModel->expects($this->once())
            ->method('getCouponCode')
            ->willReturn('TEST1234');
        $result = $this->resolver->resolve(
            $this->createMock(Field::class),
            [],
            $this->createMock(ResolveInfo::class),
            ['order_model' => $orderModel]
        );

        $this->assertEquals(['code' => 'TEST1234'], $result);
    }

    public function testResolveWithoutDiscountModel(): void
    {
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('"discount_model" value should be specified');
        $this->resolver->resolve(
            $this->createMock(Field::class),
            [],
            $this->createMock(ResolveInfo::class),
            []
        );
    }

    public function testResolveWithoutQuoteModel(): void
    {
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('"quote_model" value should be specified');
        $this->resolver->resolve(
            $this->createMock(Field::class),
            [],
            $this->createMock(ResolveInfo::class),
            ['discount_model' => $this->createMock(RuleDiscountInterface::class)]
        );
    }

    public function testResolveWithoutCoupon(): void
    {
        $quoteModel = $this->createMock(CartInterface::class);
        $discountModel = $this->createMock(RuleDiscountInterface::class);
        $this->getCouponCodesMock->method('execute')->willReturn([]);
        $this->getCouponsMock->method('execute')->willReturn([]);
        $result = $this->resolver->resolve(
            $this->createMock(Field::class),
            [],
            $this->createMock(ResolveInfo::class),
            ['discount_model' => $discountModel, 'quote_model' => $quoteModel]
        );

        $this->assertNull($result);
    }

    public function testResolveWithMatchingRuleId(): void
    {
        $quoteModel = $this->createMock(CartInterface::class);
        $discountModel = $this->createMock(RuleDiscountInterface::class);
        $discountModel->method('getRuleID')->willReturn(123);
        $couponMock = $this->createMock(CouponInterface::class);
        $couponMock->method('getRuleId')->willReturn(123);
        $couponMock->method('getCode')->willReturn('TEST1234');
        $this->getCouponCodesMock->method('execute')->willReturn(['test']);
        $this->getCouponsMock->method('execute')->willReturn([$couponMock]);
        $result = $this->resolver->resolve(
            $this->createMock(Field::class),
            [],
            $this->createMock(ResolveInfo::class),
            ['discount_model' => $discountModel, 'quote_model' => $quoteModel]
        );

        $this->assertEquals(['code' => 'TEST1234'], $result);
    }

    public function testResolveNoMatchingRuleId(): void
    {
        $quoteModel = $this->createMock(CartInterface::class);
        $discountModel = $this->createMock(RuleDiscountInterface::class);
        $discountModel->method('getRuleID')->willReturn(123);
        $couponMock = $this->createMock(CouponInterface::class);
        $couponMock->method('getRuleId')->willReturn(321);
        $this->getCouponCodesMock->method('execute')->willReturn(['test']);
        $this->getCouponsMock->method('execute')->willReturn([$couponMock]);
        $result = $this->resolver->resolve(
            $this->createMock(Field::class),
            [],
            $this->createMock(ResolveInfo::class),
            ['discount_model' => $discountModel, 'quote_model' => $quoteModel]
        );

        $this->assertNull($result);
    }
}
