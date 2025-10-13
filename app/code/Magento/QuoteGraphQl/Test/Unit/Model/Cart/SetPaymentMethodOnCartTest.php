<?php
/**
 * Copyright 2020 Adobe
 * All Rights Reserved.
 */

declare(strict_types=1);

namespace Magento\QuoteGraphQl\Test\Unit\Model\Cart;

use Magento\Checkout\Api\Exception\PaymentProcessingRateLimitExceededException;
use Magento\Checkout\Api\PaymentSavingRateLimiterInterface;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Quote\Model\Quote;
use Magento\QuoteGraphQl\Model\Cart\SetPaymentMethodOnCart;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SetPaymentMethodOnCartTest extends TestCase
{
    /**
     * @var SetPaymentMethodOnCart
     */
    private $model;

    /**
     * @var PaymentSavingRateLimiterInterface|MockObject
     */
    private $rateLimiterMock;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->rateLimiterMock = $this->createMock(PaymentSavingRateLimiterInterface::class);
        $this->model = new SetPaymentMethodOnCart(
            $this->createMock(\Magento\Quote\Api\PaymentMethodManagementInterface::class),
            $this->createMock(\Magento\QuoteGraphQl\Model\Cart\Payment\PaymentMethodBuilder::class),
            null,
            $this->rateLimiterMock
        );
    }

    /**
     * Verify that the method is rate-limited.
     *
     * @return void
     */
    public function testLimited(): void
    {
        $this->rateLimiterMock->method('limit')
            ->willThrowException(new PaymentProcessingRateLimitExceededException(__('Error')));

        //There will be en error if the limiter won't stop the execution
        $this->model->execute($this->createMock(Quote::class), []);
    }
}
