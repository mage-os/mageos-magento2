<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Fixture;

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
use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\Store\Model\Store;
use Magento\TestFramework\Fixture\RevertibleDataFixtureInterface;

/**
 * Creates catalog_product attribute decimal_store_scoped (store scope) and a simple product using it.
 *
 * Used by integration tests for issue #40218.
 */
class ProductWithStoreScopedDecimalAttribute implements RevertibleDataFixtureInterface
{
    private const ATTRIBUTE_CODE = 'decimal_store_scoped';

    private const PRODUCT_SKU = 'simple_with_store_scoped_decimal';

    public function __construct(
        private readonly ObjectManagerInterface $objectManager,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly ProductAttributeRepositoryInterface $attributeRepository,
        private readonly Registry $registry
    ) {
    }

    /**
     * @inheritdoc
     */
    public function apply(array $data = []): ?DataObject
    {
        /** @var CategorySetup $installer */
        $installer = $this->objectManager->create(CategorySetup::class);
        $entityTypeId = $installer->getEntityTypeId(ProductAttributeInterface::ENTITY_TYPE_CODE);

        /** @var AttributeFactory $attributeFactory */
        $attributeFactory = $this->objectManager->create(AttributeFactory::class);
        $attribute = $attributeFactory->create();

        if (!$attribute->loadByCode($entityTypeId, self::ATTRIBUTE_CODE)->getAttributeId()) {
            $attribute->setData([
                'attribute_code'                => self::ATTRIBUTE_CODE,
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
            $this->attributeRepository->save($attribute);
            $installer->addAttributeToGroup(
                ProductAttributeInterface::ENTITY_TYPE_CODE,
                'Default',
                'General',
                $attribute->getId()
            );
        }

        /** @var Product $product */
        $product = $this->objectManager->create(Product::class);
        $product->setTypeId(Type::TYPE_SIMPLE)
            ->setAttributeSetId(4)
            ->setWebsiteIds([1])
            ->setName('Simple Product With Store Scoped Decimal')
            ->setSku(self::PRODUCT_SKU)
            ->setPrice(50.00)
            ->setVisibility(Visibility::VISIBILITY_BOTH)
            ->setStatus(Status::STATUS_ENABLED)
            ->setStoreId(Store::DEFAULT_STORE_ID)
            ->setStockData(['use_config_manage_stock' => 1, 'qty' => 100, 'is_in_stock' => 1]);

        $product->setCustomAttribute(self::ATTRIBUTE_CODE, '100.00');
        $this->productRepository->save($product);

        $savedProduct = $this->productRepository->get(self::PRODUCT_SKU);
        $savedProduct->setStoreId(1);
        $savedProduct->setCustomAttribute(self::ATTRIBUTE_CODE, '200.00');
        $this->productRepository->save($savedProduct);

        return new DataObject(
            [
                'sku'             => self::PRODUCT_SKU,
                'attribute_code'  => self::ATTRIBUTE_CODE,
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function revert(DataObject $data): void
    {
        $this->registry->unregister('isSecureArea');
        $this->registry->register('isSecureArea', true);
        try {
            try {
                $this->productRepository->deleteById($data->getData('sku') ?: self::PRODUCT_SKU);
            } catch (NoSuchEntityException) {
                // product already removed
            }
            try {
                $this->attributeRepository->deleteById($data->getData('attribute_code') ?: self::ATTRIBUTE_CODE);
            } catch (NoSuchEntityException) {
                // attribute already removed
            }
        } finally {
            $this->registry->unregister('isSecureArea');
            $this->registry->register('isSecureArea', false);
        }
    }
}
