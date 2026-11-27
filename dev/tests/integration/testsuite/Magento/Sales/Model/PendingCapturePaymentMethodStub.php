<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Sales\Model;

use Magento\Payment\Model\InfoInterface;
use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Sales\Model\Order\Payment;

/**
 * Gateway payment method that captures online, like a deferred method (invoice purchase, direct debit)
 *
 * With payment/mageos_pending_capture/capture_pending set, the gateway only confirms the capture later.
 */
class PendingCapturePaymentMethodStub extends AbstractMethod
{
    public const CODE = 'mageos_pending_capture';

    /**
     * @var string
     */
    protected $_code = self::CODE;

    /**
     * @var bool
     */
    protected $_isGateway = true;

    /**
     * @var bool
     */
    protected $_canCapture = true;

    /**
     * Capture at the gateway, which leaves the transaction pending when capture_pending is set
     *
     * @param InfoInterface $payment
     * @param float $amount
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function capture(InfoInterface $payment, $amount)
    {
        /** @var Payment $payment */
        $payment->setTransactionId('mageos-capture-' . $payment->getOrder()->getIncrementId());
        $payment->setIsTransactionPending((bool)$this->getConfigData('capture_pending'));

        return $this;
    }
}
