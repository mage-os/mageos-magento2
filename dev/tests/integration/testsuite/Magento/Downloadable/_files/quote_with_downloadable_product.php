<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;
use Magento\Quote\Model\Quote\Address;

Resolver::getInstance()->requireDataFixture('Magento/Downloadable/_files/product_downloadable.php');

\Magento\TestFramework\Helper\Bootstrap::getInstance()->loadArea('frontend');
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->create(ProductRepositoryInterface::class);
$product = $productRepository->get('downloadable-product');

$addressData = [
    'telephone' => 3234676,
    'postcode' => 47676,
    'country_id' => 'DE',
    'city' => 'CityX',
    'street' => ['Black str, 48'],
    'lastname' => 'Smith',
    'firstname' => 'John',
    'vat_id' => 12345,
    'address_type' => 'shipping',
    'email' => 'some_email@mail.com',
];

$billingAddress = $objectManager->create(
    Address::class,
    ['data' => $addressData]
);
$billingAddress->setAddressType('billing');
$shippingAddress = clone $billingAddress;
$shippingAddress->setId(null)->setAddressType('shipping');

/** @var \Magento\Quote\Model\Quote $quote */
$quote = $objectManager->create(\Magento\Quote\Model\Quote::class);
$quote->setCustomerIsGuest(
    true
)->setStoreId(
    $objectManager->get(
        \Magento\Store\Model\StoreManagerInterface::class
    )->getStore()->getId()
)->setReservedOrderId(
    'reserved_order_id_1'
)->setIsMultiShipping(
    0
)->setBillingAddress($billingAddress)
->setShippingAddress($shippingAddress)
->addProduct(
    // @phpstan-ignore argument.type
    $product,
    new \Magento\Framework\DataObject([
    // @phpstan-ignore method.notFound
        'links' => array_keys($product->getDownloadableLinks())
    ])
);
$quote->getPayment()->setMethod('checkmo');
$quote->getShippingAddress()->setShippingMethod('flatrate_flatrate')->setCollectShippingRates(true);
$quote->collectTotals();
$quote->save();

/** @var \Magento\Quote\Model\QuoteIdMask $quoteIdMask */
$quoteIdMask = $objectManager
    ->create(\Magento\Quote\Model\QuoteIdMaskFactory::class)
    ->create();
// @phpstan-ignore method.notFound
$quoteIdMask->setQuoteId($quote->getId());
$quoteIdMask->setDataChanges(true);
$quoteIdMask->save();
