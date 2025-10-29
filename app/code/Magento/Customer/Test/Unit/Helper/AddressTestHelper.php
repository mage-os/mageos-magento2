<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Customer\Model\Address;

class AddressTestHelper extends Address
{
    /**
     * @var array<string, mixed>
     */
    private $data = [];

    /**
     * Constructor - skip parent constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->data['id'] ?? null;
    }

    /**
     * Set ID
     *
     * @param int|null $id
     * @return $this
     */
    public function setId($id)
    {
        $this->data['id'] = $id;
        return $this;
    }

    /**
     * Get is default billing
     *
     * @return bool|null
     */
    public function getIsDefaultBilling()
    {
        return $this->data['is_default_billing'] ?? null;
    }

    /**
     * Set is default billing
     *
     * @param bool $value
     * @return $this
     */
    public function setIsDefaultBilling($value)
    {
        $this->data['is_default_billing'] = $value;
        return $this;
    }

    /**
     * Get is default shipping
     *
     * @return bool|null
     */
    public function getIsDefaultShipping()
    {
        return $this->data['is_default_shipping'] ?? null;
    }

    /**
     * Set is default shipping
     *
     * @param bool $value
     * @return $this
     */
    public function setIsDefaultShipping($value)
    {
        $this->data['is_default_shipping'] = $value;
        return $this;
    }

    /**
     * Set force process
     *
     * @param bool $value
     * @return $this
     */
    public function setForceProcess($value)
    {
        $this->data['force_process'] = $value;
        return $this;
    }
}
