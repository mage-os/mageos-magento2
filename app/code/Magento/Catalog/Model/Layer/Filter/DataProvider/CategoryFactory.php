<?php
/**
 * Copyright 2013 Adobe
 * All Rights Reserved.
 */

namespace Magento\Catalog\Model\Layer\Filter\DataProvider;

use Magento\Framework\ObjectManagerInterface;

class CategoryFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create category data provider
     *
     * @param array $data
     * @return Category
     */
    public function create(array $data = [])
    {
        return $this->objectManager->create(Category::class, $data);
    }
}
