<?php
/**
 * Copyright 2013 Adobe
 * All Rights Reserved.
 */
namespace Magento\Integration\Block\Adminhtml\Widget\Grid\Column\Renderer\Button;

use Magento\Framework\DataObject;
use Magento\Integration\Block\Adminhtml\Widget\Grid\Column\Renderer\Button;

class Delete extends Button
{
    /**
     * Return 'onclick' action for the button (redirect to the integration edit page).
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    protected function _getOnclickAttribute(DataObject $row)
    {
        return sprintf(
            "this.setAttribute('data-url', '%s')",
            $this->getUrl('*/*/delete', ['id' => $row->getId()])
        );
    }

    /**
     * Get title depending on whether element is disabled or not.
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    protected function _getTitleAttribute(DataObject $row)
    {
        return $this->_isDisabled($row) ? __('Uninstall the extension to remove this integration') : __('Remove');
    }

    /**
     * Determine whether current integration came from config file, thus can not be removed
     *
     * @param \Magento\Framework\DataObject $row
     * @return bool
     */
    protected function _isDisabled(DataObject $row)
    {
        return $this->_isConfigBasedIntegration($row);
    }
}
