<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\Session;

class SessionTestHelper extends Session
{
    /**
     * @var string|null
     */
    private $beforeCompareUrl = null;

    public function __construct()
    {
        // Empty constructor
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setBeforeCompareUrl($url)
    {
        $this->beforeCompareUrl = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getBeforeCompareUrl()
    {
        return $this->beforeCompareUrl ?: 'http://magento.com/compare/before';
    }
}

