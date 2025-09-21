<?php
/**
 * Copyright 2014 Adobe
 * All Rights Reserved.
 */
namespace Magento\Indexer\Block\Backend\Grid\Column\Renderer;

/**
 * Renderer for 'Status' column in indexer grid
 */
class Status extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Render indexer status
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $class = '';
        $text = '';
        switch ($this->_getValue($row)) {
            case \Magento\Framework\Indexer\StateInterface::STATUS_INVALID:
                $class = 'grid-severity-critical';
                $text = __('Reindex required');
                break;
            case \Magento\Framework\Indexer\StateInterface::STATUS_VALID:
                $class = 'grid-severity-notice';
                $text = __('Ready');
                break;
            case \Magento\Framework\Indexer\StateInterface::STATUS_WORKING:
                $class = 'grid-severity-minor';
                $text = __('Processing');
                break;
            case \Magento\Framework\Indexer\StateInterface::STATUS_SUSPENDED:
                $class = 'grid-severity-minor';
                $text = __('Suspended');
                break;
        }
        return '<span class="' . $class . '"><span>' . $text . '</span></span>';
    }
}
