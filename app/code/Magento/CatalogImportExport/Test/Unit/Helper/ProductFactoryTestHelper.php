<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogImportExport\Test\Unit\Helper;

use Magento\Catalog\Model\ResourceModel\ProductFactory;

class ProductFactoryTestHelper extends ProductFactory
{
    /**
     * @var mixed
     */
    private $typeId = null;

    /**
     * @var mixed
     */
    private $createReturn = null;

    public function __construct()
    {
        // Empty constructor to avoid complex dependencies
    }

    /**
     * @return mixed
     */
    public function getTypeId()
    {
        return $this->typeId;
    }

    /**
     * @param mixed $typeId
     * @return $this
     */
    public function setTypeId($typeId)
    {
        $this->typeId = $typeId;
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

