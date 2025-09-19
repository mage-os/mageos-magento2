<?php
/**
 * Copyright 2013 Adobe
 * All Rights Reserved.
 */
namespace Magento\Sales\Model\Order\Creditmemo\Total;

/**
 * Base class for credit memo total
 * phpcs:disable Magento2.Classes.AbstractApi
 * @api
 *
 * @since 100.0.2
 */
abstract class AbstractTotal extends \Magento\Sales\Model\Order\Total\AbstractTotal
{
    /**
     * Collect credit memo subtotal
     *
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function collect(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
        return $this;
    }
}
