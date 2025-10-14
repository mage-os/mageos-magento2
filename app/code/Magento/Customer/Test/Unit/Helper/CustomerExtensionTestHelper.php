<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Customer\Api\Data\CustomerExtension;

/**
 * Test helper for Magento\Customer\Api\Data\CustomerExtension
 *
 * WHY THIS HELPER IS REQUIRED:
 * - CustomerExtension is dynamically generated extension attributes interface
 * - Methods like getCompanyAttributes/setCompanyAttributes are added dynamically
 * - Cannot use createPartialMock on dynamically generated methods
 * - Provides explicit implementation for testing company attributes functionality
 *
 * Used By: Multiple NegotiableQuote test files
 */
class CustomerExtensionTestHelper extends CustomerExtension
{
    /**
     * @var mixed
     */
    private $companyAttributes;

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
