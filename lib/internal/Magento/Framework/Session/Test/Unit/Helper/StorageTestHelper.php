<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Session\Test\Unit\Helper;

use Magento\Framework\DataObject;
use Magento\Framework\Session\Storage;

class StorageTestHelper extends Storage
{
    /**
     * @return bool
     */
    public function hasCompositeProductResult()
    {
        return true;
    }

    /**
     * @return DataObject
     */
    public function getCompositeProductResult()
    {
        return new DataObject();
    }

    /**
     * @return $this
     */
    public function unsCompositeProductResult()
    {
        return $this;
    }
}

