<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\SalesGraphQl\Model\Formatter;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\SalesGraphQl\Model\Order\OrderAddress;
use Magento\SalesGraphQl\Model\Order\OrderPayments;

/**
 * Format order model for graphql schema
 */
class Order
{
    private TimezoneInterface $timezone;

    /**
     * @param OrderAddress $orderAddress
     * @param OrderPayments $orderPayments
     * @param TimezoneInterface|null $timezone
     */
    public function __construct(
        private readonly OrderAddress $orderAddress,
        private readonly OrderPayments $orderPayments,
        TimezoneInterface $timezone = null
    ) {
        $this->timezone = $timezone ?: ObjectManager::getInstance()->get(TimezoneInterface::class);
    }

    /**
     * Format order model for graphql schema
     *
     * @param OrderInterface $orderModel
     * @return array
     * @throws LocalizedException
     */
    public function format(OrderInterface $orderModel): array
    {
        return [
            'created_at' => $orderModel->getCreatedAt(),
            'grand_total' => $orderModel->getGrandTotal(),
            'id' => base64_encode((string)$orderModel->getEntityId()),
            'increment_id' => $orderModel->getIncrementId(),
            'number' => $orderModel->getIncrementId(),
            'order_date' => $this->timezone->date($orderModel->getCreatedAt()),
            'order_number' => $orderModel->getIncrementId(),
            'status' => $orderModel->getStatusLabel(),
            'email' => $orderModel->getCustomerEmail(),
            'shipping_method' => $orderModel->getShippingDescription(),
            'shipping_address' => $this->orderAddress->getOrderShippingAddress($orderModel),
            'billing_address' => $this->orderAddress->getOrderBillingAddress($orderModel),
            'payment_methods' => $this->orderPayments->getOrderPaymentMethod($orderModel),
            'applied_coupons' => $orderModel->getCouponCode() ? ['code' => $orderModel->getCouponCode()] : [],
            'model' => $orderModel,
        ];
    }
}
