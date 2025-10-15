<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject;

/**
 * Test helper Product providing minimal URL data accessors for tests.
 */
class ProductTestHelper extends Product
{
    /** @var int */
    private $id;

    /** @var DataObject|null */
    private $urlDataObject;

    /**
     * @param int $id
     * @param DataObject|null $urlDataObject
     */
    public function __construct($id = 0, ?DataObject $urlDataObject = null)
    {
        // Intentionally do not call parent constructor; only minimal behavior is required for tests
        $this->id = (int)$id;
        $this->urlDataObject = $urlDataObject;
    }

    /**
     * Get entity id.
     *
     * @return int
     */
    public function getEntityId()
    {
        return $this->id;
    }

    /**
     * Check if URL data object exists.
     *
     * @return bool
     */
    public function hasUrlDataObject()
    {
        return (bool)$this->urlDataObject;
    }

    /**
     * Get URL data object.
     *
     * @return DataObject|null
     */
    public function getUrlDataObject()
    {
        return $this->urlDataObject;
    }

    /**
     * @return bool
     */
    public function isVisibleInSiteVisibility()
    {
        return false;
    }

    /**
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param DataObject $data
     * @return $this
     */
    public function setUrlDataObject($data)
    {
        $this->urlDataObject = $data;
        return $this;
    }

    /**
     * Return stick within parent flag from internal data.
     *
     * @return mixed
     */
    public function getStickWithinParent()
    {
        return $this->getData('stick_within_parent');
    }

    /**
     * Set stick within parent flag for tests.
     *
     * @param mixed $flag
     * @return $this
     */
    public function setStickWithinParent($flag)
    {
        $this->setData('stick_within_parent', $flag);
        return $this;
    }

    /**
     * Set customer group id in internal data for tests.
     *
     * @param int|string $customerGroupId
     * @return $this
     */
    public function setCustomerGroupId($customerGroupId)
    {
        $this->setData('customer_group_id', $customerGroupId);
        return $this;
    }

    /**
     * Get parent product id for tests.
     *
     * @return mixed
     */
    public function getParentProductId()
    {
        return $this->getData('parent_product_id');
    }

    /**
     * Set parent product id for tests.
     *
     * @param mixed $id
     * @return $this
     */
    public function setParentProductId($id)
    {
        $this->setData('parent_product_id', $id);
        return $this;
    }

    /**
     * Get cart qty for tests.
     *
     * @return mixed
     */
    public function getCartQty()
    {
        return $this->getData('cart_qty');
    }

    /**
     * Set cart qty for tests.
     *
     * @param mixed $qty
     * @return $this
     */
    public function setCartQty($qty)
    {
        $this->setData('cart_qty', $qty);
        return $this;
    }

    /**
     * Get stock item for tests.
     *
     * @return mixed
     */
    public function getStockItem()
    {
        return $this->getData('stock_item');
    }

    /**
     * Enable/disable super mode flag for tests.
     *
     * @param bool $flag
     * @return $this
     */
    public function setIsSuperMode($flag)
    {
        $this->setData('is_super_mode', (bool)$flag);
        return $this;
    }

    /**
     * Unset skip check required option flag for tests (no-op).
     *
     * @return $this
     */
    public function unsSkipCheckRequiredOption()
    {
        // No-op for unit tests, callable method required for mocks
        return $this;
    }
}
