<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Sales\Test\Unit\Controller\Adminhtml;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Controller\Adminhtml\Order as OrderController;
use Magento\Sales\Controller\Adminhtml\Order\View;
use Magento\Sales\Model\CurrentOrder;
use Magento\Sales\Model\Order;
use PHPUnit\Framework\TestCase;

/**
 * Verifies the current order established by the shared admin controller.
 */
class OrderTest extends TestCase
{
    /**
     * Initializing an order exposes it through both access paths.
     */
    public function testInitOrderSharesCurrentOrderWithRegistry(): void
    {
        $registry = new Registry();
        $currentOrder = new CurrentOrder($registry);
        $order = $this->createMock(Order::class);
        $repository = $this->createMock(OrderRepositoryInterface::class);
        $repository->expects($this->once())->method('get')->with(12)->willReturn($order);
        $request = $this->createMock(RequestInterface::class);
        $request->method('getParam')->with('order_id')->willReturn(12);
        $context = $this->createMock(Context::class);
        $context->method('getRequest')->willReturn($request);
        $controller = (new ObjectManager($this))->getObject(
            View::class,
            [
                'context' => $context,
                'coreRegistry' => $registry,
                'orderRepository' => $repository,
                'currentOrder' => $currentOrder
            ]
        );
        $initOrder = new \ReflectionMethod(OrderController::class, '_initOrder');

        $this->assertSame($order, $initOrder->invoke($controller));
        $this->assertSame($order, $currentOrder->get());
        $this->assertSame($order, $registry->registry('current_order'));
        $this->assertSame($order, $registry->registry('sales_order'));
    }

    /**
     * A custom repository order stays available to Registry readers.
     */
    public function testInitOrderRegistersNonModelOrder(): void
    {
        $registry = new Registry();
        $currentOrder = new CurrentOrder($registry);
        $order = $this->createMock(\Magento\Sales\Api\Data\OrderInterface::class);
        $repository = $this->createMock(OrderRepositoryInterface::class);
        $repository->expects($this->once())->method('get')->with(12)->willReturn($order);
        $request = $this->createMock(RequestInterface::class);
        $request->method('getParam')->with('order_id')->willReturn(12);
        $context = $this->createMock(Context::class);
        $context->method('getRequest')->willReturn($request);
        $controller = (new ObjectManager($this))->getObject(
            View::class,
            [
                'context' => $context,
                'coreRegistry' => $registry,
                'orderRepository' => $repository,
                'currentOrder' => $currentOrder
            ]
        );
        $initOrder = new \ReflectionMethod(OrderController::class, '_initOrder');

        $this->assertSame($order, $initOrder->invoke($controller));
        $this->assertNull($currentOrder->get());
        $this->assertSame($order, $registry->registry('current_order'));
        $this->assertSame($order, $registry->registry('sales_order'));
    }
}
