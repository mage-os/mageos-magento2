<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Helper;

use Magento\Framework\View\Element\Template;

/**
 * Test helper for Template
 */
class TemplateTestHelper extends Template
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
     * setColumn (custom method for testing)
     *
     * @param mixed $value
     * @return $this
     */
    public function setColumn($value)
    {
        $this->data['column'] = $value;
        return $this;
    }

    /**
     * getHtml (custom method for testing)
     *
     * @return mixed
     */
    public function getHtml()
    {
        return $this->data['html'] ?? null;
    }
}
