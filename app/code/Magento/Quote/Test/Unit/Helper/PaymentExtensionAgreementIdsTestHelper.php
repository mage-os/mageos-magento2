<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

/**
 * Test helper representing payment extension attributes with agreement IDs.
 */
class PaymentExtensionAgreementIdsTestHelper
{
    /** @var array<int, int>|null */
    private $agreementIds = null;

    /**
     * @return array<int, int>|null
     */
    public function getAgreementIds()
    {
        return $this->agreementIds;
    }

    /**
     * @param array<int, int>|null $ids
     * @return $this
     */
    public function setAgreementIds($ids)
    {
        $this->agreementIds = $ids;
        return $this;
    }
}


