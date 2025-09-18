<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
namespace Magento\CatalogRule\Plugin\Indexer\Product\Save;

use Magento\CatalogRule\Model\Indexer\Product\ProductRuleProcessor;
use Magento\Catalog\Model\Product;

/**
 * Plugin for Magento\Catalog\Model\Product
 */
class ApplyRulesAfterReindex
{
    /**
     * @var ProductRuleProcessor
     */
    protected $productRuleProcessor;

    /**
     * @param ProductRuleProcessor $productRuleProcessor
     */
    public function __construct(ProductRuleProcessor $productRuleProcessor)
    {
        $this->productRuleProcessor = $productRuleProcessor;
    }

    /**
     * Apply catalog rules after product resource model save
     *
     * @param Product $subject
     * @return void
     */
    public function afterReindex(Product $subject)
    {
        $this->productRuleProcessor->reindexRow($subject->getId());
    }
}
