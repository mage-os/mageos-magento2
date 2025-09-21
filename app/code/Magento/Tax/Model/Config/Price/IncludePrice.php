<?php
/**
 * Copyright 2013 Adobe
 * All Rights Reserved.
 */
namespace Magento\Tax\Model\Config\Price;

class IncludePrice extends \Magento\Framework\App\Config\Value
{
    /**
     * @return $this
     */
    public function afterSave()
    {
        $result = parent::afterSave();
        $this->_cacheManager->clean(['checkout_quote']);

        return $result;
    }
}
