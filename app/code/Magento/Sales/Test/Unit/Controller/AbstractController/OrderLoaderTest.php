<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Sales\Test\Unit\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Sales\Controller\AbstractController\OrderLoader;
use Magento\Sales\Controller\AbstractController\OrderViewAuthorizationInterface;
use Magento\Sales\Model\CurrentOrder;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
use PHPUnit\Framework\TestCase;

/**
 * Verifies the current order established by storefront loading.
 */
class OrderLoaderTest extends TestCase
{
    /**
     * Loading an authorized order exposes it through both access paths.
     */
    public function testLoadSharesCurrentOrderWithRegistry(): void
    {
        $registry = new Registry();
        $currentOrder = new CurrentOrder($registry);
        $order = $this->createMock(Order::class);
        $order->expects($this->once())->method('load')->with(12)->willReturnSelf();
        $orderFactory = $this->createMock(OrderFactory::class);
        $orderFactory->method('create')->willReturn($order);
        $authorization = $this->createMock(OrderViewAuthorizationInterface::class);
        $authorization->expects($this->once())->method('canView')->with($order)->willReturn(true);
        $request = $this->createMock(RequestInterface::class);
        $request->method('getParam')->with('order_id')->willReturn(12);
        $loader = new OrderLoader(
            $orderFactory,
            $authorization,
            $registry,
            $this->createMock(UrlInterface::class),
            $this->createMock(ForwardFactory::class),
            $this->createMock(RedirectFactory::class),
            $currentOrder
        );

        $this->assertTrue($loader->load($request));
        $this->assertSame($order, $currentOrder->get());
        $this->assertSame($order, $registry->registry('current_order'));
    }

    /**
     * A custom order accepted by an authorization implementation remains in Registry.
     */
    public function testLoadRegistersNonModelOrder(): void
    {
        $registry = new Registry();
        $currentOrder = new CurrentOrder($registry);
        $order = $this->createMock(\Magento\Sales\Api\Data\OrderInterface::class);
        $model = $this->createMock(Order::class);
        $model->expects($this->once())->method('load')->with(12)->willReturn($order);
        $orderFactory = $this->createMock(OrderFactory::class);
        $orderFactory->method('create')->willReturn($model);
        $authorization = new class implements OrderViewAuthorizationInterface {
            /**
             * Accept custom order implementations through a widened parameter type.
             *
             * @param \Magento\Sales\Api\Data\OrderInterface $order
             * @return bool
             */
            public function canView($order)
            {
                return $order instanceof \Magento\Sales\Api\Data\OrderInterface;
            }
        };
        $request = $this->createMock(RequestInterface::class);
        $request->method('getParam')->with('order_id')->willReturn(12);
        $loader = new OrderLoader(
            $orderFactory,
            $authorization,
            $registry,
            $this->createMock(UrlInterface::class),
            $this->createMock(ForwardFactory::class),
            $this->createMock(RedirectFactory::class),
            $currentOrder
        );

        $this->assertTrue($loader->load($request));
        $this->assertNull($currentOrder->get());
        $this->assertSame($order, $registry->registry('current_order'));
    }
}
