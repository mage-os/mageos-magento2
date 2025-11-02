<?php
/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Api\Data;

use Magento\Framework\ObjectManagerInterface;

class SpecialPriceInterfaceFactory
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return SpecialPriceInterface
     */
    public function create(array $data = [])
    {
        return $this->objectManager->create(SpecialPriceInterface::class, $data);
    }
}
