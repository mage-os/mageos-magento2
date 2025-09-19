<?php
/**
 * Copyright 2014 Adobe
 * All Rights Reserved.
 */
namespace Magento\UrlRewrite\Controller\Adminhtml\Url\Rewrite;

class ProductGrid extends \Magento\UrlRewrite\Controller\Adminhtml\Url\Rewrite
{
    /**
     * Ajax products grid action
     *
     * @return void
     */
    public function execute()
    {
        $this->getResponse()->setBody(
            $this->_view->getLayout()->createBlock(\Magento\UrlRewrite\Block\Catalog\Product\Grid::class)->toHtml()
        );
    }
}
