<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Downloadable\Test\Unit\Helper;

use Magento\Store\Api\Data\StoreInterface;

/**
 * Test helper class for Store with custom methods
 */
class StoreTestHelper implements StoreInterface
{
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return 1;
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCode()
    {
        return 'default';
    }

    /**
     * @inheritdoc
     */
    public function setCode($code)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Default Store';
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSortOrder()
    {
        return 0;
    }

    /**
     * @inheritdoc
     */
    public function setSortOrder($sortOrder)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getIsActive()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function setIsActive($isActive)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getWebsiteId()
    {
        return 1;
    }

    /**
     * @inheritdoc
     */
    public function setWebsiteId($websiteId)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getStoreGroupId()
    {
        return 1;
    }

    /**
     * @inheritdoc
     */
    public function setStoreGroupId($storeGroupId)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes($extensionAttributes)
    {
        return $this;
    }

    /**
     * Custom getBaseCurrency method for testing
     *
     * @return mixed
     */
    public function getBaseCurrency()
    {
        return null;
    }
}
