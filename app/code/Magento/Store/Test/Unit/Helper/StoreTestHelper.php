<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Store\Test\Unit\Helper;

use Magento\Store\Model\Store;

/**
 * Test helper for Store with custom methods
 */
class StoreTestHelper extends Store
{
    /**
     * @var string
     */
    private $baseUrl = '';

    /**
     * Constructor that skips parent dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get base URL (custom method for tests)
     *
     * @param string $type
     * @param bool|null $secure
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getBaseUrl($type = \Magento\Framework\UrlInterface::URL_TYPE_LINK, $secure = null): string
    {
        return $this->baseUrl;
    }

    /**
     * Set base URL
     *
     * @param string $baseUrl
     * @return $this
     */
    public function setBaseUrl(string $baseUrl): self
    {
        $this->baseUrl = $baseUrl;
        return $this;
    }
}
