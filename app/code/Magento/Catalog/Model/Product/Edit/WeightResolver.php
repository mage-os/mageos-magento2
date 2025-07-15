<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */

namespace Magento\Catalog\Model\Product\Edit;

class WeightResolver
{
    /**
     * Product has weight
     */
    const HAS_WEIGHT = 1;

    /**
     * Product don't have weight
     */
    const HAS_NO_WEIGHT = 0;

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    public function resolveProductHasWeight(\Magento\Catalog\Model\Product $product)
    {
        return (bool) ($product->getData('product_has_weight') == self::HAS_WEIGHT);
    }
}
