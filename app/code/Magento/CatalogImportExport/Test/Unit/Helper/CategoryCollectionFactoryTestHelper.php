<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogImportExport\Test\Unit\Helper;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;

class CategoryCollectionFactoryTestHelper extends CategoryCollectionFactory
{
    /**
     * @var mixed
     */
    private $createReturn = null;

    public function __construct()
    {
        // Empty constructor to avoid complex dependencies
    }

    /**
     * @return $this
     */
    public function addNameToResult()
    {
        return $this;
    }

    /**
     * @param mixed $createReturn
     * @return $this
     */
    public function setCreateReturn($createReturn)
    {
        $this->createReturn = $createReturn;
        return $this;
    }

    /**
     * @param array $data
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function create(array $data = [])
    {
        return $this->createReturn;
    }
}

