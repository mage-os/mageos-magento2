<?php
/**
 * Copyright 2025 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

use Magento\Framework\Registry;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteFactory;
use Magento\Quote\Model\ResourceModel\Quote as QuoteResource;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

$objectManager = Bootstrap::getObjectManager();
/** @var Registry $registry */
$registry = $objectManager->get(Registry::class);
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var QuoteFactory $quoteFactory */
$quoteFactory = $objectManager->get(QuoteFactory::class);
/** @var QuoteResource $quoteResource */
$quoteResource = $objectManager->get(QuoteResource::class);
/** @var Quote $quote */
$quote = $quoteFactory->create();
$quoteResource->load($quote, 'multishipping_subselect_bundle', 'reserved_order_id');
if ($quote->getId()) {
    $quoteResource->delete($quote);
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);

Resolver::getInstance()->requireDataFixture('Magento/Bundle/_files/bundle_product_with_dynamic_price_rollback.php');
