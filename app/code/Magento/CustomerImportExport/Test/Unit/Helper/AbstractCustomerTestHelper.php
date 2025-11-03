<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\CustomerImportExport\Test\Unit\Helper;

use Magento\CustomerImportExport\Model\Import\AbstractCustomer;
use Magento\Framework\Data\Collection;

class AbstractCustomerTestHelper extends AbstractCustomer
{
    /**
     * @var Collection|null
     */
    private ?Collection $customerCollection = null;

    /**
     * Constructor that skips parent dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get customer collection (custom method for testing)
     *
     * @return Collection|null
     */
    protected function _getCustomerCollection(): ?Collection
    {
        return $this->customerCollection;
    }

    /**
     * Set customer collection for testing
     *
     * @param Collection $collection
     * @return $this
     */
    public function setCustomerCollection(Collection $collection): self
    {
        $this->customerCollection = $collection;
        return $this;
    }

    /**
     * Validate row for update (stub implementation)
     *
     * @param array $rowData
     * @param int $rowNumber
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _validateRowForUpdate(array $rowData, $rowNumber)
    {
        // Stub implementation - no validation needed in tests
    }

    /**
     * Validate row for delete (stub implementation)
     *
     * @param array $rowData
     * @param int $rowNumber
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _validateRowForDelete(array $rowData, $rowNumber)
    {
        // Stub implementation - no validation needed in tests
    }

    /**
     * Import data (stub implementation)
     *
     * @return bool
     */
    protected function _importData(): bool
    {
        return true;
    }

    /**
     * Get entity type code (stub implementation)
     *
     * @return string
     */
    public function getEntityTypeCode(): string
    {
        return 'customer';
    }
}
