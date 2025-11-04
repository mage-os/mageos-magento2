<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Filter\Test\Unit\Helper;

use Magento\Framework\Filter\FilterManager;

/**
 * Test helper class for FilterManager with custom methods
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class FilterManagerTestHelper extends FilterManager
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Skip parent constructor to avoid dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Custom translitUrl method for testing
     *
     * @param string $string
     * @return mixed
     */
    public function translitUrl($string)
    {
        return $this->data['translitUrl'] ?? null;
    }

    /**
     * Set return value for translitUrl
     *
     * @param mixed $result
     * @return $this
     */
    public function setTranslitUrlResult($result)
    {
        $this->data['translitUrl'] = $result;
        return $this;
    }

    /**
     * Custom stripTags method for testing
     *
     * @param string $value
     * @return string
     */
    public function stripTags($value = '')
    {
        return $this->data['stripTags'] ?? $value;
    }

    /**
     * Set return value for stripTags
     *
     * @param string $value
     * @return self
     */
    public function setStripTagsReturn(string $value): self
    {
        $this->data['stripTags'] = $value;
        return $this;
    }

    /**
     * Custom sprintf method for testing
     *
     * @param string $format
     * @param mixed ...$args
     * @return string
     */
    public function sprintf($format, ...$args)
    {
        return $this->data['sprintf'] ?? $format;
    }

    /**
     * Set return value for sprintf
     *
     * @param string $value
     * @return self
     */
    public function setSprintfReturn(string $value): self
    {
        $this->data['sprintf'] = $value;
        return $this;
    }
}
