<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Multi-address quote whose single shipping address carries one dynamic-price bundle.
 * The bundle selects simple1 (special price 5.99) and simple2 (special price 15.99), so its visible
 * row total is 21.98, while the two bundle child rows carry 5.99 and 15.99. Address::addItem()
 * creates a parent address item plus two child address items, so the pre-fix getAllItems() path
 * summed 21.98 + 5.99 + 15.99 = 43.96 (double count). Only the parent may be counted: 21.98.
 *
 * NOTE: integration fixture — requires the integration test database to execute.
 */
declare(strict_types=1);

use Magento\Bundle\Model\Product\Type as BundleType;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\DataObject;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Rate;
use Magento\Quote\Model\QuoteFactory;
use Magento\Quote\Model\ResourceModel\Quote as QuoteResource;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Bundle/_files/bundle_product_with_dynamic_price.php');

$objectManager = Bootstrap::getObjectManager();
/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->get(ProductRepositoryInterface::class);
/** @var StoreManagerInterface $storeManager */
$storeManager = $objectManager->get(StoreManagerInterface::class);
$storeId = (int) $storeManager->getStore()->getId();

$bundle = $productRepository->get('bundle_product_with_dynamic_price');

// Build a buy request that picks one selection from each required option.
/** @var BundleType $typeInstance */
$typeInstance = $bundle->getTypeInstance();
$typeInstance->setStoreFilter($bundle->getStoreId(), $bundle);
$optionCollection = $typeInstance->getOptionsCollection($bundle);
$bundleOptions = [];
$bundleOptionsQty = [];
foreach ($optionCollection as $option) {
    /** @var \Magento\Bundle\Model\Option $option */
    $selectionsCollection = $typeInstance->getSelectionsCollection([$option->getId()], $bundle);
    $bundleOptions[$option->getId()] = $selectionsCollection->getFirstItem()->getSelectionId();
    $bundleOptionsQty[$option->getId()] = 1;
}
$buyRequest = new DataObject(
    [
        'product' => $bundle->getId(),
        'bundle_option' => $bundleOptions,
        'bundle_option_qty' => $bundleOptionsQty,
        'qty' => 1,
    ]
);

/** @var QuoteFactory $quoteFactory */
$quoteFactory = $objectManager->get(QuoteFactory::class);
/** @var Quote $quote */
$quote = $quoteFactory->create();
$quote->setReservedOrderId('multishipping_subselect_bundle')
    ->setStoreId($storeId)
    ->setCustomerEmail('multishipping_bundle@test.com')
    ->setCustomerIsGuest(true);

$quote->addProduct($bundle, $buyRequest);

$shippingData = [
    'firstname' => 'John',
    'lastname' => 'Doe',
    'telephone' => '0333-233-221',
    'street' => ['Main Division 1'],
    'city' => 'Culver City',
    'region' => 'CA',
    'postcode' => '90800',
    'country_id' => 'US',
    'email' => 'multishipping_bundle@shipping.test',
    'address_type' => 'shipping',
];
$billingData = [
    'firstname' => 'John',
    'lastname' => 'Doe',
    'telephone' => '0333-233-221',
    'street' => ['Third Division 1'],
    'city' => 'New York',
    'region' => 'NY',
    'postcode' => '10029',
    'country_id' => 'US',
    'email' => 'multishipping_bundle@billing.test',
    'address_type' => 'billing',
];

/** @var Rate $rate */
$rate = $objectManager->create(Rate::class);
$rate->setCode('flatrate_flatrate')->setPrice(5.00);
/** @var AddressInterface $shipping */
$shipping = $objectManager->create(AddressInterface::class, ['data' => $shippingData]);
$shipping->setShippingMethod('flatrate_flatrate')
    ->addShippingRate($rate)
    ->setShippingAmount(5.00)
    ->setBaseShippingAmount(5.00);
$quote->addAddress($shipping);

/** @var AddressInterface $billing */
$billing = $objectManager->create(AddressInterface::class, ['data' => $billingData]);
$quote->setBillingAddress($billing);

$quote->setIsMultiShipping(1);

// Save and reload the quote so its items have IDs before address items reference them.
$quote->collectTotals();
/** @var CartRepositoryInterface $quoteRepository */
$quoteRepository = $objectManager->get(CartRepositoryInterface::class);
$quoteRepository->save($quote);

/** @var QuoteResource $quoteResource */
$quoteResource = $objectManager->get(QuoteResource::class);
$quote = $quoteFactory->create();
$quoteResource->load($quote, 'multishipping_subselect_bundle', 'reserved_order_id');

$shippingAddresses = $quote->getAllShippingAddresses();
$shippingAddress = reset($shippingAddresses);
foreach ($quote->getAllVisibleItems() as $visibleItem) {
    $shippingAddress->addItem($visibleItem);
}

$quote->setTotalsCollectedFlag(false);
$quote->collectTotals();
$quoteRepository->save($quote);
