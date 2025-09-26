<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogUrlRewrite\Test\Unit\Mock;

use Magento\Framework\Model\AbstractModel;

/**
 * Mock class for AbstractModel with additional methods
 */
class AbstractModelMock extends AbstractModel
{
    private $storeIds = null;
    private $websiteId = null;

    /**
     * Mock method for getStoreIds
     *
     * @return mixed
     */
    public function getStoreIds()
    {
        return $this->storeIds;
    }

    /**
     * Set the store IDs
     *
     * @param mixed $storeIds
     * @return $this
     */
    public function setStoreIds($storeIds)
    {
        $this->storeIds = $storeIds;
        return $this;
    }

    /**
     * Mock method for getWebsiteId
     *
     * @return mixed
     */
    public function getWebsiteId()
    {
        return $this->websiteId;
    }

    /**
     * Set the website ID
     *
     * @param mixed $websiteId
     * @return $this
     */
    public function setWebsiteId($websiteId)
    {
        $this->websiteId = $websiteId;
        return $this;
    }

    /**
     * Required method from AbstractModel
     */
    protected function _construct(): void
    {
        // Mock implementation
    }
}

