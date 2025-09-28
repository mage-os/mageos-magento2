<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Downloadable\Test\Unit\Helper;

use Magento\Downloadable\Model\Product\Type;

/**
 * Test helper for Magento\Downloadable\Model\Product\Type
 */
class TypeInstanceTestHelper extends Type
{
    /**
     * @var array
     */
    private $links = [];
    
    public function __construct()
    {
        // Skip parent constructor to avoid dependencies
    }
    
    /**
     * Set links
     *
     * @param array $links
     * @return $this
     */
    public function setLinks($links)
    {
        $this->links = $links;
        return $this;
    }
    
    /**
     * Get links
     *
     * @param mixed $product
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getLinks($product)
    {
        return $this->links;
    }
}
