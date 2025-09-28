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
     * @var bool
     */
    private $gwsRelevant = false;
    
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
        // Skip parent constructor to avoid dependencies
    }
    
    /**
     * Is GWS is all flag
     * 
     * @return bool
     */
    public function isGwsIsAll()
    {
        return $this->gwsIsAll;
    }
    
    /**
     * Get GWS is all flag (alias for compatibility)
     * 
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getGwsIsAll()
    {
        return $this->isGwsIsAll();
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
     * Is GWS relevant flag
     * 
     * @return bool
     */
    public function isGwsRelevant()
    {
        return $this->gwsRelevant;
    }
    
    /**
     * Get GWS relevant flag (alias for compatibility)
     * 
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getGwsRelevant()
    {
        return $this->isGwsRelevant();
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
     * Is GWS data isset flag
     * 
     * @return bool
     */
    public function isGwsDataIsset()
    {
        return $this->gwsDataIsset;
    }
    
    /**
     * Get GWS data isset flag (alias for compatibility)
     * 
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getGwsDataIsset()
    {
        return $this->isGwsDataIsset();
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
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function load($modelId, $field = null)
    {
        return $this;
    }
}
