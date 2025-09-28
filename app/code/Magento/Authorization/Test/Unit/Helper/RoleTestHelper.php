<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Authorization\Test\Unit\Helper;

use Magento\Authorization\Model\Role;

/**
 * Test helper for Magento\Authorization\Model\Role
 */
class RoleTestHelper extends Role
{
    private $gwsIsAll = false;
    private $gwsWebsites = [];
    private $gwsStoreGroups = [];
    private $gwsStores = [];
    private $gwsRelevant = false;
    private $gwsDataIsset = false;
    private $data = [];
    
    public function __construct()
    {
        // Skip parent constructor to avoid dependencies
    }
    
    /**
     * Get GWS is all flag
     * 
     * @return bool
     */
    public function getGwsIsAll()
    {
        return $this->gwsIsAll;
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
     * Get GWS stores
     * 
     * @return array
     */
    public function getGwsStores()
    {
        return $this->gwsStores;
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
     * Get GWS relevant flag
     * 
     * @return bool
     */
    public function getGwsRelevant()
    {
        return $this->gwsRelevant;
    }
    
    /**
     * Set GWS relevant flag
     * 
     * @param bool $value
     * @return $this
     */
    public function setGwsRelevant($value)
    {
        $this->gwsRelevant = $value;
        return $this;
    }
    
    /**
     * Get GWS data isset flag
     * 
     * @return bool
     */
    public function getGwsDataIsset()
    {
        return $this->gwsDataIsset;
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
    
    /**
     * Get data
     * 
     * @param string $key
     * @param mixed $index
     * @return mixed
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
            $this->data = $key;
        } else {
            $this->data[$key] = $value;
        }
        return $this;
    }
    
    /**
     * Load role
     * 
     * @param mixed $modelId
     * @param string $field
     * @return $this
     */
    public function load($modelId, $field = null)
    {
        return $this;
    }
}
