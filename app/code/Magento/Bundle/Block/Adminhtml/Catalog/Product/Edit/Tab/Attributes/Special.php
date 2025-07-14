<?php
/**
 * Copyright 2013 Adobe
 * All Rights Reserved.
 */
namespace Magento\Bundle\Block\Adminhtml\Catalog\Product\Edit\Tab\Attributes;

/**
 * Bundle Special Price Attribute Block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Special extends \Magento\Catalog\Block\Adminhtml\Form\Renderer\Fieldset\Element
{
    /**
     * @return string
     */
    public function getElementHtml()
    {
        $html = '<input id="' . $this->getElement()->getHtmlId()
            . '" name="' . $this->getElement()->getName()
            . '" value="' . $this->getElement()->getEscapedValue() . '" '
            . $this->getElement()->serialize($this->getElement()->getHtmlAttributes()) . '/>'
            . "\n" . '<label class="addafter" for="' . $this->getElement()->getHtmlId()
            . '"><strong>[%]</strong></label>';
        return $html;
    }
}
