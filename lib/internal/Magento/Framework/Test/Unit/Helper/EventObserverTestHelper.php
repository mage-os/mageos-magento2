<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Framework\Event\Observer;

/**
 * Test helper for Event Observer
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class EventObserverTestHelper extends Observer
{
    /**
     * @var mixed
     */
    private $responseObject = null;

    /**
     * @var mixed
     */
    private $data = null;

    /**
     * @var mixed
     */
    private $customerAddress = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get response object
     *
     * @return mixed
     */
    public function getResponseObject()
    {
        return $this->responseObject;
    }

    /**
     * Set response object
     *
     * @param mixed $object
     * @return $this
     */
    public function setResponseObject($object): self
    {
        $this->responseObject = $object;
        return $this;
    }

    /**
     * Get data
     *
     * @param mixed $key
     * @param mixed $index
     * @return mixed
     */
    public function getData($key = '', $index = null)
    {
        return $this->data;
    }

    /**
     * Set data
     *
     * @param mixed $key
     * @param mixed $value
     * @return $this
     */
    public function setData($key, $value = null): self
    {
        $this->data = $value;
        return $this;
    }

    /**
     * Get customer address
     *
     * @return mixed
     */
    public function getCustomerAddress()
    {
        return $this->customerAddress;
    }

    /**
     * Set customer address
     *
     * @param mixed $address
     * @return $this
     */
    public function setCustomerAddress($address): self
    {
        $this->customerAddress = $address;
        return $this;
    }
}
