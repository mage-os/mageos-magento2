<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\DB\Logger;

interface QueryAnalyzerInterface
{
    /**
     * Analyze query
     *
     * @param string $sql
     * @param array $bindings
     * @return array
     */
    public function process(string $sql, array $bindings): array;
}
