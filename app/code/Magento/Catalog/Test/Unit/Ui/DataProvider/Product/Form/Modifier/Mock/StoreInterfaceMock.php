<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Ui\DataProvider\Product\Form\Modifier\Mock;

use Magento\Store\Api\Data\StoreInterface;

/**
 * Mock class for StoreInterface with additional methods
 */
class StoreInterfaceMock implements StoreInterface
{
    private array $data = [];

    // Required StoreInterface methods
    public function getId(): ?int
    {
        return $this->getData('id');
    }

    public function setId($id)
    {
        return $this->setData('id', $id);
    }

    public function getCode(): ?string
    {
        return $this->getData('code');
    }

    public function setCode($code)
    {
        return $this->setData('code', $code);
    }

    public function getName(): ?string
    {
        return $this->getData('name');
    }

    public function setName($name)
    {
        return $this->setData('name', $name);
    }

    public function getWebsiteId(): ?int
    {
        return $this->getData('website_id');
    }

    public function setWebsiteId($websiteId)
    {
        return $this->setData('website_id', $websiteId);
    }

    public function getStoreGroupId(): ?int
    {
        return $this->getData('store_group_id');
    }

    public function setStoreGroupId($storeGroupId)
    {
        return $this->setData('store_group_id', $storeGroupId);
    }

    public function getIsActive(): ?int
    {
        return $this->getData('is_active');
    }

    public function setIsActive($isActive)
    {
        return $this->setData('is_active', $isActive);
    }

    public function getSortOrder(): ?int
    {
        return $this->getData('sort_order');
    }

    public function setSortOrder($sortOrder)
    {
        return $this->setData('sort_order', $sortOrder);
    }

    public function getExtensionAttributes()
    {
        return $this->getData('extension_attributes');
    }

    public function setExtensionAttributes($extensionAttributes)
    {
        return $this->setData('extension_attributes', $extensionAttributes);
    }

    // Additional methods that were being added via addMethods()
    public function load($id, $field = null)
    {
        return $this;
    }

    public function getConfig($path)
    {
        return $this->getData('config_' . $path);
    }

    // Helper methods
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
}
