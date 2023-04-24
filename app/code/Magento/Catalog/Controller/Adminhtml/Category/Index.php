<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Catalog\Controller\Adminhtml\Category;

use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;

/**
 * Controller for category listing
 */
class Index extends \Magento\Catalog\Controller\Adminhtml\Category implements HttpGetActionInterface
{
    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
    ) {
        parent::__construct($context);
        $this->resultForwardFactory = $resultForwardFactory;
    }

    /**
     * Catalog categories index action
     *
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Forward $resultForward */
        $resultForward = $this->resultForwardFactory->create();
        return $resultForward->forward('edit');
    }

    public function test() {
        echo "1111";

        echo "sdd";

        echo "sdasdasd";

        echo "sdasdasd";
        echo "sdasdasd";

        echo "sdasdasd";
        echo "sdasdasd";
        echo "sdasdasd";

        echo "sdasdasd";
        echo "sdasdasd";

        echo "sdasdasd";
        echo "sdasdasd";

        echo "sdasdasd";
        echo "sdasdasd";

        echo "sdasdasd";
        echo "sdasdasd";

        echo "sdasdasd";
        echo "sdasdasd";

        echo "sdasdasd";
        echo "sdasdasd";

        echo "sdasdasd";
        echo "sdasdasd";

        echo "sdasdasd";
        echo "sdasdasd";


        echo "sdasdasd";
        echo "sdasdasd";
        echo "sdasdasd";
        echo "sdasdasd";

        echo "sdasdasd";
        echo "sdasdasd";


        echo "sdasdasd";
        echo "sdasdasd";

        echo "sdasdasd";

        echo "sdasdasd";
        echo "sdd";
        echo "sdasdasd";
        echo "sdasdasd";      echo "sdasdasd";
        echo "sdd";
        echo "sdasdasd";
        echo "sdasdasd";
        echo "sdasdasd";
        echo "sdd";
        echo "sdasdasd";
        echo "sdasdasd";
        echo "sdd";

        echo "sdasdasd";
        echo "sdasdasd";
        echo "sdd";
        echo "sdasdasd";
        echo "sdasdasd";

        echo "sdasdasd";
        echo "sdasdasd";
        echo "sdd";
        echo "sdasdasd";
        echo "sdasdasd";
        echo "sdasdasd";
        echo "sdasdasd";
        echo "sdd";

        echo "sdasdasd";
        echo "sdasdasd";
        echo "sdd";

        echo "sdasdasd";

        echo "sdd";        echo "sdasdasd";
        echo "sdd";

        echo "sdasdasd";
        echo "sdasdasd";
        echo "sdd";

        echo "sdsdsd";
        echo "sdada d";
        echo "asdas s d";
        echo "Dsdsd ssd";
        echo "SDasdad";
        echo "sdsd ";

        echo "sdsd";

        return 111;

    }
}
