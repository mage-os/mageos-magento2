<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
namespace Magento\CatalogInventory\Block\Plugin;

use Magento\CatalogInventory\Model\Product\QuantityValidator;
use Magento\Framework\App\ObjectManager;

class ProductView
{
    /**
     * @var QuantityValidator
     */
    private $productQuantityValidator;

    /**
     * @param QuantityValidator|null $productQuantityValidator
     */
    public function __construct(
        QuantityValidator $productQuantityValidator = null
    ) {
        $this->productQuantityValidator = $productQuantityValidator ?: ObjectManager::getInstance()->get(
            QuantityValidator::class
        );
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
