<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
namespace Magento\CatalogInventory\Block\Plugin;

use Magento\CatalogInventory\Model\Product\QuantityValidator;

class ProductView
{
    /**
     * @var QuantityValidator
     */
    private $productQuantityValidator;

    /**
     * @param QuantityValidator $productQuantityValidator
     */
    public function __construct(
        QuantityValidator $productQuantityValidator
    ) {
        $this->productQuantityValidator = $productQuantityValidator;
    }

    /**
     * Adds quantities validator.
     *
     * @param \Magento\Catalog\Block\Product\View $block
     * @param array $validators
     * @return array
     */
    public function afterGetQuantityValidators(
        \Magento\Catalog\Block\Product\View $block,
        array $validators
    ) {
        return array_merge(
            $validators,
            $this->productQuantityValidator->getData(
                $block->getProduct()->getId(),
                $block->getProduct()->getStore()->getWebsiteId()
            )
        );
    }
}
