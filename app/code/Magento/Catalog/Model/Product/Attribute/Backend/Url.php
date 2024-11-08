<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Model\Product\Attribute\Backend;

use Magento\Catalog\Helper\Data;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;

class Url extends AbstractBackend
{
    /**
     * @var Data
     */
    private Data $helper;

    /**
     * @param Data $helper
     */
    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * Set Attribute instance, Rewrite for redefine attribute scope
     *
     * @param Attribute $attribute
     * @return $this
     */
    public function setAttribute($attribute)
    {
        parent::setAttribute($attribute);
        $this->setScope($attribute);
        return $this;
    }

    /**
     * Redefine Attribute scope
     *
     * @param Attribute $attribute
     * @return void
     */
    private function setScope(Attribute $attribute): void
    {
        if ($this->helper->isUrlScopeWebsite()) {
            $attribute->setIsGlobal(ScopedAttributeInterface::SCOPE_WEBSITE);
        } else {
            $attribute->setIsGlobal(ScopedAttributeInterface::SCOPE_STORE);
        }
    }
}
