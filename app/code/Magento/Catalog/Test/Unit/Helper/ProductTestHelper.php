<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\Product;

/**
 * Test helper for Magento\Catalog\Model\Product
 */
class ProductTestHelper extends Product
{
    /**
     * @var bool
     */
    private $isObjectNew = false;
    
    /**
     * @var bool
     */
    private $isRecurring = false;
    
    /**
     * @var int
     */
    private $isObjectNewCallCount = 0;
    
    /**
     * @var mixed
     */
    private $price = null;
    
    /**
     * @var mixed
     */
    private $status = null;
    
    /**
     * @var string
     */
    private $typeId = 'simple';
    
    /**
     * @var mixed
     */
    private $downloadableData = null;
    
    /**
     * @var mixed
     */
    private $typeInstance = null;
    
    /**
     * @var mixed
     */
    private $bundleSelectionsData = null;
    
    /**
     * @var int
     */
    private $storeId = 1;
    
    /**
     * @var array
     */
    private $data = [];
    
    /**
     * @var int
     */
    private $priceType = 0;
    
    /**
     * @var mixed
     */
    private $giftcardAmounts = null;
    
    /**
     * @var mixed
     */
    private $msrpDisplayActualPriceType = null;
    
    public function __construct()
    {
        // Skip parent constructor to avoid dependencies
    }
    
    /**
     * Set is object new
     *
     * @param bool $flag
     * @return $this
     */
    public function setIsObjectNew($flag)
    {
        $this->isObjectNew = $flag;
        return $this;
    }
    
    /**
     * Is object new
     *
     * @param bool|null $flag
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function isObjectNew($flag = null)
    {
        $this->isObjectNewCallCount++;
        return $this->isObjectNew;
    }
    
    /**
     * Get is object new call count
     *
     * @return int
     */
    public function getIsObjectNewCallCount()
    {
        return $this->isObjectNewCallCount;
    }
    
    /**
     * Reset is object new call count
     *
     * @return $this
     */
    public function resetIsObjectNewCallCount()
    {
        $this->isObjectNewCallCount = 0;
        return $this;
    }
    
    /**
     * Set is recurring
     *
     * @param bool $value
     * @return $this
     */
    public function setIsRecurring($value)
    {
        $this->isRecurring = $value;
        return $this;
    }
    
    /**
     * Is recurring
     *
     * @return bool
     */
    public function isRecurring()
    {
        return $this->isRecurring;
    }
    
    /**
     * Get is recurring (alias for compatibility)
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsRecurring()
    {
        return $this->isRecurring();
    }
    
    /**
     * Set price
     *
     * @param mixed $price
     * @return $this
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }
    
    /**
     * Get price
     *
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }
    
    /**
     * Set status
     *
     * @param mixed $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }
    
    /**
     * Get status
     *
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }
    
    /**
     * Set type ID
     *
     * @param string $typeId
     * @return $this
     */
    public function setTypeId($typeId)
    {
        $this->typeId = $typeId;
        return $this;
    }
    
    /**
     * Get type ID
     *
     * @return string
     */
    public function getTypeId()
    {
        return $this->typeId;
    }
    
    /**
     * Set downloadable data
     *
     * @param array $data
     * @return $this
     */
    public function setDownloadableData($data)
    {
        $this->downloadableData = $data;
        return $this;
    }
    
    /**
     * Get downloadable data
     *
     * @return array|null
     */
    public function getDownloadableData()
    {
        return $this->downloadableData;
    }
    
    /**
     * Set type instance
     *
     * @param mixed $typeInstance
     * @return $this
     */
    public function setTypeInstance($typeInstance)
    {
        $this->typeInstance = $typeInstance;
        return $this;
    }
    
    /**
     * Get type instance
     *
     * @return mixed
     */
    public function getTypeInstance()
    {
        return $this->typeInstance;
    }
    
    /**
     * Set bundle selections data
     *
     * @param array $data
     * @return $this
     */
    public function setBundleSelectionsData($data)
    {
        $this->bundleSelectionsData = $data;
        return $this;
    }
    
    /**
     * Get bundle selections data
     *
     * @return array|null
     */
    public function getBundleSelectionsData()
    {
        return $this->bundleSelectionsData;
    }
    
    /**
     * Set store ID
     *
     * @param mixed $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;
        return $this;
    }
    
    /**
     * Get store ID
     *
     * @return mixed
     */
    public function getStoreId()
    {
        return $this->storeId;
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
     * Set price type
     *
     * @param mixed $priceType
     * @return $this
     */
    public function setPriceType($priceType)
    {
        $this->priceType = $priceType;
        return $this;
    }
    
    /**
     * Get price type
     *
     * @return mixed
     */
    public function getPriceType()
    {
        return $this->priceType;
    }
    
    /**
     * Set giftcard amounts
     *
     * @param mixed $amounts
     * @return $this
     */
    public function setGiftcardAmounts($amounts)
    {
        $this->giftcardAmounts = $amounts;
        return $this;
    }
    
    /**
     * Get giftcard amounts
     *
     * @return mixed
     */
    public function getGiftcardAmounts()
    {
        return $this->giftcardAmounts;
    }
    
    /**
     * Set MSRP display actual price type
     *
     * @param mixed $type
     * @return $this
     */
    public function setMsrpDisplayActualPriceType($type)
    {
        $this->msrpDisplayActualPriceType = $type;
        return $this;
    }
    
    /**
     * Get MSRP display actual price type
     *
     * @return mixed
     */
    public function getMsrpDisplayActualPriceType()
    {
        return $this->msrpDisplayActualPriceType;
    }
}
