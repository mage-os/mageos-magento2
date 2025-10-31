<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Store\Test\Unit\Helper;

use Magento\Store\Api\Data\WebsiteInterface;

/**
 * Test helper for WebsiteInterface with custom methods
 * 
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class WebsiteInterfaceTestHelper implements WebsiteInterface
{
    /**
     * @var array
     */
    private $storeIds = [];

    /**
     * Get store IDs (custom method for tests)
     *
     * @return array
     */
    public function getStoreIds(): array
    {
        return $this->storeIds;
    }

    /**
     * Set store IDs (custom method for tests)
     *
     * @param array $storeIds
     * @return $this
     */
    public function setStoreIds(array $storeIds): self
    {
        $this->storeIds = $storeIds;
        return $this;
    }

    // Implement WebsiteInterface methods as stubs
    public function getId()
    {
        return null;
    }

    public function setId($id)
    {
        return $this;
    }

    public function getCode()
    {
        return null;
    }

    public function setCode($code)
    {
        return $this;
    }

    public function getName()
    {
        return null;
    }

    public function setName($name)
    {
        return $this;
    }

    public function getDefaultGroupId()
    {
        return null;
    }

    public function setDefaultGroupId($defaultGroupId)
    {
        return $this;
    }

    public function getExtensionAttributes()
    {
        return null;
    }

    public function setExtensionAttributes(\Magento\Store\Api\Data\WebsiteExtensionInterface $extensionAttributes)
    {
        return $this;
    }
}
