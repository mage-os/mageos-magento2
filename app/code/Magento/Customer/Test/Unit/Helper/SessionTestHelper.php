<?php
/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Customer\Model\Session;

/**
 * Test helper for Session with custom methods
 */
class SessionTestHelper extends Session
{
    /**
     * @var array<string, mixed>
     */
    private array $testData = [];

    /**
     * Constructor that skips parent to avoid dependency injection
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get before request params
     *
     * @return array|null
     */
    public function getBeforeRequestParams()
    {
        return $this->testData['before_request_params'] ?? null;
    }

    /**
     * Set before request params
     *
     * @param array $params
     * @return $this
     */
    public function setBeforeRequestParams(array $params)
    {
        $this->testData['before_request_params'] = $params;
        return $this;
    }
}
