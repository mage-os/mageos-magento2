<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Pricing\Test\Unit\Helper;

use Magento\Framework\Pricing\SaleableInterface;

/**
 * Test helper for SaleableInterface
 */
class SaleableInterfaceTestHelper implements SaleableInterface
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        // No parent constructor to avoid dependencies
    }

    /**
     * @inheritdoc
     */
    public function getPriceInfo()
    {
        return $this->data['price_info'] ?? null;
    }

    /**
     * Set price info
     *
     * @param mixed $priceInfo
     * @return $this
     */
    public function setPriceInfo($priceInfo): self
    {
        $this->data['price_info'] = $priceInfo;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTypeId()
    {
        return $this->data['type_id'] ?? 'grouped';
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->data['id'] ?? 1;
    }

    /**
     * @inheritdoc
     */
    public function getQty()
    {
        return $this->data['qty'] ?? 1;
    }

    /**
     * Get type instance (custom method for testing)
     *
     * @return mixed
     */
    public function getTypeInstance()
    {
        return $this->data['type_instance'] ?? null;
    }

    /**
     * Set type instance
     *
     * @param mixed $typeInstance
     * @return $this
     */
    public function setTypeInstance($typeInstance): self
    {
        $this->data['type_instance'] = $typeInstance;
        return $this;
    }

    /**
     * Get store (custom method for testing)
     *
     * @return mixed
     */
    public function getStore()
    {
        return $this->data['store'] ?? null;
    }

    /**
     * Set store
     *
     * @param mixed $store
     * @return $this
     */
    public function setStore($store): self
    {
        $this->data['store'] = $store;
        return $this;
    }

    /**
     * Get custom option (custom method for testing)
     *
     * @param string $code
     * @return mixed
     */
    public function getCustomOption($code)
    {
        return $this->data['custom_options'][$code] ?? null;
    }

    /**
     * Set custom option
     *
     * @param string $code
     * @param mixed $option
     * @return $this
     */
    public function setCustomOption(string $code, $option): self
    {
        $this->data['custom_options'][$code] = $option;
        return $this;
    }
}
