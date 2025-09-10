<?php
/**
 * Copyright 2013 Adobe
 * All Rights Reserved.
 */
namespace Magento\Sales\Model\Order\Invoice\Total;

/**
 * Order invoice shipping total calculation model
 */
class Shipping extends AbstractTotal
{
    /**
     * Collect shipping total
     *
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        $orderShippingAmount = $invoice->getOrder()->getShippingAmount();
        $baseOrderShippingAmount = $invoice->getOrder()->getBaseShippingAmount();
        $shippingInclTax = $invoice->getOrder()->getShippingInclTax();
        $baseShippingInclTax = $invoice->getOrder()->getBaseShippingInclTax();
        if ($orderShippingAmount) {
            /**
             * Check shipping amount in previous invoices
             */
            foreach ($invoice->getOrder()->getInvoiceCollection() as $previousInvoice) {
                if ($previousInvoice->getShippingAmount() !== null && !$previousInvoice->isCanceled()) {
                    return $this;
                }
            }
            $invoice->setShippingAmount($orderShippingAmount);
            $invoice->setBaseShippingAmount($baseOrderShippingAmount);
            $invoice->setShippingInclTax($shippingInclTax);
            $invoice->setBaseShippingInclTax($baseShippingInclTax);

            $invoice->setGrandTotal($invoice->getGrandTotal() + $orderShippingAmount);
            $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $baseOrderShippingAmount);
        }
        return $this;
    }
}
