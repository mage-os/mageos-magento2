<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Checkout\Test\Unit\Helper;

use Magento\Framework\App\ResponseInterface;

/**
 * Simple Response helper exposing representJson for controller tests.
 */
class ResponseRepresentJsonTestHelper implements ResponseInterface
{
    /**
     * @inheritDoc
     */
    public function sendResponse()
    {
        return 0;
    }

    /**
     * Return provided JSON payload for assertions in tests.
     *
     * @param string $json
     * @return string
     */
    public function representJson(string $json)
    {
        return $json;
    }
}
