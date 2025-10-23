<?php
/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogSearch\Test\Unit\Helper;

use Magento\Catalog\Api\Data\CategoryInterface;

/**
 * Mock class for CategoryInterface with additional methods
 */
class CategoryInterfaceTestHelper implements CategoryInterface
{
    /**
     * @var mixed
     */
    private $isAnchor = null;

    /**
     * Mock method for getIsAnchor
     *
     * @return bool|null
     */
    public function getIsAnchor()
    {
        return $this->isAnchor;
    }

    /**
     * Set the isAnchor value
     *
     * @param bool|null $value
     * @return $this
     */
    public function setIsAnchor($value)
    {
        $this->isAnchor = $value;
        return $this;
    }

    // Required methods from CategoryInterface
    public function getId()
    {
        return null;
    }
    public function setId($id)
    {
        return $this;
    }
    public function getParentId()
    {
        return null;
    }
    public function setParentId($parentId)
    {
        return $this;
    }
    public function getName()
    {
        return null;
    }
    public function setName($name)
    {
        return $this;
    }
    public function getIsActive()
    {
        return null;
    }
    public function setIsActive($isActive)
    {
        return $this;
    }
    public function getPosition()
    {
        return null;
    }
    public function setPosition($position)
    {
        return $this;
    }
    public function getLevel()
    {
        return null;
    }
    public function setLevel($level)
    {
        return $this;
    }
    public function getPath()
    {
        return null;
    }
    public function setPath($path)
    {
        return $this;
    }
    public function getAvailableSortBy()
    {
        return null;
    }
    public function setAvailableSortBy($availableSortBy)
    {
        return $this;
    }
    public function getIncludeInMenu()
    {
        return null;
    }
    public function setIncludeInMenu($includeInMenu)
    {
        return $this;
    }
    public function getChildren()
    {
        return null;
    }
    public function setChildren($children)
    {
        return $this;
    }
    public function getCreatedAt()
    {
        return null;
    }
    public function setCreatedAt($createdAt)
    {
        return $this;
    }
    public function getUpdatedAt()
    {
        return null;
    }
    public function setUpdatedAt($updatedAt)
    {
        return $this;
    }
    public function getExtensionAttributes()
    {
        return null;
    }
    public function setExtensionAttributes($extensionAttributes)
    {
        return $this;
    }

    // Required methods from CustomAttributesDataInterface
    public function getCustomAttribute($attributeCode)
    {
        return null;
    }
    public function setCustomAttribute($attributeCode, $attributeValue)
    {
        return $this;
    }
    public function getCustomAttributes()
    {
        return null;
    }
    public function setCustomAttributes($attributes)
    {
        return $this;
    }
}
