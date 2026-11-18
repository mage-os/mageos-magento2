<?php
/**
 * Copyright 2014 Adobe
 * All Rights Reserved.
 */
namespace Magento\Sales\Controller\Guest;

use Magento\Framework\App\ObjectManager;
use Magento\Sales\Controller\AbstractController\OrderViewAuthorizationInterface;
use Magento\Sales\Model\CurrentOrder;

/**
 * Authorizes guest access against the current order.
 */
class OrderViewAuthorization implements OrderViewAuthorizationInterface
{
    /**
     * @var CurrentOrder
     */
    private CurrentOrder $currentOrder;

    /**
     * @var \Magento\Framework\Registry|null
     */
    protected $registry;

    /**
     * @param \Magento\Framework\Registry|null $registry
     * @param CurrentOrder|null $currentOrder
     */
    public function __construct(
        ?\Magento\Framework\Registry $registry = null,
        ?CurrentOrder $currentOrder = null
    ) {
        $this->currentOrder = $currentOrder ?? ObjectManager::getInstance()->get(CurrentOrder::class);
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function canView(\Magento\Sales\Model\Order $order)
    {
        $currentOrder = $this->currentOrder->get();
        if ($currentOrder !== null && $order->getId() && $order->getId() === $currentOrder->getId()) {
            return true;
        }
        return false;
    }
}
