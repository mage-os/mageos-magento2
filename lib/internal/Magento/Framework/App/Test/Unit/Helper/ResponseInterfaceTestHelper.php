<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\App\Test\Unit\Helper;

use Magento\Framework\App\ResponseInterface;

/**
 * Test helper for ResponseInterface
 */
class ResponseInterfaceTestHelper implements ResponseInterface
{
    /**
     * Set public headers
     *
     * @return $this
     */
    public function setPublicHeaders()
    {
        return $this;
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
     * Set header
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

    /**
     * Clear header
     *
     * @param string $name
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function clearHeader($name)
    {
        return $this;
    }

    /**
     * Clear headers
     *
     * @return $this
     */
    public function clearHeaders()
    {
        return $this;
    }

    /**
     * Set HTTP response code
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
     * Set body
     *
     * @param string $body
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setBody($body)
    {
        return $this;
    }

    /**
     * Append body
     *
     * @param string $body
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function appendBody($body)
    {
        return $this;
    }

    /**
     * Get body
     *
     * @return string|null
     */
    public function getBody()
    {
        return null;
    }

    /**
     * Set redirect
     *
     * @param string $url
     * @param int $code
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setRedirect($url, $code = 302)
    {
        return $this;
    }

    /**
     * Set status header
     *
     * @param int $httpCode
     * @param string|null $version
     * @param string|null $phrase
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setStatusHeader($httpCode, $version = null, $phrase = null)
    {
        return $this;
    }
}
