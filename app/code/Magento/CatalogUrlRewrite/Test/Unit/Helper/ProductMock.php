<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogUrlRewrite\Test\Unit\Helper;

use Magento\Catalog\Model\Product;

/**
 * Mock class for Product with URL key methods
 */
class ProductMock extends Product
{
    /**
     * Mock method for getUrlKey
     *
     * @return string|null
     */
    public function getUrlKey()
    {
        return $this->getData('url_key');
    }

    /**
     * Mock method for formatUrlKey
     *
     * @param string $str
     * @return string
     */
    public function formatUrlKey($str)
    {
        return $str;
    }

    /**
     * Mock method for setStoreId
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        return $this->setData('store_id', $storeId);
    }

    /**
     * Mock method for load
     *
     * @param int $modelId
     * @param string|null $field
     * @return $this
     */
    public function load($modelId, $field = null)
    {
        return $this;
    }

    /**
     * Mock method for setUrlKey
     *
     * @param string $urlKey
     * @return $this
     */
    public function setUrlKey($urlKey)
    {
        return $this->setData('url_key', $urlKey);
    }

    /**
     * Mock method for getIsChangedCategories
     *
     * @return bool|null
     */
    public function getIsChangedCategories()
    {
        return $this->getData('is_changed_categories');
    }

    /**
     * Mock method for getWebsiteIds
     *
     * @return array
     */
    public function getWebsiteIds()
    {
        return $this->getData('website_ids') ?: [];
    }

    /**
     * Initialize resources
     *
     * @return void
     */
    protected function _construct()
    {
        // Mock implementation - no actual resource initialization needed
    }
}
