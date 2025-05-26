<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\ViewModel\Attribute;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Catalog\Helper\Data as CatalogHelper;

class ProductAttributeHelper implements ArgumentInterface
{
    private CatalogHelper $catalogHelper;

    public function __construct(CatalogHelper $catalogHelper)
    {
        $this->catalogHelper = $catalogHelper;
    }

    public function getCatalogHelper(): CatalogHelper
    {
        return $this->catalogHelper;
    }
}
