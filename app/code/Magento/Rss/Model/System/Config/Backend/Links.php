<?php
/**
 * Copyright 2013 Adobe
 * All Rights Reserved.
 */
namespace Magento\Rss\Model\System\Config\Backend;

/**
 * Cache cleaner backend model
 *
 */
class Links extends \Magento\Framework\App\Config\Value
{
    /**
     * Invalidate cache type, when value was changed
     *
     * @return $this
     */
    public function afterSave()
    {
        if ($this->isValueChanged()) {
            $this->cacheTypeList->invalidate(\Magento\Framework\View\Element\AbstractBlock::CACHE_GROUP);
        }
        return parent::afterSave();
    }
}
