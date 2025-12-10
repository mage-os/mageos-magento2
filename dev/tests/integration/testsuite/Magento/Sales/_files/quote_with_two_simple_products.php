<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Customer/_files/customer.php');

$objectManager = Bootstrap::getObjectManager();
/** @var StoreManagerInterface $storeManager */
$storeManager = $objectManager->get(StoreManagerInterface::class);
$product1 = $objectManager->create(Product::class);
$product1->setTypeId(Type::TYPE_SIMPLE)
    ->setId(10)
    ->setAttributeSetId(4)
    ->setName('Simple Product 1')
    ->setSku('simple-product-1')
    ->setPrice(10)
    ->setTaxClassId(0)
    ->setMetaTitle('Simple Product 1')
    ->setMetaKeyword('simple product 1')
    ->setMetaDescription('Simple Product 1 Description')
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setStockData(
        [
            'use_config_manage_stock' => 1,
            'qty' => 100,
            'is_qty_decimal' => 0,
            'is_in_stock' => 1,
        ]
    )
    ->setWebsiteIds([$storeManager->getStore()->getWebsiteId()])
    ->save();

$product2 = $objectManager->create(Product::class);
$product2->setTypeId(Type::TYPE_SIMPLE)
    ->setId(11)
    ->setAttributeSetId(4)
    ->setName('Simple Product 2')
    ->setSku('simple-product-2')
    ->setPrice(20)
    ->setTaxClassId(0)
    ->setMetaTitle('Simple Product 2')
    ->setMetaKeyword('simple product 2')
    ->setMetaDescription('Simple Product 2 Description')
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setStockData(
        [
            'use_config_manage_stock' => 1,
            'qty' => 100,
            'is_qty_decimal' => 0,
            'is_in_stock' => 1,
        ]
    )
    ->setWebsiteIds([$storeManager->getStore()->getWebsiteId()])
    ->save();

/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->create(ProductRepositoryInterface::class);
$product1 = $productRepository->get('simple-product-1');
$product2 = $productRepository->get('simple-product-2');
$addressData = include __DIR__ . '/address_data.php';
$billingAddress = $objectManager->create(
    Address::class,
    ['data' => $addressData]
);
$billingAddress->setAddressType('billing');
$shippingAddress = clone $billingAddress;
$shippingAddress->setId(null)->setAddressType('shipping');
$store = $storeManager->getStore();
/** @var CustomerRepositoryInterface $customerRepository */
$customerRepository = $objectManager->create(CustomerRepositoryInterface::class);
$customer = $customerRepository->getById(1);
/** @var Quote $quote */
$quote = $objectManager->create(Quote::class);
$quote->setStoreId($store->getId())
    ->setReservedOrderId('test_quote_two_products')
    ->setBillingAddress($billingAddress)
    ->setShippingAddress($shippingAddress);
$quote->setCustomer($customer)->setCustomerIsGuest(false)->save();
foreach ($quote->getAllAddresses() as $address) {
    $address->setCustomerId(1)->save();
}
$quote->addProduct($product1, 2);
$quote->addProduct($product2, 1);
$quote->getShippingAddress()
    ->setShippingMethod('flatrate_flatrate')
    ->setShippingDescription('Flat Rate - Fixed')
    ->setCollectShippingRates(true)
    ->collectShippingRates();
$quote->getPayment()->setMethod('checkmo');
$quote->setIsMultiShipping(0);
$quote->collectTotals();
/** @var CartRepositoryInterface $quoteRepository */
$quoteRepository = $objectManager->get(CartRepositoryInterface::class);
$quoteRepository->save($quote);
/** @var QuoteIdMask $quoteIdMask */
$quoteIdMask = $objectManager->create(QuoteIdMaskFactory::class)->create();
$quoteIdMask->setQuoteId($quote->getId());
$quoteIdMask->setDataChanges(true);
$quoteIdMask->save();
