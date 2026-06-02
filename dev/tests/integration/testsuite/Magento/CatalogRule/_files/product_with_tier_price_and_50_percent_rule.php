<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

/**
 * Creates a simple product ($100) with an all-groups global tier price ($30 for qty=1)
 * and a 50% catalog price rule for all customer groups on website 1.
 *
 * Scenario:
 *   - Regular price:  $100
 *   - Tier price:     $30  (all groups, qty=1, global)
 *   - Rule:           50% off
 *   - Rule on regular: $50  — HIGHER than tier → without fix, rule raises effective price
 *   - Expected:        rule price ≤ $30 (LEAST logic caps it)
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

/** @var ProductInterfaceFactory $productFactory */
$productFactory      = $objectManager->get(ProductInterfaceFactory::class);
/** @var ProductRepositoryInterface $productRepository */
$productRepository   = $objectManager->get(ProductRepositoryInterface::class);
/** @var ProductTierPriceInterfaceFactory $tierPriceFactory */
$tierPriceFactory    = $objectManager->get(ProductTierPriceInterfaceFactory::class);
/** @var ProductTierPriceExtensionFactory $tierExtFactory */
$tierExtFactory      = $objectManager->get(ProductTierPriceExtensionFactory::class);
/** @var CatalogRuleRepositoryInterface $catalogRuleRepository */
$catalogRuleRepository = $objectManager->get(CatalogRuleRepositoryInterface::class);
/** @var RuleFactory $ruleFactory */
$ruleFactory         = $objectManager->get(RuleFactory::class);

// Create product with regular price $100
$product = $productFactory->create();
$product->setTypeId('simple')
    ->setAttributeSetId($product->getDefaultAttributeSetId())
    ->setWebsiteIds([1])
    ->setName('Simple Product With Tier Price')
    ->setSku('simple-tier-price-rule')
    ->setPrice(100.00)
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setStockData(['use_config_manage_stock' => 1, 'qty' => 100, 'is_in_stock' => 1]);

// Add all-groups global tier price: $30 for qty=1
$tierPrice = $tierPriceFactory->create();
$tierPrice->setCustomerGroupId(Group::CUST_GROUP_ALL);
$tierPrice->setQty(1);
$tierPrice->setValue(30.00);
$tierPrice->setWebsiteId(0);
$tierPrice->setExtensionAttributes($tierExtFactory->create());

$product->setTierPrices([$tierPrice]);
$productRepository->save($product);

// Create 50% off catalog rule for all customer groups on website 1
$rule = $ruleFactory->create();
$rule->loadPost([
    'name'                  => 'Test Catalog Rule 50% off (tier price test)',
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
