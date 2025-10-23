<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\App\Test\Unit\Helper;

use Magento\Framework\App\ResponseInterface;

/**
 * Test helper for ResponseInterface
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class ResponseInterfaceTestHelper implements ResponseInterface
{
    public function __construct()
    {
    }

    public function setRedirect($url, $code = 302)
    {
        return $this;
    }

    public function sendResponse()
    {
        return $this;
    }
}
