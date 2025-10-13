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
}
