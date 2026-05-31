<?php
/**
 * Copyright 2020 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Checkout\Test\Unit\Api\Exception;

use Magento\Checkout\Api\Exception\PaymentProcessingRateLimitExceededException;
use Magento\Framework\Phrase;
use PHPUnit\Framework\TestCase;

class PaymentProcessingRateLimitExceededExceptionTest extends TestCase
{
    public function testExceptionHas429Code(): void
    {
        $exception = new PaymentProcessingRateLimitExceededException(new Phrase('Too many requests'));

        $this->assertSame(429, $exception->getCode());
    }

    public function testExceptionPreservesCause(): void
    {
        $cause = new \Exception('Original cause');
        $exception = new PaymentProcessingRateLimitExceededException(new Phrase('Too many requests'), $cause);

        $this->assertSame($cause, $exception->getPrevious());
        $this->assertSame(429, $exception->getCode());
    }
}
