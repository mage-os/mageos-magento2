<?php
/**
 * Copyright 2025 Mage-OS
 * All Rights Reserved.
 *
 * Multi-address quote whose single shipping address carries a matching item followed by a
 * non-matching item. Reproduces the ACP2E-3986 ordering scenario for Subselect::validate():
 * the subselect must still apply when the matching line comes before a non-matching line,
 * regardless of item order.
 *
 * NOTE: integration fixture — requires the integration test database to execute.
 */
declare(strict_types=1);

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Rate;
use Magento\Quote\Model\QuoteFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->get(ProductRepositoryInterface::class);
/** @var StoreManagerInterface $storeManager */
$storeManager = $objectManager->get(StoreManagerInterface::class);
$storeId = (int) $storeManager->getStore()->getId();

$createSimple = static function (string $sku, float $price, string $name) use ($objectManager, $storeManager) {
    /** @var Product $product */
    $product = $objectManager->create(Product::class);
    $product->setTypeId(Type::TYPE_SIMPLE)
        ->setAttributeSetId($product->getDefaultAttributeSetId())
        ->setWebsiteIds([$storeManager->getWebsite('base')->getId()])
        ->setName($name)
        ->setSku($sku)
        ->setPrice($price)
        ->setVisibility(Visibility::VISIBILITY_BOTH)
        ->setStatus(Status::STATUS_ENABLED)
        ->setStockData(['use_config_manage_stock' => 1, 'qty' => 100, 'is_in_stock' => 1]);
    return $product;
};

/** @var ProductRepositoryInterface $productRepository */
$matching = $productRepository->save($createSimple('subselect_match', 100.00, 'Subselect Matching'));
$nonMatching = $productRepository->save($createSimple('subselect_other', 50.00, 'Subselect Non Matching'));

/** @var QuoteFactory $quoteFactory */
$quoteFactory = $objectManager->get(QuoteFactory::class);
/** @var Quote $quote */
$quote = $quoteFactory->create();
$quote->setReservedOrderId('multishipping_subselect_ordering')
    ->setStoreId($storeId)
    ->setCustomerEmail('multishipping_ordering@test.com')
    ->setCustomerIsGuest(true);

// Add the matching item first, the non-matching item second (the order that triggered ACP2E-3986).
$quote->addProduct($matching, 2);
$quote->addProduct($nonMatching, 1);

$shippingData = [
    'firstname' => 'John',
    'lastname' => 'Doe',
    'telephone' => '0333-233-221',
    'street' => ['Main Division 1'],
    'city' => 'Culver City',
    'region' => 'CA',
    'postcode' => '90800',
    'country_id' => 'US',
    'email' => 'multishipping_ordering@shipping.test',
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
    'email' => 'multishipping_ordering@billing.test',
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

$shippingAddresses = $quote->getAllShippingAddresses();
$shippingAddress = reset($shippingAddresses);
foreach ($quote->getAllVisibleItems() as $visibleItem) {
    $shippingAddress->addItem($visibleItem);
}

$quote->collectTotals();
/** @var CartRepositoryInterface $quoteRepository */
$quoteRepository = $objectManager->get(CartRepositoryInterface::class);
$quoteRepository->save($quote);
