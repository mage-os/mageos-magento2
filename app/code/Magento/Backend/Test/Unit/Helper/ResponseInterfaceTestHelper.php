<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Helper;

use Magento\Framework\App\ResponseInterface;

/**
 * Test helper for ResponseInterface
 */
class ResponseInterfaceTestHelper extends ResponseInterface
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
     * setRedirect (custom method for testing)
     *
     * @param mixed $value
     * @return $this
     */
    public function setRedirect($value)
    {
        $this->data['redirect'] = $value;
        return $this;
    }
}
