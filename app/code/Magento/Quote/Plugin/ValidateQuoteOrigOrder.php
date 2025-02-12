<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Quote\Plugin;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Validate order id from request param
 */
class ValidateQuoteOrigOrder
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * Validate the user authorization to order
     *
     * @param CartRepositoryInterface $cartRepository
     * @param CartInterface $quote
     * @return void
     * @throws NoSuchEntityException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSave(
        CartRepositoryInterface $cartRepository,
        CartInterface $quote
    ): void {
        if ($orderId = $quote->getOrigOrderId()) {
            $order = $this->orderRepository->get($orderId);
            $orderCustomer = (int)$order->getCustomerId();
            if ($quote->getCustomerId() !== $orderCustomer) {
                throw new NoSuchEntityException(__('Please check input parameters.'));
            }
        }
    }
}
