<?php
/**
 * Copyright 2013 Adobe
 * All Rights Reserved.
 */

/**
 * Price display type source model
 */
namespace Magento\Tax\Model\System\Config\Source\Tax\Display;

class Type implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var array
     */
    protected $_options;

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = [];
            $this->_options[] = [
                'value' => \Magento\Tax\Model\Config::DISPLAY_TYPE_EXCLUDING_TAX,
                'label' => __('Excluding Tax'),
            ];
            $this->_options[] = [
                'value' => \Magento\Tax\Model\Config::DISPLAY_TYPE_INCLUDING_TAX,
                'label' => __('Including Tax'),
            ];
            $this->_options[] = [
                'value' => \Magento\Tax\Model\Config::DISPLAY_TYPE_BOTH,
                'label' => __('Including and Excluding Tax'),
            ];
        }
        return $this->_options;
    }
}
