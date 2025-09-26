<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\ProductAlert\Test\Unit\Block\Product\View\Mock;

use Magento\Catalog\Model\Product;

/**
 * Mock class for Product with getCanShowPrice method
 */
class ProductMock extends Product
{
    /**
     * Mock method for getCanShowPrice
     *
     * @return bool
     */
    public function getCanShowPrice()
    {
        return $this->getData('can_show_price') !== false;
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
