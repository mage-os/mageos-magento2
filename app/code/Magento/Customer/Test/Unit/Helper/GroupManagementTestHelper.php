<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Customer\Model\GroupManagement;

/**
 * Test helper for GroupManagement with custom getId method
 */
class GroupManagementTestHelper extends GroupManagement
{
    /**
     * @var string|int|null
     */
    private $id = null;

    /**
     * Constructor that skips parent dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependencies
    }

    /**
     * Get group ID (custom method for tests)
     *
     * @return string|int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set group ID (custom method for tests)
     *
     * @param string|int $id
     * @return $this
     */
    public function setId($id): self
    {
        $this->id = $id;
        return $this;
    }
}

