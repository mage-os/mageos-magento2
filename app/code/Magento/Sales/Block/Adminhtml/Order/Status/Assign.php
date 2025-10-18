<?php
/**
 * Copyright 2013 Adobe
 * All Rights Reserved.
 */
namespace Magento\Sales\Block\Adminhtml\Order\Status;

/**
 * @api
 * @since 100.0.2
 */
class Assign extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_order_status';
        $this->_mode = 'assign';
        $this->_blockGroup = 'Magento_Sales';
        parent::_construct();
        $this->buttonList->update('save', 'label', __('Save Status Assignment'));
        $this->buttonList->remove('delete');
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Assign Order Status to State');
    }
}
