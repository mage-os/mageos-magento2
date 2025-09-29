<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Reports\Test\Unit\Helper;

use Magento\Reports\Model\Event;

/**
 * Test helper for Reports Event
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class EventTestHelper extends Event
{
    /**
     * @var mixed
     */
    private $response = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
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
