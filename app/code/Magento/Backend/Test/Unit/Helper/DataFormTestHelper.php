<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Helper;

use Magento\Framework\Data\Form;

/**
 * Test helper for Magento\Framework\Data\Form
 * Provides custom methods needed for testing
 */
class DataFormTestHelper extends Form
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Skip parent constructor to avoid dependency injection issues
     */
    public function __construct()
    {
        // Skip parent constructor
    }

    /**
     * Set parent (custom method for testing)
     *
     * @param mixed $parent
     * @return $this
     */
    public function setParent($parent)
    {
        $this->data['parent'] = $parent;
        return $this;
    }

    /**
     * Set base URL (custom method for testing)
     *
     * @param string $baseUrl
     * @return $this
     */
    public function setBaseUrl($baseUrl)
    {
        $this->data['base_url'] = $baseUrl;
        return $this;
    }
}


