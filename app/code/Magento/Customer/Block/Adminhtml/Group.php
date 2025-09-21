<?php
/**
 * Copyright 2013 Adobe
 * All Rights Reserved.
 */

/**
 * Adminhtml customers group page content block
 */
namespace Magento\Customer\Block\Adminhtml;

/**
 * @api
 * @since 100.0.2
 */
class Group extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Modify header & button labels
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'customer_group';
        $this->_headerText = __('Customer Groups');
        $this->_addButtonLabel = __('Add New Customer Group');
        parent::_construct();
    }

    /**
     * Redefine header css class
     *
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'icon-head head-customer-groups';
    }
}
