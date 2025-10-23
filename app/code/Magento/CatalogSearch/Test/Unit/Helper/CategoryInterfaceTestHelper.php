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
 * 
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
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
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setName($name)
    {
        return $this;
    }
    public function getIsActive()
    {
        return null;
    }
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setIsActive($isActive)
    {
        return $this;
    }
    public function getPosition()
    {
        return null;
    }
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setPosition($position)
    {
        return $this;
    }
    public function getLevel()
    {
        return null;
    }
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setLevel($level)
    {
        return $this;
    }
    public function getPath()
    {
        return null;
    }
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setPath($path)
    {
        return $this;
    }
    public function getAvailableSortBy()
    {
        return null;
    }
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setAvailableSortBy($availableSortBy)
    {
        return $this;
    }
    public function getIncludeInMenu()
    {
        return null;
    }
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setIncludeInMenu($includeInMenu)
    {
        return $this;
    }
    public function getChildren()
    {
        return null;
    }
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setChildren($children)
    {
        return $this;
    }
    public function getCreatedAt()
    {
        return null;
    }
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setCreatedAt($createdAt)
    {
        return $this;
    }
    public function getUpdatedAt()
    {
        return null;
    }
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this;
    }
    public function getExtensionAttributes()
    {
        return null;
    }
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setExtensionAttributes($extensionAttributes)
    {
        return $this;
    }

    // Required methods from CustomAttributesDataInterface
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getCustomAttribute($attributeCode)
    {
        return null;
    }
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setCustomAttribute($attributeCode, $attributeValue)
    {
        return $this;
    }
    public function getCustomAttributes()
    {
        return null;
    }
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setCustomAttributes($attributes)
    {
        return $this;
    }
}
