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

    /**
     * @var mixed
     */
    private $form = null;

    /**
     * @var mixed
     */
    private $product = null;

    /**
     * @var mixed
     */
    private $collection = null;

    /**
     * @var mixed
     */
    private $formData = null;

    /**
     * @var mixed
     */
    private $redirectUrl = null;

    /**
     * Get form
     *
     * @return mixed
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Set form
     *
     * @param mixed $form
     * @return $this
     */
    public function setForm($form): self
    {
        $this->form = $form;
        return $this;
    }

    /**
     * Get product
     *
     * @return mixed
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set product
     *
     * @param mixed $product
     * @return $this
     */
    public function setProduct($product): self
    {
        $this->product = $product;
        return $this;
    }

    /**
     * Get collection
     *
     * @return mixed
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * Set collection
     *
     * @param mixed $collection
     * @return $this
     */
    public function setCollection($collection): self
    {
        $this->collection = $collection;
        return $this;
    }

    /**
     * Get form data
     *
     * @param bool $clear
     * @return mixed
     */
    public function getFormData($clear = false)
    {
        return $this->formData;
    }

    /**
     * Set form data
     *
     * @param mixed $data
     * @return $this
     */
    public function setFormData($data): self
    {
        $this->formData = $data;
        return $this;
    }

    /**
     * Get redirect URL
     *
     * @return mixed
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    /**
     * Set redirect URL
     *
     * @param mixed $url
     * @return $this
     */
    public function setRedirectUrl($url): self
    {
        $this->redirectUrl = $url;
        return $this;
    }
}
