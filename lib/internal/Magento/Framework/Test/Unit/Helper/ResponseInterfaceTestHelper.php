<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Framework\App\ResponseInterface;

/**
 * Test helper for ResponseInterface
 */
class ResponseInterfaceTestHelper implements ResponseInterface
{
    /**
     * @var string Response body
     */
    private $body = '';

    public function __construct()
    {
    }

    /**
     * Set response body
     *
     * @param string $body
     * @return $this
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Get response body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Send response
     *
     * @return $this
     */
    public function sendResponse()
    {
        return $this;
    }

    /**
     * Set HTTP response code (custom method for testing)
     *
     * @param int $code
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setHttpResponseCode($code)
    {
        return $this;
    }

    /**
     * Clear response body (custom method for testing)
     *
     * @return $this
     */
    public function clearBody()
    {
        return $this;
    }

    /**
     * Send headers (custom method for testing)
     *
     * @return $this
     */
    public function sendHeaders()
    {
        return $this;
    }

    /**
     * Set header (custom method for testing)
     *
     * @param string $name
     * @param string $value
     * @param bool $replace
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setHeader($name, $value, $replace = false)
    {
        return $this;
    }
}
