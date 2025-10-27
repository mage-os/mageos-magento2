<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\CustomerImportExport\Test\Unit\Helper;

/**
 * Test helper for data source bunch operations
 */
class DataSourceBunchTestHelper
{
    /**
     * @var callable|null
     */
    private $saveBunchCallback = null;

    /**
     * Clean bunches
     *
     * @return void
     */
    public function cleanBunches(): void
    {
        // Stub implementation
    }

    /**
     * Save bunch
     *
     * @param string $entityType
     * @param string $behavior
     * @param array $bunchRows
     * @return mixed
     */
    public function saveBunch($entityType, $behavior, $bunchRows)
    {
        if ($this->saveBunchCallback) {
            return call_user_func($this->saveBunchCallback, $entityType, $behavior, $bunchRows);
        }
        return null;
    }

    /**
     * Set save bunch callback
     *
     * @param callable $callback
     * @return $this
     */
    public function setSaveBunchCallback(callable $callback): self
    {
        $this->saveBunchCallback = $callback;
        return $this;
    }
}
