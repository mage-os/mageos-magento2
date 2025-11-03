<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\ResourceModel\Product\Link;

class ProductLinkTestHelper extends Link
{
    /**
     * @var mixed
     */
    private $linkedProductId = null;

    /**
     * @var mixed
     */
    private $arrayData = null;

    public function __construct()
    {
        // Empty constructor
    }

    /**
     * @return mixed
     */
    public function getLinkedProductId()
    {
        return $this->linkedProductId;
    }

    /**
     * @param mixed $id
     * @return $this
     */
    public function setLinkedProductId($id)
    {
        $this->linkedProductId = $id;
        return $this;
    }

    /**
     * @param mixed $keys
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function toArray($keys = null)
    {
        return $this->arrayData;
    }

    /**
     * @param mixed $data
     * @return $this
     */
    public function setArrayData($data)
    {
        $this->arrayData = $data;
        return $this;
    }
}

