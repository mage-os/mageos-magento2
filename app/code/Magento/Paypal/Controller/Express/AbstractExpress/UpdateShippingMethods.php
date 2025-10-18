<?php
/**
 * Copyright 2014 Adobe
 * All Rights Reserved.
 */
namespace Magento\Paypal\Controller\Express\AbstractExpress;

class UpdateShippingMethods extends \Magento\Paypal\Controller\Express\AbstractExpress
{
    /**
     * Update Order (combined action for ajax and regular request)
     *
     * @return void
     */
    public function execute()
    {
        try {
            $this->_initCheckout();
            $this->_checkout->prepareOrderReview($this->_initToken());
            $this->_view->loadLayout('paypal_express_review');

            $this->getResponse()->setBody(
                $this->_view
                    ->getLayout()
                    ->getBlock('express.review.shipping.method')
                    ->setQuote($this->_getQuote())
                    ->toHtml()
            );
            return;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                $e->getMessage()
            );
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('We can\'t update shipping method.')
            );
        }
        $this->getResponse()->setBody(
            '<script>window.location.href = ' . $this->_url->getUrl('*/*/review') . ';</script>'
        );
    }
}
