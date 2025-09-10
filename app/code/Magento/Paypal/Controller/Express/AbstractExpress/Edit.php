<?php
/**
 * Copyright 2014 Adobe
 * All Rights Reserved.
 */
namespace Magento\Paypal\Controller\Express\AbstractExpress;

class Edit extends \Magento\Paypal\Controller\Express\AbstractExpress
{
    /**
     * Dispatch customer back to PayPal for editing payment information
     *
     * @return void
     */
    public function execute()
    {
        try {
            $this->getResponse()->setRedirect($this->_config->getExpressCheckoutEditUrl($this->_initToken()));
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                $e->getMessage()
            );
            $this->_redirect('*/*/review');
        }
    }
}
