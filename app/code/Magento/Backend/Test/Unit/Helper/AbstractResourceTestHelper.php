<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Helper;

use Magento\Framework\Model\ResourceModel\AbstractResource;

/**
 * Test helper for AbstractResource
 */
class AbstractResourceTestHelper extends AbstractResource
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Skip parent constructor
     */
    public function __construct()
    {
        // Skip parent constructor
    }

    /**
     * getIdFieldName (custom method for testing)
     *
     * @return mixed
     */
    public function getIdFieldName()
    {
        return $this->data['idFieldName'] ?? null;
    }

    /**
     * save (custom method for testing)
     *
     * @return mixed
     */
    public function save()
    {
        return $this->data['save'] ?? null;
    }
}
