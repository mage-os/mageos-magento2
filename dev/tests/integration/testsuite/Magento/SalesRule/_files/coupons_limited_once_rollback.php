<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

use Magento\Framework\Registry;
use Magento\SalesRule\Model\Coupon;
use Magento\SalesRule\Model\Rule;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
/** @var Registry $registry */
$registry = $objectManager->get(Registry::class);
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);
$coupon = $objectManager->create(Coupon::class);
$coupon->loadByCode('test_once_usage');
if ($coupon->getId()) {
    $ruleId = $coupon->getRuleId();
    $coupon->delete();
    $rule = $objectManager->create(Rule::class);
    $rule->load($ruleId);
    if ($rule->getId()) {
        $rule->delete();
    }
}
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
