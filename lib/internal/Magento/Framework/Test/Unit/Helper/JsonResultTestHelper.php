<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

/**
 * Test helper for JSON Result
 *
 * This helper implements ResultInterface to provide
 * test-specific functionality for JSON responses.
 */
class JsonResultTestHelper implements ResultInterface
{
    /**
     * @var array
     */
    private $jsonData;

    /**
     * Set JSON data
     *
     * @param array $data
     * @return $this
     */
    public function setJsonData($data)
    {
        $this->jsonData = $data;
        return $this;
    }

    /**
     * Get JSON data
     *
     * @return array
     */
    public function getJsonData()
    {
        return $this->jsonData;
    }

    /**
     * Set HTTP response code
     *
     * @param int $code
     * @return $this
     */
    public function setHttpResponseCode($code)
    {
        return $this;
    }

    /**
     * Set header
     *
     * @param string $name
     * @param string $value
     * @param bool|null $replace
     * @return $this
     */
    public function setHeader($name, $value, $replace = null)
    {
        return $this;
    }

    /**
     * Render result
     *
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function renderResult(ResponseInterface $response)
    {
        return $response;
    }
}

