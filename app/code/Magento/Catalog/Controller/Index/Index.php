<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Catalog\Controller\Index;

use Magento\Framework\App\Action\HttpGetActionInterface;

/**
 * Catalog index page controller.
 */
class Index extends \Magento\Framework\App\Action\Action implements HttpGetActionInterface
{
    /**
     * Index action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        echo "123 33 e";


        echo "11 121 d dsd d2c  d testdd 11"
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('/');
    }
}
