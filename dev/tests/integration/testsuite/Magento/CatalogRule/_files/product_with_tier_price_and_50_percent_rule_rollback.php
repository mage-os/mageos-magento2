<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogRule\Model\ResourceModel\Rule\Collection as RuleCollection;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

$registry = $objectManager->get(Registry::class);
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

// Remove product
try {
    $productRepository = $objectManager->get(ProductRepositoryInterface::class);
    $productRepository->deleteById('simple-tier-price-rule');
} catch (\Exception $e) {
    // already gone
}

// Remove catalog rules created by fixture
$ruleCollection = $objectManager->create(RuleCollection::class);
$ruleCollection->addFieldToFilter('name', ['like' => '%tier price test%']);
foreach ($ruleCollection as $rule) {
    $rule->delete();
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
