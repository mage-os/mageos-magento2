<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Filter\Test\Unit\Helper;

use Magento\Framework\Filter\FilterManager;

/**
 * Test helper for FilterManager
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class FilterManagerTestHelper extends FilterManager
{
    /**
     * @var mixed
     */
    private $translitUrlResult = null;

    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    public function translitUrl($string)
    {
        return $this->translitUrlResult;
    }

    public function setTranslitUrlResult($result)
    {
        $this->translitUrlResult = $result;
        return $this;
    }

    public function stripTags($string)
    {
        return $string;
    }
}

