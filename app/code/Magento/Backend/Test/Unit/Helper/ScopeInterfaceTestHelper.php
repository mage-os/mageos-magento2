<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Test helper for ScopeInterface
 */
class ScopeInterfaceTestHelper extends ScopeInterface
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Skip parent constructor
     */
    public function __construct()
    {
        // Skip parent constructor
    }

    /**
     * setPath (custom method for testing)
     *
     * @param mixed $value
     * @return $this
     */
    public function setPath($value)
    {
        $this->data['path'] = $value;
        return $this;
    }

    /**
     * setScope (custom method for testing)
     *
     * @param mixed $value
     * @return $this
     */
    public function setScope($value)
    {
        $this->data['scope'] = $value;
        return $this;
    }

    /**
     * setScopeId (custom method for testing)
     *
     * @param mixed $value
     * @return $this
     */
    public function setScopeId($value)
    {
        $this->data['scopeId'] = $value;
        return $this;
    }

    /**
     * setValue (custom method for testing)
     *
     * @param mixed $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->data['value'] = $value;
        return $this;
    }

    /**
     * getValue (custom method for testing)
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->data['value'] ?? null;
    }
}
