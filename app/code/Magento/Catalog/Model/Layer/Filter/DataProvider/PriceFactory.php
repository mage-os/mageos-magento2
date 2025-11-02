<?php
/**
 * Copyright 2013 Adobe
 * All Rights Reserved.
 */

namespace Magento\Catalog\Model\Layer\Filter\DataProvider;

use Magento\Framework\ObjectManagerInterface;

class PriceFactory
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
     * Create price data provider
     *
     * @param array $data
     * @return Price
     */
    public function create(array $data = [])
    {
        return $this->objectManager->create(Price::class, $data);
    }
}
