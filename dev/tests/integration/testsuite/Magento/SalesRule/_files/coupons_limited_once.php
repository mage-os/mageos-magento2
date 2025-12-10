<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

use Magento\Customer\Model\ResourceModel\Group\Collection;
use Magento\SalesRule\Model\Coupon;
use Magento\SalesRule\Model\Rule;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\SalesRule\Api\CouponRepositoryInterface;

$objectManager = Bootstrap::getObjectManager();
/** @var Collection $groupCollection */
$groupCollection = $objectManager->get(Collection::class);
/** @var Rule $salesRule */
$salesRule = $objectManager->create(Rule::class);
$salesRule->setData(
    [
        'name' => 'Test Rule with Coupon - 10% Off',
        'is_active' => 1,
        'customer_group_ids' => $groupCollection->getAllIds(),
        'coupon_type' => Rule::COUPON_TYPE_SPECIFIC,
        'simple_action' => Rule::BY_PERCENT_ACTION,
        'discount_amount' => 10,
        'discount_step' => 0,
        'stop_rules_processing' => 1,
        'website_ids' => [
            $objectManager->get(StoreManagerInterface::class)->getWebsite()->getId(),
        ],
    ]
);
$objectManager->get(\Magento\SalesRule\Model\ResourceModel\Rule::class)->save($salesRule);
$coupon = $objectManager->create(Coupon::class);
$coupon->setRuleId($salesRule->getId())
    ->setCode('test_once_usage')
    ->setType(0)
    ->setUsageLimit(1)
    ->setUsagePerCustomer(1);
/** @var CouponRepositoryInterface $couponRepository */
$couponRepository = $objectManager->get(CouponRepositoryInterface::class);
$couponRepository->save($coupon);
