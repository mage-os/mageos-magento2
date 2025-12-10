<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Registry;
use Magento\Quote\Model\Quote;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

$objectManager = Bootstrap::getObjectManager();
/** @var Registry $registry */
$registry = $objectManager->get(Registry::class);
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);
/** @var Quote $quote */
$quote = $objectManager->create(Quote::class);
$quote->load('test_quote_two_products', 'reserved_order_id');
if ($quote->getId()) {
    $quote->delete();
}
$productRepository = $objectManager->get(ProductRepositoryInterface::class);
foreach (['simple-product-1', 'simple-product-2'] as $sku) {
    $product = $productRepository->get($sku);
    if ($product->getId()) {
        $productRepository->delete($product);
    }
}
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);

Resolver::getInstance()->requireDataFixture('Magento/Customer/_files/customer_rollback.php');
