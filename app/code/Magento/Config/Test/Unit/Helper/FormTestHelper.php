<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Config\Test\Unit\Helper;

use Magento\Framework\Data\Form;

/**
 * Test helper for Form
 */
class FormTestHelper extends Form
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
     * getHtmlIdPrefix (custom method for testing)
     *
     * @return mixed
     */
    public function getHtmlIdPrefix()
    {
        return $this->data['htmlIdPrefix'] ?? null;
    }

    /**
     * getHtmlIdSuffix (custom method for testing)
     *
     * @return mixed
     */
    public function getHtmlIdSuffix()
    {
        return $this->data['htmlIdSuffix'] ?? null;
    }
}
