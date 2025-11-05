<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Helper;

use Magento\Backend\Model\Auth\Session;

/**
 * Test helper for Session
 */
class SessionTestHelper extends Session
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
     * setIsUrlNotice (custom method for testing)
     *
     * @param mixed $value
     * @return $this
     */
    public function setIsUrlNotice($value)
    {
        $this->data['isUrlNotice'] = $value;
        return $this;
    }

    /**
     * getUser (custom method for testing)
     *
     * @return mixed
     */
    public function getUser()
    {
        return $this->data['user'] ?? null;
    }

    /**
     * getAclRole (custom method for testing)
     *
     * @return mixed
     */
    public function getAclRole()
    {
        return $this->data['aclRole'] ?? null;
    }

    /**
     * hasUser (custom method for testing)
     *
     * @return bool
     */
    public function hasUser()
    {
        return $this->data['user'] ?? false;
    }

    /**
     * setPostData (custom method for testing)
     *
     * @param mixed $data
     * @return $this
     */
    public function setPostData($data)
    {
        $this->data['post_data'] = $data;
        return $this;
    }
}
