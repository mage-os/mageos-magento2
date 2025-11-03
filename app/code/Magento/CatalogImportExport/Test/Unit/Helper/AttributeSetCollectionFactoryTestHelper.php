<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogImportExport\Test\Unit\Helper;

use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory as AttributeSetCollectionFactory;

class AttributeSetCollectionFactoryTestHelper extends AttributeSetCollectionFactory
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
     * @param mixed $entityType
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setEntityTypeFilter($entityType)
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

