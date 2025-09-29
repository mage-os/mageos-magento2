<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Framework\App\ResponseInterface;

/**
 * Test helper class for ResponseInterface used across Framework and related module tests
 */
class ResponseInterfaceTestHelper implements ResponseInterface
{
    /**
     * @var string
     */
    private string $redirectUrl = '';

    /**
     * @var int
     */
    private int $httpResponseCode = 200;

    /**
     * @var array
     */
    private array $headers = [];

    /**
     * Constructor - skip parent constructor to avoid dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Set redirect URL
     *
     * @param string $url
     * @return $this
     */
    public function setRedirect($url): self
    {
        $this->redirectUrl = $url;
        return $this;
    }

    /**
     * Get redirect URL
     *
     * @return string
     */
    public function getRedirectUrl(): string
    {
        return $this->redirectUrl;
    }

    /**
     * Set HTTP response code
     *
     * @param int $code
     * @return $this
     */
    public function setHttpResponseCode($code): self
    {
        $this->httpResponseCode = $code;
        return $this;
    }

    /**
     * Get HTTP response code
     *
     * @return int
     */
    public function getHttpResponseCode(): int
    {
        return $this->httpResponseCode;
    }

    /**
     * Set header
     *
     * @param string $name
     * @param string $value
     * @param bool $replace
     * @return $this
     */
    public function setHeader($name, $value, $replace = false): self
    {
        if ($replace || !isset($this->headers[$name])) {
            $this->headers[$name] = $value;
        }
        return $this;
    }

    /**
     * Get header
     *
     * @param string $name
     * @return string|null
     */
    public function getHeader($name): ?string
    {
        return $this->headers[$name] ?? null;
    }

    /**
     * Clear headers
     *
     * @return $this
     */
    public function clearHeaders(): self
    {
        $this->headers = [];
        return $this;
    }

    /**
     * Send response
     *
     * @return $this
     */
    public function sendResponse(): self
    {
        // No-op for testing
        return $this;
    }
}
