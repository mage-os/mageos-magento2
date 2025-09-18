<?php
/**
 * Copyright 2014 Adobe
 * All Rights Reserved.
 */
namespace Magento\CatalogRule\Controller\Adminhtml\Promo\Catalog;

class Chooser extends \Magento\CatalogRule\Controller\Adminhtml\Promo\Catalog
{
    /**
     * @return void
     */
    public function execute()
    {
        if ($this->getRequest()->getParam('attribute') == 'sku') {
            $type = \Magento\CatalogRule\Block\Adminhtml\Promo\Widget\Chooser\Sku::class;
        }
        if (!empty($type)) {
            $block = $this->_view->getLayout()->createBlock($type);
            if ($block) {
                $this->getResponse()->setBody($block->toHtml());
            }
        }
    }
}
