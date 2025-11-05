<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Helper;

use Magento\User\Model\User;

/**
 * Test helper for User
 */
class UserTestHelper extends User
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
     * getFrontController (custom method for testing)
     *
     * @return mixed
     */
    public function getFrontController()
    {
        return $this->data['frontController'] ?? null;
    }

    /**
     * getTranslator (custom method for testing)
     *
     * @return mixed
     */
    public function getTranslator()
    {
        return $this->data['translator'] ?? null;
    }

    /**
     * getReloadAclFlag (custom method for testing)
     *
     * @return mixed
     */
    public function getReloadAclFlag()
    {
        return $this->data['reloadAclFlag'] ?? null;
    }

    /**
     * setReloadAclFlag (custom method for testing)
     *
     * @param mixed $value
     * @return $this
     */
    public function setReloadAclFlag($value)
    {
        $this->data['reloadAclFlag'] = $value;
        return $this;
    }

    /**
     * getExtra (custom method for testing)
     *
     * @return mixed
     */
    public function getExtra()
    {
        return $this->data['extra'] ?? null;
    }
}
