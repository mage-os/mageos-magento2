<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Store\Test\Unit\Helper;

use Magento\Store\Model\Website;

class WebsiteTestHelper extends Website
{
    /**
     * @var mixed
     */
    private $storeIds = null;

    public function __construct()
    {
        // Empty constructor
    }

    /**
     * @return mixed
     */
    public function getStoreIds()
    {
        return $this->storeIds;
    }

    /**
     * @param mixed $storeIds
     * @return $this
     */
    public function setStoreIds($storeIds)
    {
        $this->storeIds = $storeIds;
        return $this;
    }
}

