<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogUrlRewrite\Test\Unit\Mock;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Framework\Api\CustomAttributesDataInterface;

/**
 * Mock class for CategoryInterface with additional methods
 */
class CategoryInterfaceMock implements CategoryInterface, CustomAttributesDataInterface
{
    private $resource = null;

    /**
     * Mock method for getResource
     *
     * @return mixed
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Set the resource
     *
     * @param mixed $resource
     * @return $this
     */
    public function setResource($resource)
    {
        $this->resource = $resource;
        return $this;
    }

    // Required methods from CategoryInterface
    public function getId(): ?int { return null; }
    public function setId($id): CategoryInterface { return $this; }
    public function getParentId(): ?int { return null; }
    public function setParentId($parentId): CategoryInterface { return $this; }
    public function getName(): ?string { return null; }
    public function setName($name): CategoryInterface { return $this; }
    public function getIsActive(): ?int { return null; }
    public function setIsActive($isActive): CategoryInterface { return $this; }
    public function getPosition(): ?int { return null; }
    public function setPosition($position): CategoryInterface { return $this; }
    public function getLevel(): ?int { return null; }
    public function setLevel($level): CategoryInterface { return $this; }
    public function getPath(): ?string { return null; }
    public function setPath($path): CategoryInterface { return $this; }
    public function getAvailableSortBy(): ?array { return null; }
    public function setAvailableSortBy($availableSortBy): CategoryInterface { return $this; }
    public function getIncludeInMenu(): ?int { return null; }
    public function setIncludeInMenu($includeInMenu): CategoryInterface { return $this; }
    public function getChildren(): ?string { return null; }
    public function setChildren($children): CategoryInterface { return $this; }
    public function getCreatedAt(): ?string { return null; }
    public function setCreatedAt($createdAt): CategoryInterface { return $this; }
    public function getUpdatedAt(): ?string { return null; }
    public function setUpdatedAt($updatedAt): CategoryInterface { return $this; }
    public function getExtensionAttributes(): ?\Magento\Catalog\Api\Data\CategoryExtensionInterface { return null; }
    public function setExtensionAttributes(\Magento\Catalog\Api\Data\CategoryExtensionInterface $extensionAttributes): CategoryInterface { return $this; }

    // Required methods from CustomAttributesDataInterface
    public function getCustomAttributes(): ?array { return null; }
    public function setCustomAttributes(array $customAttributes): CustomAttributesDataInterface { return $this; }
    public function getCustomAttribute($attributeCode): ?\Magento\Framework\Api\AttributeInterface { return null; }
    public function setCustomAttribute($attributeCode, $attributeValue): CustomAttributesDataInterface { return $this; }
}
