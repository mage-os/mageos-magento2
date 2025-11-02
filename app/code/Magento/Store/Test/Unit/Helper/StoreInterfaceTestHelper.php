<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Store\Test\Unit\Helper;

use Magento\Store\Api\Data\StoreInterface;

/**
 * Test helper for StoreInterface with custom methods
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class StoreInterfaceTestHelper implements StoreInterface
{
    /**
     * @var string
     */
    private $baseUrl = '';

    /**
     * Get base URL (custom method for tests)
     *
     * @param string|null $type
     * @param bool|null $secure
     * @return string
     */
    public function getBaseUrl($type = null, $secure = null): string
    {
        return $this->baseUrl;
    }

    /**
     * Set base URL
     *
     * @param string $baseUrl
     * @return $this
     */
    public function setBaseUrl(string $baseUrl): self
    {
        $this->baseUrl = $baseUrl;
        return $this;
    }

    // StoreInterface implementation (stubs)
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
        return '';
    }

    public function setCode($code)
    {
        return $this;
    }

    public function getName()
    {
        return '';
    }

    public function setName($name)
    {
        return $this;
    }

    public function getWebsiteId()
    {
        return null;
    }

    public function setWebsiteId($websiteId)
    {
        return $this;
    }

    public function getStoreGroupId()
    {
        return null;
    }

    public function setStoreGroupId($storeGroupId)
    {
        return $this;
    }

    public function getExtensionAttributes()
    {
        return null;
    }

    public function setExtensionAttributes(\Magento\Store\Api\Data\StoreExtensionInterface $extensionAttributes)
    {
        return $this;
    }

    public function getIsActive()
    {
        return true;
    }

    public function setIsActive($isActive)
    {
        return $this;
    }
}

