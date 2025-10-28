<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\Category;

/**
 * Mock class for Category with URL and product change methods
 */
class CategoryTestHelper extends Category
{
    /**
     * Mock method for getChangedProductIds
     *
     * @return array
     */
    public function getChangedProductIds()
    {
        return $this->getData('changed_product_ids') ?: [];
    }

    /**
     * Mock method for getUrlPath
     *
     * @return string|null
     */
    public function getUrlPath()
    {
        return $this->getData('url_path');
    }

    /**
     * Mock method for setUrlPath
     *
     * @param string $urlPath
     * @return $this
     */
    public function setUrlPath($urlPath)
    {
        return $this->setData('url_path', $urlPath);
    }

    /**
     * Mock method for unsUrlPath (unset url_path)
     *
     * @return $this
     */
    public function unsUrlPath()
    {
        return $this->unsetData('url_path');
    }

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
     * Initialize resources
     *
     * @return void
     */
    protected function _construct()
    {
        // Mock implementation - no actual resource initialization needed
    }
}
