<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;

class CategoryCollectionFactoryTestHelper extends CollectionFactory
{
    /**
     * @var mixed
     */
    private $createResult;

    public function __construct()
    {
    }

    /**
     * @param mixed $result
     * @return $this
     */
    public function setCreate($result)
    {
        $this->createResult = $result;
        return $this;
    }

    /**
     * @param array $data
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function create(array $data = [])
    {
        return $this->createResult;
    }
}

