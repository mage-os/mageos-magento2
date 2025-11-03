<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CustomerImportExport\Test\Unit\Helper;

/**
 * Test helper for data source model
 */
class DataSourceModelTestHelper
{
    /**
     * @var array|null
     */
    private ?array $nextBunch = null;

    /**
     * Get next bunch
     *
     * @return array|null
     */
    public function getNextBunch(): ?array
    {
        return $this->nextBunch;
    }

    /**
     * Set next bunch
     *
     * @param array|null $bunch
     * @return $this
     */
    public function setNextBunch(?array $bunch): self
    {
        $this->nextBunch = $bunch;
        return $this;
    }
}
