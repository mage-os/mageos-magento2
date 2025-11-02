<?php
/**
 * Copyright 2013 Adobe
 * All Rights Reserved.
 */

namespace Magento\Catalog\Model\Layer\Filter;

use Magento\Framework\ObjectManagerInterface;

class ItemFactory
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
     * Create filter item
     *
     * @param array $data
     * @return Item
     */
    public function create(array $data = [])
    {
        return $this->objectManager->create(Item::class, $data);
    }
}
