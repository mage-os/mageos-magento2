<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogRule\Test\Unit\Helper;

use Magento\CatalogRule\Model\Rule;

/**
 * Test helper for Magento\CatalogRule\Model\Rule
 *
 * This helper provides custom logic that cannot be achieved with standard PHPUnit mocks:
 * 1. isObjectNew() with setter capability - allows setting the flag via parameter
 * 2. getId() returns default value of 1 for non-new objects
 *
 * Fixed: Now extends the correct Model class (not ResourceModel).
 */
class RuleTestHelper extends Rule
{
    /**
     * @var bool
     */
    private $isObjectNew = false;

    /**
     * @var int
     */
    private $id = 1;

    public function __construct()
    {
        // Skip parent constructor to avoid dependencies
    }

    /**
     * Is object new with setter capability
     *
     * Custom logic: Allows setting the flag by passing a parameter
     * Parent AbstractModel::isObjectNew() only returns the flag, doesn't allow setting
     *
     * @param bool|null $flag
     * @return bool|$this
     */
    public function isObjectNew($flag = null)
    {
        if ($flag !== null) {
            $this->isObjectNew = $flag;
            return $this;
        }
        return $this->isObjectNew;
    }

    /**
     * Get ID
     *
     * Custom logic: Returns default value of 1 to simulate existing object
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    // Other methods are inherited from parent AbstractModel and DataObject:
    // - getData(), setData() - from DataObject
    // - getOrigData(), setOrigData() - from AbstractModel
}
