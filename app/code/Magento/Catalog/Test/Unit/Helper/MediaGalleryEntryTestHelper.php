<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\Product\Gallery\Entry;

class MediaGalleryEntryTestHelper extends Entry
{
    /**
     * @var mixed
     */
    private $testExtensionAttributes = null;

    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Override to accept any value without type hint
     * @param mixed $extensionAttributes
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setExtensionAttributes($extensionAttributes = null)
    {
        $this->testExtensionAttributes = $extensionAttributes;
        return $this;
    }

    /**
     * Override to return test value without parent's factory
     * @return mixed
     */
    public function getExtensionAttributes()
    {
        return $this->testExtensionAttributes;
    }
}

