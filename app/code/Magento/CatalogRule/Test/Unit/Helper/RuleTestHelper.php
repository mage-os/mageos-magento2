<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogRule\Test\Unit\Helper;

use Magento\CatalogRule\Model\ResourceModel\Rule;

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
    
    /**
     * @var array
     */
    private $data = [];
    
    /**
     * @var array
     */
    private $origData = [];
    
    public function __construct()
    {
        // Skip parent constructor for testing
    }
    
    public function isObjectNew($flag = null)
    {
        if ($flag !== null) {
            $this->isObjectNew = $flag;
            return $this;
        }
        return $this->isObjectNew;
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
    
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getData($key = '', $index = null)
    {
        if ($key === 'website_ids') {
            return $this->data['website_ids'] ?? [];
        }
        return $this->data[$key] ?? null;
    }
    
    public function getOrigData($key = null)
    {
        if ($key === 'website_ids') {
            return $this->origData['website_ids'] ?? [];
        }
        return $this->origData[$key] ?? null;
    }
    
    public function setData($key, $value = null)
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }
        return $this;
    }
    
    public function setOrigData($key, $value = null)
    {
        if (is_array($key)) {
            $this->origData = array_merge($this->origData, $key);
        } else {
            $this->origData[$key] = $value;
        }
        return $this;
    }
}
