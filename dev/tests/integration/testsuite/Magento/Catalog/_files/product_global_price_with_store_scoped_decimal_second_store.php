<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Setup\CategorySetup;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Store/_files/second_store.php');

$objectManager = Bootstrap::getObjectManager();
/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->get(ProductRepositoryInterface::class);
/** @var ProductFactory $productFactory */
$productFactory = $objectManager->get(ProductFactory::class);
/** @var StoreManagerInterface $storeManager */
$storeManager = $objectManager->get(StoreManagerInterface::class);

/** @var CategorySetup $installer */
$installer = $objectManager->create(CategorySetup::class);
$attributeSetId = $installer->getAttributeSetId(Product::ENTITY, 'Default');

$attributeCode = 'decimal_attr_store_scope_pr40419';

$installer->addAttribute(
    Product::ENTITY,
    $attributeCode,
    [
        'type' => 'decimal',
        'label' => 'Store scoped decimal PR40419',
        'input' => 'text',
        'required' => false,
        'global' => ScopedAttributeInterface::SCOPE_STORE,
        'user_defined' => true,
        'visible' => true,
        'group' => 'General',
    ]
);

$objectManager->get(EavConfig::class)->clear();

$sku = 'simple-global-price-store-decimal-pr40419';

$product = $productFactory->create();
$product->setTypeId(Type::TYPE_SIMPLE)
    ->setAttributeSetId($attributeSetId)
    ->setWebsiteIds([$storeManager->getWebsite()->getId()])
    ->setName('Product global price and store scoped decimal')
    ->setSku($sku)
    ->setPrice(77.5)
    ->setWeight(1)
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setStockData(
        [
            'use_config_manage_stock' => 1,
            'qty' => 100,
            'is_in_stock' => 1,
        ]
    );
$product->setCustomAttribute($attributeCode, '1.25');
$productRepository->save($product);

$secondStoreId = (int)$storeManager->getStore('fixture_second_store')->getId();
$currentStoreCode = $storeManager->getStore()->getCode();

try {
    $storeManager->setCurrentStore('fixture_second_store');
    $product = $productRepository->get($sku, false, $secondStoreId, true);
    $product->setCustomAttribute($attributeCode, '9.99');
    $productRepository->save($product);
} finally {
    $storeManager->setCurrentStore($currentStoreCode);
}