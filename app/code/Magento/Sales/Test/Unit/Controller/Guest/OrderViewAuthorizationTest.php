<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Sales\Test\Unit\Controller\Guest;

use Magento\Framework\Registry;
use Magento\Sales\Controller\Guest\OrderViewAuthorization;
use Magento\Sales\Model\CurrentOrder;
use Magento\Sales\Model\Order;
use PHPUnit\Framework\TestCase;

/**
 * Verifies guest authorization against the current order.
 */
class OrderViewAuthorizationTest extends TestCase
{
    /**
     * Matching order IDs grant access, including for a different model instance.
     */
    public function testCanViewMatchingOrder(): void
    {
        $currentOrder = new CurrentOrder(new Registry());
        $currentOrder->set($this->createOrder(12));
        $authorization = new OrderViewAuthorization(null, $currentOrder);

        $this->assertTrue($authorization->canView($this->createOrder(12)));
    }

    /**
     * Different order IDs deny access.
     */
    public function testCannotViewDifferentOrder(): void
    {
        $currentOrder = new CurrentOrder(new Registry());
        $currentOrder->set($this->createOrder(12));
        $authorization = new OrderViewAuthorization(null, $currentOrder);

        $this->assertFalse($authorization->canView($this->createOrder(13)));
    }

    /**
     * Requests without a current order deny access without an error.
     */
    public function testCannotViewWithoutCurrentOrder(): void
    {
        $currentOrder = new CurrentOrder(new Registry());
        $authorization = new OrderViewAuthorization(null, $currentOrder);

        $this->assertFalse($authorization->canView($this->createOrder(12)));
    }

    /**
     * Create an order with a known ID without loading its dependencies.
     *
     * @param int $id
     * @return Order
     */
    private function createOrder(int $id): Order
    {
        $order = $this->createMock(Order::class);
        $order->method('getId')->willReturn($id);
        return $order;
    }
}
