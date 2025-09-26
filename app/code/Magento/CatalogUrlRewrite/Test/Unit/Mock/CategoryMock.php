<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogUrlRewrite\Test\Unit\Mock;

use Magento\Catalog\Model\Category;

/**
 * Mock class for Category with URL and product change methods
 */
class CategoryMock extends Category
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
     * Initialize resources
     *
     * @return void
     */
    protected function _construct()
    {
        // Mock implementation - no actual resource initialization needed
    }
}