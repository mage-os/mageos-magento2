<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Downloadable\Test\Unit\Helper;

use Magento\Catalog\Api\Data\ProductOptionInterface;

/**
 * Test helper class for ProductOptionInterface with custom methods
 */
class ProductOptionInterfaceTestHelper implements ProductOptionInterface
{
    /**
     * @inheritdoc
     */
    public function getExtensionAttributes()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes($extensionAttributes)
    {
        return $this;
    }

    /**
     * Custom getDownloadableOption method for testing
     *
     * @return mixed
     */
    public function getDownloadableOption()
    {
        return null;
    }
}
