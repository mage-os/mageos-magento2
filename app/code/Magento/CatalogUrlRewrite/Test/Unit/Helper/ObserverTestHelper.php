<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogUrlRewrite\Test\Unit\Helper;

use Magento\Framework\Event\Observer;

/**
 * Mock class for Observer with category methods
 */
class ObserverTestHelper extends Observer
{
    /**
     * Mock method for getCategory
     *
     * @return mixed
     */
    public function getCategory()
    {
        return $this->getData('category');
    }
}
