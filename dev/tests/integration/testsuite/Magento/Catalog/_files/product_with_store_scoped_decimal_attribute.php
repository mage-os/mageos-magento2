<?php

/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */

declare(strict_types=1);

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory;
use Magento\Catalog\Setup\CategorySetup;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Store\Model\Store;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var CategorySetup $installer */
$installer = $objectManager->create(CategorySetup::class);
$entityTypeId = $installer->getEntityTypeId(ProductAttributeInterface::ENTITY_TYPE_CODE);

/** @var AttributeFactory $attributeFactory */
$attributeFactory = $objectManager->create(AttributeFactory::class);
$attribute = $attributeFactory->create();

if (!$attribute->loadByCode($entityTypeId, 'decimal_store_scoped')->getAttributeId()) {
    $attribute->setData([
        'attribute_code'                => 'decimal_store_scoped',
        'entity_type_id'                => $entityTypeId,
        'is_global'                     => ScopedAttributeInterface::SCOPE_STORE,
        'is_user_defined'               => 1,
        'frontend_input'                => 'price',
        'is_unique'                     => 0,
        'is_required'                   => 0,
        'is_searchable'                 => 0,
        'is_visible_in_advanced_search' => 0,
        'is_comparable'                 => 0,
        'is_filterable'                 => 0,
        'is_filterable_in_search'       => 0,
        'is_html_allowed_on_front'      => 0,
        'is_visible_on_front'           => 0,
        'used_in_product_listing'       => 0,
        'frontend_label'                => ['Decimal Store Scoped'],
    ]);

    /** @var ProductAttributeRepositoryInterface $attributeRepository */
    $attributeRepository = $objectManager->create(ProductAttributeRepositoryInterface::class);
    $attributeRepository->save($attribute);

    $installer->addAttributeToGroup(
        ProductAttributeInterface::ENTITY_TYPE_CODE,
        'Default',
        'General',
        $attribute->getId()
    );
}

/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->create(ProductRepositoryInterface::class);

/** @var Product $product */
$product = $objectManager->create(Product::class);
$product->setTypeId(Type::TYPE_SIMPLE)
    ->setAttributeSetId(4)
    ->setWebsiteIds([1])
    ->setName('Simple Product With Store Scoped Decimal')
    ->setSku('simple_with_store_scoped_decimal')
    ->setPrice(50.00)
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setStoreId(Store::DEFAULT_STORE_ID)
    ->setStockData(['use_config_manage_stock' => 1, 'qty' => 100, 'is_in_stock' => 1]);

$product->setCustomAttribute('decimal_store_scoped', '100.00');
$productRepository->save($product);

// Set a different value for the default store view (store ID = 1)
$savedProduct = $productRepository->get('simple_with_store_scoped_decimal');
$savedProduct->setStoreId(1);
$savedProduct->setCustomAttribute('decimal_store_scoped', '200.00');
$productRepository->save($savedProduct);
