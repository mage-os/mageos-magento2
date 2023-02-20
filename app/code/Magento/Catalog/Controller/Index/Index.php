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
        echo "Test   sds f 11";


        echo "11 121 d dsd d2cdfdd  d testdd 11";
        echo "dd";
        echo "dsds";
        echo "sdsd";

        echo "dsds";
        echo "sdsd";

        echo "dsdsdss";

        echo "dddd dsds";


        echo "ddsd";







        echo "111 vd  dsds usdsd g dsdsd dsd sdsd sdd asdasd asas " ;

        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('/');
    }
}
