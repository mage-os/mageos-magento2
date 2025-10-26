<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Customer\Api\Data\CustomerExtensionInterface;

/**
 * Test helper that implements CustomerExtensionInterface
 *
 * Provides stub implementation for customer extension attributes.
 * Only implements companyAttributes which is actively used by tests (106 occurrences).
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class CustomerExtensionTestHelper implements CustomerExtensionInterface
{
    /**
     * @var \Magento\Company\Api\Data\CompanyCustomerInterface|null
     */
    private $companyAttributes;

    /**
     * Constructor
     *
     * @param mixed $companyAttributes
     */
    public function __construct($companyAttributes = null)
    {
        $this->companyAttributes = $companyAttributes;
    }

    /**
     * Get company attributes
     *
     * @return \Magento\Company\Api\Data\CompanyCustomerInterface|null
     */
    public function getCompanyAttributes()
    {
        return $this->companyAttributes;
    }

    /**
     * Set company attributes
     *
     * @param \Magento\Company\Api\Data\CompanyCustomerInterface|null $companyAttributes
     * @return $this
     */
    public function setCompanyAttributes($companyAttributes)
    {
        $this->companyAttributes = $companyAttributes;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsSubscribed()
    {
        return null;
    }

    /**
     * @param mixed $isSubscribed
     * @return $this
     */
    public function setIsSubscribed($isSubscribed)
    {
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAssistanceAllowed()
    {
        return null;
    }

    /**
     * @param mixed $assistanceAllowed
     * @return $this
     */
    public function setAssistanceAllowed($assistanceAllowed)
    {
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAmazonId()
    {
        return null;
    }

    /**
     * @param mixed $amazonId
     * @return $this
     */
    public function setAmazonId($amazonId)
    {
        return $this;
    }

    /**
     * @return mixed
     */
    public function getVertexCustomerCode()
    {
        return null;
    }

    /**
     * @param mixed $vertexCustomerCode
     * @return $this
     */
    public function setVertexCustomerCode($vertexCustomerCode)
    {
        return $this;
    }
}
