<?php
/**
 * Copyright 2014 Adobe
 * All Rights Reserved.
 */
namespace Magento\Indexer\Controller\Adminhtml;

/**
 * Abstract class used as part of inheritance tree for Indexer controllers
 */
abstract class Indexer extends \Magento\Backend\App\Action
{
    /**
     * Check ACL permissions
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        switch ($this->_request->getActionName()) {
            case 'list':
                return $this->_authorization->isAllowed('Magento_Indexer::index');
            case 'massOnTheFly':
            case 'massChangelog':
                return $this->_authorization->isAllowed('Magento_Indexer::changeMode');
            case 'massInvalidate':
                return $this->_authorization->isAllowed('Magento_Indexer::invalidate');
        }
        return false;
    }
}
