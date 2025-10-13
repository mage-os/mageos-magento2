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
 * Provides stub implementations for all extension attribute methods
 */
class CustomerExtensionTestHelper implements CustomerExtensionInterface
{
    /**
     * @var mixed
     */
    private $companyAttributes;

    /**
     * @var mixed
     */
    private $assistanceAllowed;

    /**
     * @var mixed
     */
    private $isSubscribed;

    /**
     * @var mixed
     */
    private $allCompanyAttributes;

    /**
     * @var mixed
     */
    private $testGroupCode;

    /**
     * @var mixed
     */
    private $customerConsent;

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
     * @return mixed
     */
    public function getCompanyAttributes()
    {
        return $this->companyAttributes;
    }

    /**
     * Set company attributes
     *
     * @param mixed $companyAttributes
     * @return $this
     */
    public function setCompanyAttributes($companyAttributes)
    {
        $this->companyAttributes = $companyAttributes;
        return $this;
    }

    /**
     * Get assistance allowed
     *
     * @return mixed
     */
    public function getAssistanceAllowed()
    {
        return $this->assistanceAllowed;
    }

    /**
     * Set assistance allowed
     *
     * @param mixed $assistanceAllowed
     * @return $this
     */
    public function setAssistanceAllowed($assistanceAllowed)
    {
        $this->assistanceAllowed = $assistanceAllowed;
        return $this;
    }

    /**
     * Get is subscribed
     *
     * @return mixed
     */
    public function getIsSubscribed()
    {
        return $this->isSubscribed;
    }

    /**
     * Set is subscribed
     *
     * @param mixed $isSubscribed
     * @return $this
     */
    public function setIsSubscribed($isSubscribed)
    {
        $this->isSubscribed = $isSubscribed;
        return $this;
    }

    /**
     * Get all company attributes
     *
     * @return mixed
     */
    public function getAllCompanyAttributes()
    {
        return $this->allCompanyAttributes;
    }

    /**
     * Set all company attributes
     *
     * @param mixed $allCompanyAttributes
     * @return $this
     */
    public function setAllCompanyAttributes($allCompanyAttributes)
    {
        $this->allCompanyAttributes = $allCompanyAttributes;
        return $this;
    }

    /**
     * Get test group code
     *
     * @return mixed
     */
    public function getTestGroupCode()
    {
        return $this->testGroupCode;
    }

    /**
     * Set test group code
     *
     * @param mixed $testGroupCode
     * @return $this
     */
    public function setTestGroupCode($testGroupCode)
    {
        $this->testGroupCode = $testGroupCode;
        return $this;
    }

    /**
     * Get customer consent
     *
     * @return mixed
     */
    public function getCustomerConsent()
    {
        return $this->customerConsent;
    }

    /**
     * Set customer consent
     *
     * @param mixed $customerConsent
     * @return $this
     */
    public function setCustomerConsent($customerConsent)
    {
        $this->customerConsent = $customerConsent;
        return $this;
    }
}
