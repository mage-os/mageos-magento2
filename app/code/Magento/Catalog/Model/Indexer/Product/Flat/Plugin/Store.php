<?php
/**
 * Copyright 2014 Adobe
 * All Rights Reserved.
 */

namespace Magento\Catalog\Model\Indexer\Product\Flat\Plugin;

use Magento\Catalog\Model\Indexer\Product\Flat\Processor;
use Magento\Framework\Model\AbstractModel;
use Magento\Store\Model\ResourceModel\Store as StoreResourceModel;

class Store
{
    /**
     * Product flat indexer processor
     *
     * @var Processor
     */
    protected $_productFlatIndexerProcessor;

    /**
     * @param Processor $productFlatIndexerProcessor
     */
    public function __construct(Processor $productFlatIndexerProcessor)
    {
        $this->_productFlatIndexerProcessor = $productFlatIndexerProcessor;
    }

    /**
     * Before save handler
     *
     * @param StoreResourceModel $subject
     * @param StoreResourceModel $result
     * @param AbstractModel $object
     *
     * @return StoreResourceModel
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSave(StoreResourceModel $subject, StoreResourceModel $result, AbstractModel $object)
    {
        if ($object->isObjectNew() || $object->dataHasChangedFor('group_id')) {
            $this->_productFlatIndexerProcessor->markIndexerAsInvalid();
        }

        return $result;
    }
}
