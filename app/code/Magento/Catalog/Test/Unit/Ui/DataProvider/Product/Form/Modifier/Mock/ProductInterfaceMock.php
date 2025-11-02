<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Ui\DataProvider\Product\Form\Modifier\Mock;

use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Mock class for ProductInterface with additional methods
 */
class ProductInterfaceMock implements ProductInterface
{
    private array $data = [];

    // Required ProductInterface methods
    public function getId(): ?int
    {
        return $this->getData('id');
    }

    public function setId($id)
    {
        return $this->setData('id', $id);
    }

    public function getSku(): string
    {
        return $this->getData('sku') ?: '';
    }

    public function setSku($sku)
    {
        return $this->setData('sku', $sku);
    }

    public function getName(): ?string
    {
        return $this->getData('name');
    }

    public function setName($name)
    {
        return $this->setData('name', $name);
    }

    public function getAttributeSetId(): ?int
    {
        return $this->getData('attribute_set_id');
    }

    public function setAttributeSetId($attributeSetId)
    {
        return $this->setData('attribute_set_id', $attributeSetId);
    }

    public function getPrice(): ?float
    {
        return $this->getData('price');
    }

    public function setPrice($price)
    {
        return $this->setData('price', $price);
    }

    public function getStatus(): ?int
    {
        return $this->getData('status');
    }

    public function setStatus($status)
    {
        return $this->setData('status', $status);
    }

    public function getVisibility(): ?int
    {
        return $this->getData('visibility');
    }

    public function setVisibility($visibility)
    {
        return $this->setData('visibility', $visibility);
    }

    public function getTypeId(): ?string
    {
        return $this->getData('type_id');
    }

    public function setTypeId($typeId)
    {
        return $this->setData('type_id', $typeId);
    }

    public function getCreatedAt(): ?string
    {
        return $this->getData('created_at');
    }

    public function setCreatedAt($createdAt)
    {
        return $this->setData('created_at', $createdAt);
    }

    public function getUpdatedAt(): ?string
    {
        return $this->getData('updated_at');
    }

    public function setUpdatedAt($updatedAt)
    {
        return $this->setData('updated_at', $updatedAt);
    }

    public function getWeight(): ?float
    {
        return $this->getData('weight');
    }

    public function setWeight($weight)
    {
        return $this->setData('weight', $weight);
    }

    public function getExtensionAttributes()
    {
        return $this->getData('extension_attributes');
    }

    public function setExtensionAttributes($extensionAttributes)
    {
        return $this->setData('extension_attributes', $extensionAttributes);
    }

    public function getProductLinks(): ?array
    {
        return $this->getData('product_links');
    }

    public function setProductLinks(?array $links = null)
    {
        return $this->setData('product_links', $links);
    }

    public function getOptions(): ?array
    {
        return $this->getData('options');
    }

    public function setOptions(?array $options = null)
    {
        return $this->setData('options', $options);
    }

    public function getMediaGalleryEntries(): ?array
    {
        return $this->getData('media_gallery_entries');
    }

    public function setMediaGalleryEntries(?array $mediaGalleryEntries = null)
    {
        return $this->setData('media_gallery_entries', $mediaGalleryEntries);
    }

    public function getTierPrices(): ?array
    {
        return $this->getData('tier_prices');
    }

    public function setTierPrices(?array $tierPrices = null)
    {
        return $this->setData('tier_prices', $tierPrices);
    }

    // Custom attributes interface methods
    public function getCustomAttributes(): ?array
    {
        return $this->getData('custom_attributes');
    }

    public function setCustomAttributes(?array $customAttributes = null)
    {
        return $this->setData('custom_attributes', $customAttributes);
    }

    public function getCustomAttribute($attributeCode)
    {
        $customAttributes = $this->getCustomAttributes();
        return $customAttributes[$attributeCode] ?? null;
    }

    public function setCustomAttribute($attributeCode, $attributeValue)
    {
        $customAttributes = $this->getCustomAttributes() ?: [];
        $customAttributes[$attributeCode] = $attributeValue;
        return $this->setCustomAttributes($customAttributes);
    }

    // Additional methods that were being added via addMethods()
    public function getStoreId()
    {
        return $this->getData('store_id');
    }

    public function getResource()
    {
        return $this->getData('resource');
    }

    public function getData($key = null)
    {
        if ($key === null) {
            return $this->data;
        }
        return $this->data[$key] ?? null;
    }

    public function setData($key, $value = null)
    {
        if (is_array($key)) {
            $this->data = $key;
        } else {
            $this->data[$key] = $value;
        }
        return $this;
    }

    public function getAttributes()
    {
        return $this->getData('attributes');
    }

    public function getStore()
    {
        return $this->getData('store');
    }

    public function getAttributeDefaultValue($attributeCode)
    {
        return $this->getData('attribute_default_value_' . $attributeCode);
    }

    public function getExistsStoreValueFlag($attributeCode)
    {
        return $this->getData('exists_store_value_flag_' . $attributeCode);
    }

    public function isLockedAttribute($attributeCode)
    {
        return $this->getData('locked_attribute_' . $attributeCode);
    }
}
