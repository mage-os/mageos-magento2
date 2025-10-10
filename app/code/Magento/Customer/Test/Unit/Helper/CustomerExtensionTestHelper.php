<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Customer\Api\Data\CustomerExtensionInterface;

/**
 * Test helper that implements CustomerExtensionInterface
 * 
 * Provides getCompanyAttributes/setCompanyAttributes methods for testing
 */
class CustomerExtensionTestHelper implements CustomerExtensionInterface
{
    /**
     * @var mixed
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
}

