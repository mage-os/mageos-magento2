<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
namespace Magento\Quote\Model\Cart;

use Magento\Quote\Api\Data\TotalsAdditionalDataInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * @inheritDoc
 */
class TotalsAdditionalData extends AbstractExtensibleModel implements TotalsAdditionalDataInterface
{
    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magento\Quote\Api\Data\TotalsAdditionalDataExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     *
     * @param \Magento\Quote\Api\Data\TotalsAdditionalDataExtensionInterface $extensionAttributes
     * @return void
     */
    public function setExtensionAttributes(
        \Magento\Quote\Api\Data\TotalsAdditionalDataExtensionInterface $extensionAttributes
    ) {
        $this->_setExtensionAttributes($extensionAttributes);
    }
}
