<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Framework\Api\ExtensionAttributesInterface;

/**
 * TestHelper for ExtensionAttributesInterface with dynamic methods
 */
class ExtensionAttributesInterfaceTestHelper implements ExtensionAttributesInterface
{
    /** @var array */
    private $excludeWebsiteIds = [];

    public function __construct()
    {
        // Skip constructor
    }

    public function getExcludeWebsiteIds()
    {
        return $this->excludeWebsiteIds;
    }

    public function setExcludeWebsiteIds($value)
    {
        $this->excludeWebsiteIds = $value;
        return $this;
    }
}
