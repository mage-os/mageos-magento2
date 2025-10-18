<?php
/**
 * Copyright 2013 Adobe
 * All Rights Reserved.
 */
namespace Magento\Review\Block\Adminhtml\Grid\Renderer;

/**
 * Adminhtml review grid item renderer for item type
 */
class Type extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Render review type
     *
     * @param \Magento\Framework\DataObject $row
     * @return \Magento\Framework\Phrase
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        if ($row->getCustomerId()) {
            return __('Customer');
        }
        if ($row->getStoreId() == \Magento\Store\Model\Store::DEFAULT_STORE_ID) {
            return __('Administrator');
        }
        return __('Guest');
    }
}// Class \Magento\Review\Block\Adminhtml\Grid\Renderer\Type END
