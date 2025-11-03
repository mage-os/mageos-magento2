<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\App\Test\Unit\Helper;

use Magento\Framework\App\Response\Http;

class ResponseTestHelper extends Http
{
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
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
     * @return bool
     */
    public function isRedirect()
    {
        return false;
    }
}

