<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Webapi\Test\Unit\Helper;

use Magento\Framework\Webapi\Request;

/**
 * Test helper for Magento\Framework\Webapi\Request
 *
 * WHY THIS HELPER IS REQUIRED:
 * - Parent Webapi\Request has complex constructor requiring 4 dependencies:
 *   CookieReaderInterface, StringUtils, AreaList, ScopeInterface (line 42-48)
 * - Parent getPostValue() EXISTS (inherited from PhpEnvironment\Request line 564)
 * - Parent setPostValue() EXISTS but takes key-value pairs, not full array replacement
 * - setPostData() does NOT exist in parent - this is a custom test method
 * - Provides simple array-based post data storage for unit tests
 *
 * Used By:
 * - magento2ee/app/code/Magento/AdminGws/Test/Unit/Model/Plugin/SaveRoleTest.php
 *
 * Cannot be replaced with:
 * - createMock(Request::class) → Constructor has 4 required dependencies
 * - Parent setPostValue() → Takes key-value pairs, not full array
 */
class RequestTestHelper extends Request
{
    /**
     * @var array<string, mixed>
     */
    private $postData = [];

    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get post value
     *
     * Override parent to use simple array storage instead of Parameters object
     *
     * @param string|null $name
     * @param mixed $default
     * @return mixed
     */
    public function getPostValue($name = null, $default = null)
    {
        if ($name === null) {
            return $this->postData;
        }
        return $this->postData[$name] ?? $default;
    }

    /**
     * Set post data (custom test method)
     *
     * This method does NOT exist in parent Request class.
     * Provides convenient way to set entire post data array for testing.
     *
     * @param array<string, mixed> $data
     * @return $this
     */
    public function setPostData($data)
    {
        $this->postData = $data;
        return $this;
    }
}
