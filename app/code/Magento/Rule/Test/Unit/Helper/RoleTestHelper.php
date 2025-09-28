<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Authorization\Test\Unit\Helper;

use Magento\Authorization\Model\Role;

/**
 * Test helper for creating Role mocks with GWS (Global Website Store) methods
 *
 * This helper extends the concrete Role class directly, providing a clean
 * way to add test-specific GWS methods without using anonymous classes.
 */
class RoleTestHelper extends Role
{
    /**
     * @var bool
     */
    private $gwsIsAll = false;
    
    /**
     * @var array
     */
    private $gwsWebsites = [];
    
    /**
     * @var array
     */
    private $gwsStoreGroups = [];
    
    /**
     * @var array
     */
    private $gwsStores = [];
    
    /**
     * @var array
     */
    private $gwsRelevantWebsites = [];
    
    /**
     * @var bool
     */
    private $gwsDataIsset = false;
    
    /**
     * @var array
     */
    private $data = [];
    
    public function __construct()
    {
        // Skip parent constructor for testing
    }
    
    /**
     * Get data by key
     *
     * @param string $key
     * @param mixed $index
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getData($key = '', $index = null)
    {
        if ($key === '') {
            return $this->data;
        }
        return $this->data[$key] ?? null;
    }
    
    /**
     * Set data
     *
     * @param string|array $key
     * @param mixed $value
     * @return $this
     */
    public function setData($key, $value = null)
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }
        return $this;
    }
    
    /**
     * Set GWS is all flag
     *
     * @param bool $value
     * @return $this
     */
    public function setGwsIsAll($value)
    {
        $this->gwsIsAll = $value;
        return $this;
    }
    
    /**
     * Get GWS websites
     *
     * @return array
     */
    public function getGwsWebsites()
    {
        return $this->gwsWebsites;
    }
    
    /**
     * Set GWS websites
     *
     * @param array $websites
     * @return $this
     */
    public function setGwsWebsites($websites)
    {
        $this->gwsWebsites = $websites;
        return $this;
    }
    
    /**
     * Get GWS store groups
     *
     * @return array
     */
    public function getGwsStoreGroups()
    {
        return $this->gwsStoreGroups;
    }
    
    /**
     * Set GWS store groups
     *
     * @param array $storeGroups
     * @return $this
     */
    public function setGwsStoreGroups($storeGroups)
    {
        $this->gwsStoreGroups = $storeGroups;
        return $this;
    }
    
    /**
     * Set GWS stores
     *
     * @param array $stores
     * @return $this
     */
    public function setGwsStores($stores)
    {
        $this->gwsStores = $stores;
        return $this;
    }
    
    /**
     * Set GWS relevant websites
     *
     * @param array $websites
     * @return $this
     */
    public function setGwsRelevantWebsites($websites)
    {
        $this->gwsRelevantWebsites = $websites;
        return $this;
    }
    
    /**
     * Set GWS data isset flag
     *
     * @param bool $value
     * @return $this
     */
    public function setGwsDataIsset($value)
    {
        $this->gwsDataIsset = $value;
        return $this;
    }
}
