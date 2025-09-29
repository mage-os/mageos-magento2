<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Framework\Event;

/**
 * Test helper class for Event used across Framework and related module tests
 */
class EventTestHelper extends Event
{
    /**
     * @var mixed
     */
    public $cart = null;

    /**
     * @var mixed
     */
    public $info = null;

    /**
     * @var mixed
     */
    public $request = null;

    /**
     * @var mixed
     */
    public $response = null;

    /**
     * Constructor - skip parent constructor to avoid dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get cart
     *
     * @return mixed
     */
    public function getCart()
    {
        return $this->cart;
    }

    /**
     * Set cart
     *
     * @param mixed $cart
     * @return $this
     */
    public function setCart($cart): self
    {
        $this->cart = $cart;
        return $this;
    }

    /**
     * Get info
     *
     * @return mixed
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Set info
     *
     * @param mixed $info
     * @return $this
     */
    public function setInfo($info): self
    {
        $this->info = $info;
        return $this;
    }

    /**
     * Get request
     *
     * @return mixed
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Set request
     *
     * @param mixed $request
     * @return $this
     */
    public function setRequest($request): self
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Get response
     *
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Set response
     *
     * @param mixed $response
     * @return $this
     */
    public function setResponse($response): self
    {
        $this->response = $response;
        return $this;
    }
}
