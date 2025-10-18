<?php
/**
 * Copyright 2013 Adobe
 * All Rights Reserved.
 */

/**
 * Block form after
 */
namespace Magento\ImportExport\Block\Adminhtml\Form;

/**
 * @api
 * @since 100.0.2
 */
class After extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Get current operation
     *
     * @return \Magento\Framework\Model\AbstractModel
     */
    public function getOperation()
    {
        return $this->_registry->registry('current_operation');
    }
}
