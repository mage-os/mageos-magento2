<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
namespace Magento\Backend\Plugin;

use Magento\Backend\Controller\Adminhtml\System\Store\Save;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Cache\TypeListInterface;

class StoreSaveFlushConfig
{
    /**
     * @var TypeListInterface
     */
    private $cacheTypeList;

    /**
     * @param TypeListInterface $cacheTypeList
     */
    public function __construct(
        TypeListInterface $cacheTypeList
    ) {
        $this->cacheTypeList = $cacheTypeList;
    }

    /**
     * Flush config cache after store save
     *
     * @param Save $subject
     * @param Redirect $result
     * @return Redirect
     */
    public function afterExecute(Save $subject, $result)
    {
        $postData = $subject->getRequest()->getPostValue();
        if (isset($postData['store_type']) && $postData['store_type'] === 'store') {
            $this->cacheTypeList->cleanType('config');
        }
        return $result;
    }
}
