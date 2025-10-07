<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Helper;

use Magento\Backend\Model\Session;

/**
 * Test helper for Session class
 */
class SessionTestHelper extends Session
{
    /**
     * @var array
     */
    private $pageData;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Set page data
     *
     * @param array $data
     * @return $this
     */
    public function setPageData($data)
    {
        $this->pageData = $data;
        return $this;
    }

    /**
     * Get page data
     *
     * @return array|null
     */
    public function getPageData()
    {
        return $this->pageData;
    }
}
