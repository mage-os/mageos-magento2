<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Event\Test\Unit\Helper;

use Magento\Framework\Event;

/**
 * Test helper for Event class
 */
class EventTestHelper extends Event
{
    /**
     * @var array
     */
    private $productIds = [];

    /**
     * @var mixed
     */
    private $response;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get product IDs
     *
     * @return array
     */
    public function getProductIds()
    {
        return $this->productIds;
    }

    /**
     * Set product IDs
     *
     * @param array $ids
     * @return $this
     */
    public function setProductIds($ids)
    {
        $this->productIds = $ids;
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
     * @return void
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }
}
