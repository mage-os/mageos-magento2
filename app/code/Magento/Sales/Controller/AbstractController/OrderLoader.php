<?php
/**
 * Copyright 2014 Adobe
 * All Rights Reserved.
 */
namespace Magento\Sales\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Sales\Model\CurrentOrder;

/**
 * Loads the order authorized for the current storefront request.
 */
class OrderLoader implements OrderLoaderInterface
{
    /**
     * @var CurrentOrder
     */
    private CurrentOrder $currentOrder;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var OrderViewAuthorizationInterface
     */
    protected $orderAuthorization;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var RedirectFactory
     */
    protected $redirectFactory;

    /**
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param OrderViewAuthorizationInterface $orderAuthorization
     * @param Registry $registry
     * @param \Magento\Framework\UrlInterface $url
     * @param ForwardFactory $resultForwardFactory
     * @param RedirectFactory $redirectFactory
     * @param CurrentOrder|null $currentOrder
     */
    public function __construct(
        \Magento\Sales\Model\OrderFactory $orderFactory,
        OrderViewAuthorizationInterface $orderAuthorization,
        Registry $registry,
        \Magento\Framework\UrlInterface $url,
        ForwardFactory $resultForwardFactory,
        RedirectFactory $redirectFactory,
        ?CurrentOrder $currentOrder = null
    ) {
        $this->currentOrder = $currentOrder ?? new CurrentOrder($registry);
        $this->orderFactory = $orderFactory;
        $this->orderAuthorization = $orderAuthorization;
        $this->registry = $registry;
        $this->url = $url;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->redirectFactory = $redirectFactory;
    }

    /**
     * @param RequestInterface $request
     * @return bool|\Magento\Framework\Controller\Result\Forward|\Magento\Framework\Controller\Result\Redirect
     */
    public function load(RequestInterface $request)
    {
        $orderId = (int)$request->getParam('order_id');
        if (!$orderId) {
            /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }

        $order = $this->orderFactory->create()->load($orderId);

        if ($this->orderAuthorization->canView($order)) {
            if ($order instanceof \Magento\Sales\Model\Order) {
                $this->currentOrder->set($order);
            } else {
                $this->registry->register('current_order', $order);
            }
            return true;
        }
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->redirectFactory->create();
        return $resultRedirect->setUrl($this->url->getUrl('*/*/history'));
    }
}
