<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

/**
 * Creates a simple product ($100) assigned to BOTH websites with:
 * - An all-groups tier price of $25 scoped to website_id=1 (not global)
 * - A 50% catalog rule for all groups on website 1
 *
 * Tests the `price_tier` JOIN (website-specific, not global).
 */

use Magento\Catalog\Api\Data\ProductInterfaceFactory;
use Magento\Catalog\Api\Data\ProductTierPriceExtensionFactory;
use Magento\Catalog\Api\Data\ProductTierPriceInterfaceFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\CatalogRule\Api\CatalogRuleRepositoryInterface;
use Magento\CatalogRule\Model\RuleFactory;
use Magento\Customer\Model\Group;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

$productFactory      = $objectManager->get(ProductInterfaceFactory::class);
$productRepository   = $objectManager->get(ProductRepositoryInterface::class);
$tierPriceFactory    = $objectManager->get(ProductTierPriceInterfaceFactory::class);
$tierExtFactory      = $objectManager->get(ProductTierPriceExtensionFactory::class);
$catalogRuleRepository = $objectManager->get(CatalogRuleRepositoryInterface::class);
$ruleFactory         = $objectManager->get(RuleFactory::class);

$product = $productFactory->create();
$product->setTypeId('simple')
    ->setAttributeSetId($product->getDefaultAttributeSetId())
    ->setWebsiteIds([1])
    ->setName('Product With Website-Scoped Tier Price')
    ->setSku('simple-website-tier-price-rule')
    ->setPrice(100.00)
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setStockData(['use_config_manage_stock' => 1, 'qty' => 100, 'is_in_stock' => 1]);

// Website-scoped tier price: $25 for website_id=1, all groups, qty=1
$tierPrice = $tierPriceFactory->create();
$tierPrice->setCustomerGroupId(Group::CUST_GROUP_ALL);
$tierPrice->setQty(1);
$tierPrice->setValue(25.00);
$tierPrice->setWebsiteId(1); // website-specific, not global (0)
$tierPrice->setExtensionAttributes($tierExtFactory->create());

$product->setTierPrices([$tierPrice]);
$productRepository->save($product);

$rule = $ruleFactory->create();
$rule->loadPost([
    'name'                  => 'Test Catalog Rule 50% off (website tier test)',
    'is_active'             => '1',
    'stop_rules_processing' => 0,
    'website_ids'           => [1],
    'customer_group_ids'    => [Group::NOT_LOGGED_IN_ID, 1, 2, 3],
    'discount_amount'       => 50,
    'simple_action'         => 'by_percent',
    'from_date'             => '',
    'to_date'               => '',
    'sort_order'            => 0,
    'sub_is_enable'         => 0,
    'sub_discount_amount'   => 0,
    'conditions'            => [],
]);
$catalogRuleRepository->save($rule);
