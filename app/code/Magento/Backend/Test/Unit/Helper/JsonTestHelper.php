<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Helper;

use Magento\Framework\Controller\Result\Json;

/**
 * Test helper for Json
 */
class JsonTestHelper extends Json
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Skip parent constructor
     */
    public function __construct()
    {
        // Skip parent constructor
    }

    /**
     * getUser (custom method for testing)
     *
     * @return mixed
     */
    public function getUser()
    {
        return $this->data['user'] ?? null;
    }
}
