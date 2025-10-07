<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\App\Test\Unit\Helper;

use Magento\Framework\App\Request\Http;

/**
 * Test helper for Http class
 */
class HttpTestHelper extends Http
{
    /**
     * Skip parent constructor
     */
    public function __construct()
    {
    }

    /**
     * To array
     *
     * @return array
     */
    public function toArray()
    {
        return ['color' => 59, 'size' => 1, 'random_param' => '123'];
    }

    /**
     * Get query
     *
     * @param string|null $name
     * @param mixed $default
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getQuery($name = null, $default = null)
    {
        return $this;
    }
}
