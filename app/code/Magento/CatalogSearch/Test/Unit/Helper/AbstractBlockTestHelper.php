<?php
/**
 * Copyright 2016 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogSearch\Test\Unit\Helper;

use Magento\Framework\View\Element\AbstractBlock;

/**
 * Test helper for AbstractBlock
 *
 * This helper extends the concrete AbstractBlock class to provide
 * test-specific functionality without dependency injection issues.
 */
class AbstractBlockTestHelper extends AbstractBlock
{
    /**
     * Mock method for addFieldMap
     *
     * @param string $fieldId
     * @param string $fieldName
     * @return $this
     */
    public function addFieldMap(string $fieldId, string $fieldName): self
    {
        return $this;
    }

    /**
     * Mock method for addFieldDependence
     *
     * @param string $fieldId
     * @param string $dependentField
     * @param string $value
     * @return $this
     */
    public function addFieldDependence(string $fieldId, string $dependentField, string $value): self
    {
        return $this;
    }

    /**
     * Required method from AbstractBlock
     *
     * @return void
     */
    protected function _construct(): void
    {
        // Mock implementation
    }
}
