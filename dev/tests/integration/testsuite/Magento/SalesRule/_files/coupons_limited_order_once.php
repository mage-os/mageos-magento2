<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

use Magento\Quote\Model\Quote;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;
use Magento\Quote\Api\CouponManagementInterface;

Resolver::getInstance()->requireDataFixture('Magento/SalesRule/_files/coupons_limited_once.php');
Resolver::getInstance()->requireDataFixture('Magento/Sales/_files/quote_with_two_simple_products.php');

$objectManager = Bootstrap::getObjectManager();
/** @var Quote $quote */
$quote = Bootstrap::getObjectManager()->create(Quote::class);
$quote->load('test_quote_two_products', 'reserved_order_id');
$quote->getShippingAddress()
    ->setShippingMethod('flatrate_flatrate')
    ->setShippingDescription('Flat Rate - Fixed')
    ->setCollectShippingRates(true)
    ->collectShippingRates()
    ->save();
/** @var CouponManagementInterface $couponManagement */
$couponManagement = Bootstrap::getObjectManager()->get(CouponManagementInterface::class);
$couponManagement->set($quote->getId(), 'test_once_usage');
