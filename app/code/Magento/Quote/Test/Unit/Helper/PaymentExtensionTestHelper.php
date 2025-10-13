<?php
/**
 * ADOBE CONFIDENTIAL
 *
 * Copyright 2025 Adobe
 * All Rights Reserved.
 *
 * NOTICE: All information contained herein is, and remains
 * the property of Adobe and its suppliers, if any. The intellectual
 * and technical concepts contained herein are proprietary to Adobe
 * and its suppliers and are protected by all applicable intellectual
 * property laws, including trade secret and copyright laws.
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from Adobe.
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
