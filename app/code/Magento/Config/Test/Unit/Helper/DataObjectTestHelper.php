<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Config\Test\Unit\Helper;

use Magento\Framework\DataObject;

/**
 * Test helper for DataObject
 */
class DataObjectTestHelper extends DataObject
{
    public function __construct(array $data = [])
    {
        $this->_data = $data;
    }
}
