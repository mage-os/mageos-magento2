<?php
/**
 * Copyright 2014 Adobe
 * All Rights Reserved.
 */
namespace Magento\Sales\Controller\Guest;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class PrintAction extends \Magento\Sales\Controller\AbstractController\PrintAction
{
    /**
     * @param Context $context
     * @param OrderLoader $orderLoader
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Magento\Sales\Controller\Guest\OrderLoader $orderLoader,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context, $orderLoader, $resultPageFactory);
    }
}
