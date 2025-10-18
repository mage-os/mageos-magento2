<?php
/**
 * Copyright 2014 Adobe
 * All Rights Reserved.
 */
namespace Magento\Customer\Controller\Adminhtml\Index;

class Newsletter extends \Magento\Customer\Controller\Adminhtml\Index
{
    /**
     * Customer newsletter grid
     *
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $customerId = $this->initCurrentCustomer();
        /** @var  \Magento\Newsletter\Model\Subscriber $subscriber */
        $subscriber = $this->_objectManager
            ->create(\Magento\Newsletter\Model\Subscriber::class)
            ->loadByCustomerId($customerId);

        $this->_coreRegistry->register('subscriber', $subscriber);
        $resultLayout = $this->resultLayoutFactory->create();
        return $resultLayout;
    }
}
