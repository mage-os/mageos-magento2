<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Helper;

use Magento\Framework\Message\ManagerInterface;

/**
 * Test helper for ManagerInterface
 */
class ManagerInterfaceTestHelper extends ManagerInterface
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
     * setPostData (custom method for testing)
     *
     * @param mixed $value
     * @return $this
     */
    public function setPostData($value)
    {
        $this->data['postData'] = $value;
        return $this;
    }
}
