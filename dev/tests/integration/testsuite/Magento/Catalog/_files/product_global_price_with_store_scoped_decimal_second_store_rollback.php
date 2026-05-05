<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 *
 * Reverts product_global_price_with_store_scoped_decimal_second_store.php so extra store views
 * do not duplicate rows in catalog_product_index_eav for other integration tests.
 */
declare(strict_types=1);

use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
/** @var Registry $registry */
$registry = $objectManager->get(Registry::class);
/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->get(ProductRepositoryInterface::class);
/** @var ProductAttributeRepositoryInterface $attributeRepository */
$attributeRepository = $objectManager->get(ProductAttributeRepositoryInterface::class);
/** @var StoreManagerInterface $storeManager */
$storeManager = $objectManager->get(StoreManagerInterface::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

$sku = 'simple-global-price-store-decimal-pr40419';
$attributeCode = 'decimal_attr_store_scope_pr40419';

try {
    $productRepository->deleteById($sku);
} catch (NoSuchEntityException $e) {
}

try {
    $attribute = $attributeRepository->get($attributeCode);
    $attributeRepository->delete($attribute);
} catch (NoSuchEntityException $e) {
}

$currentStoreCode = $storeManager->getStore()->getCode();
try {
    $storeManager->setCurrentStore(Store::DEFAULT_STORE_ID);
    /** @var Store $fixtureStore */
    $fixtureStore = $objectManager->create(Store::class);
    $fixtureStore->load('fixture_second_store', 'code');
    if ($fixtureStore->getId()) {
        $fixtureStore->delete();
    }
} finally {
    $storeManager->setCurrentStore($currentStoreCode);
}

$objectManager->get(EavConfig::class)->clear();

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);