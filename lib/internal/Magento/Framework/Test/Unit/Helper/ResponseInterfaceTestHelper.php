<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Framework\App\Response\Http;

/**
 * Test helper for ResponseInterface with custom methods
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class ResponseInterfaceTestHelper extends Http
{
    /**
     * Constructor that skips parent dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Set redirect
     *
     * @param string $url
     * @param int $code
     * @return $this
     */
    public function setRedirect($url, $code = 302): self
    {
        return $this;
    }

    /**
     * Represent JSON
     *
     * @param string|array $valueToEncode
     * @return string
     */
    public function representJson($valueToEncode): string
    {
        return json_encode($valueToEncode);
    }

    /**
     * Set HTTP response code
     *
     * @param mixed $code
     * @return $this
     */
    public function setHttpResponseCode($code): self
    {
        return $this;
    }
}
