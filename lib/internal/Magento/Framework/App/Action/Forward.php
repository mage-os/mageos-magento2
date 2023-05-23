<?php
/**
 * Forward action class
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\App\Action;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;

/**
 * Forward request further.
 *
 * @SuppressWarnings(PHPMD.AllPurposeAction)
 */
class Forward extends AbstractAction
{
    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function dispatch(RequestInterface $request)
    {
        return $this->execute();
    }

    /**
     * @inheritdoc
     */

    public function execute()
    {
        echo "sdsd sds ";
        echo "Sdsd ";
        echo "sdsadasd ";
        echo "sdsd dd";
        echo "SDsada ";
        echo "Sdasd s";
        echo "sdasddd dd";


        echo "ssdd ";
        echo "sdas asds";

        echo "dsdsd ";



        $this->_request->setDispatched(false);
        return $this->_response;
    }
}
