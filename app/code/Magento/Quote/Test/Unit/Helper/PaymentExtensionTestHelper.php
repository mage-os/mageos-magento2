<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Api\Data\PaymentExtensionInterface;

/**
 * Test helper that implements PaymentExtensionInterface
 *
 * Provides stub implementations for all extension attribute methods
 */
class PaymentExtensionTestHelper implements PaymentExtensionInterface
{
    /**
     * @var array
     */
    private $agreementIds = [];

    /**
     * Constructor
     *
     * @param array $agreementIds
     */
    public function __construct(array $agreementIds = [])
    {
        $this->agreementIds = $agreementIds;
    }

    /**
     * Get agreement IDs
     *
     * @return array
     */
    public function getAgreementIds(): array
    {
        return $this->agreementIds;
    }

    /**
     * Set agreement IDs
     *
     * @param array $agreementIds
     * @return $this
     */
    public function setAgreementIds($agreementIds)
    {
        $this->agreementIds = $agreementIds;
        return $this;
    }
}
