<?php
/**
 * Copyright 2014 Adobe
 * All Rights Reserved.
 */
namespace Magento\CheckoutAgreements\Block\Adminhtml;

/**
 * Admin tax rule content block
 */
class Agreement extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_agreement';
        $this->_blockGroup = 'Magento_CheckoutAgreements';
        $this->_headerText = __('Terms and Conditions');
        $this->_addButtonLabel = __('Add New Condition');
        parent::_construct();
    }
}
