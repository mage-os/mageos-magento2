<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Helper;

use Magento\Backend\Model\Auth\Credential\StorageInterface as CredentialStorage;

class AuthStorageTestHelper implements \Magento\Backend\Model\Auth\StorageInterface
{
    /**
     * @return $this
     */
    public function processLogin()
    {
        return $this;
    }

    /**
     * @return $this
     */
    public function processLogout()
    {
        return $this;
    }

    /**
     * @return bool
     */
    public function isLoggedIn()
    {
        return true;
    }

    /**
     * @return void
     */
    public function prolong()
    {
    }

    /**
     * @param string $path
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setDeletedPath($path)
    {
        return $this;
    }
}

