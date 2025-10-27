<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Framework\Model\AbstractModel;

class AbstractModelTestHelper extends AbstractModel
{
    /**
     * Constructor that skips parent dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
        $this->_data = [];
    }

    /**
     * Initialize object state with data
     *
     * @param array $data
     * @return void
     */
    public function initializeWithData(array $data): void
    {
        $this->_data = $data;
    }
}

