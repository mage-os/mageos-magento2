<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Model\Test\Unit\Helper;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\AbstractResource;

/**
 * TestHelper for AbstractResource
 * Provides implementation for AbstractResource with additional test methods
 */
class AbstractResourceTestHelper extends AbstractResource
{
    /** @var string|null */
    private $idFieldName = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid complex dependencies
    }

    /**
     * Construct method
     */
    protected function _construct()
    {
        // Required abstract method implementation
    }

    /**
     * Get id field name
     *
     * @return string|null
     */
    public function getIdFieldName()
    {
        return $this->idFieldName;
    }

    /**
     * Set id field name
     *
     * @param string|null $value
     * @return $this
     */
    public function setIdFieldName($value)
    {
        $this->idFieldName = $value;
        return $this;
    }

    /**
     * Save method
     *
     * @param AbstractModel $object
     * @return $this
     */
    public function save(AbstractModel $object)
    {
        return $this;
    }

    /**
     * Delete method
     *
     * @param AbstractModel $object
     * @return $this
     */
    public function delete(AbstractModel $object)
    {
        return $this;
    }

    /**
     * Load method
     *
     * @param AbstractModel $object
     * @param mixed $value
     * @param string|null $field
     * @return $this
     */
    public function load(AbstractModel $object, $value, $field = null)
    {
        return $this;
    }

    /**
     * Get connection
     *
     * @return null
     */
    public function getConnection()
    {
        return null;
    }

    /**
     * Get table
     *
     * @param string $tableName
     * @return string
     */
    public function getTable($tableName)
    {
        return $tableName;
    }

    /**
     * Get main table
     *
     * @return string
     */
    public function getMainTable()
    {
        return 'main_table';
    }

    /**
     * Get table prefix
     *
     * @return string
     */
    public function getTablePrefix()
    {
        return '';
    }
}
