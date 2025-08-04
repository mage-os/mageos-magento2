<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\DB\Logger;

use Magento\Framework\App\ResourceConnection;

class QueryIndexAnalyzer implements QueryAnalyzerInterface
{
    private const DEFAULT_SMALL_TABLE_THRESHOLD = 100;

    /**
     * @var int
     */
    private int $smallTableThreshold;

    /**
     * @param ResourceConnection $resource
     * @param int|null $smallTableThreshold
     */
    public function __construct(private readonly ResourceConnection $resource, ?int $smallTableThreshold = null)
    {
        if ($smallTableThreshold !== null) {
            $this->smallTableThreshold = $smallTableThreshold;
        } else {
            $this->smallTableThreshold = self::DEFAULT_SMALL_TABLE_THRESHOLD;
        }
    }

    /**
     * Check for potential index issues
     *
     * @param string $sql
     * @param array $bindings
     * @return array
     * @throws \Zend_Db_Statement_Exception|\InvalidArgumentException|QueryAnalyzerException
     */
    public function process(string $sql, array $bindings): array
    {
        if (!$this->isSelectQuery($sql)) {
            throw new \InvalidArgumentException("Can't process query type");
        }

        $connection = $this->resource->getConnection();
        $explainOutput = $connection->query('EXPLAIN ' . $sql, $bindings)->fetchAll();

        if (empty($explainOutput)) {
            throw new \InvalidArgumentException("No 'explain' output available");
        }

        $issues = $this->analyzeQueries($explainOutput);
        if ($issues === null) {
            throw new QueryAnalyzerException("Small table");
        }

        return array_values(array_unique($issues));
    }

    /**
     * Detects if a given SQL string is a SELECT query.
     *
     * @param string $query
     * @return bool
     */
    private function isSelectQuery(string $query): bool
    {
        $cleaned = ltrim($query);

        // Remove leading SQL line comments (e.g., -- comment) and block comments (/* ... */)
        while (preg_match('/^(--[^\n]*\n|\/\*.*?\*\/\s*)/s', $cleaned, $matches)) {
            $cleaned = ltrim(substr($cleaned, strlen($matches[0])));
        }

        // Check if the cleaned string starts with SELECT (case-insensitive)
        return (bool) preg_match('/^SELECT\b/i', $cleaned);
    }

    /**
     * Check each select from given query for potential issues
     *
     * @param array $explainOutput
     * @return array|null
     */
    private function analyzeQueries(array $explainOutput): ?array
    {
        $issues = array_map(fn (array $row) => $this->getQueryIssues($row), $explainOutput);
        if (!array_filter($issues, 'is_array')) {
            return null;
        }

        return array_merge(...array_filter($issues));
    }

    /**
     * Check EXPLAIN output for potential issues
     *
     * @param array $selectDetails
     * @return array|null
     */
    private function getQueryIssues(array $selectDetails): ?array
    {
        $issues = [];
        $selectDetails = array_change_key_case($selectDetails);
        $type = strtolower($selectDetails['type'] ?? '');

        // skip small tables
        if ((int) $selectDetails['rows'] < $this->smallTableThreshold && $type === 'all') {
            return null;
        }

        if ($this->hasFullTableScan($selectDetails)) {
            $issues[] = self::FULL_TABLE_SCAN;
        }

        if ($this->isUsingIndex($selectDetails)) {
            $issues[] = self::NO_INDEX;
        }

        if ($this->isUsingFileSort($selectDetails)) {
            $issues[] = self::FILESORT;
        }

        if ($this->hasDependentSubquery($selectDetails)) {
            $issues[] = self::DEPENDENT_SUBQUERY;
        }

        if ($this->isPartialIndexUsage($selectDetails)) {
            $issues[] = self::PARTIAL_INDEX;
        }

        return $issues;
    }

    /**
     * Check if dependent subqueries are used
     *
     * @param array $selectDetails
     * @return bool
     */
    private function hasDependentSubquery(array $selectDetails): bool
    {
        $selectType = strtolower($selectDetails['select_type'] ?? '');

        return $selectType === 'dependent subquery';
    }

    /**
     * Check if query is using filesort
     *
     * @param array $selectDetails
     * @return bool
     */
    private function isUsingFileSort(array $selectDetails): bool
    {
        $extra = strtolower($selectDetails['extra'] ?? '');

        return str_contains($extra, 'using filesort');
    }

    /**
     * Check if query optimizer is using an index
     *
     * @param array $selectDetails
     * @return bool
     */
    private function isUsingIndex(array $selectDetails): bool
    {
        $extra = strtolower($selectDetails['extra'] ?? '');
        $key = $selectDetails['key'] ?? null;

        return empty($key) && !str_contains($extra, 'no matching row in const table');
    }

    /**
     * Check if query uses full table scan
     *
     * @param array $selectDetails
     * @return bool
     */
    private function hasFullTableScan(array $selectDetails): bool
    {
        $key = $selectDetails['key'] ?? null;
        $type = $selectDetails['type'] ?? '';

        return strtolower($type) === 'all' && empty($key);
    }

    /**
     * Check for partial index usage
     *
     * @param array $row
     * @return bool
     */
    private function isPartialIndexUsage(array $row): bool
    {
        $extra = strtolower($row['extra'] ?? '');
        $type = strtolower($row['type'] ?? '');
        $key = $row['key'] ?? '';

        if (empty($key)) {
            return false;
        }

        if ($this->checkForCoveringIndex($extra, $type)) {
            return false;
        }

        if ($this->checkEfficientAccessTypes($type)) {
            return false;
        }

        // Partial usage: index used but not covering, or used inefficiently
        if (str_contains($extra, 'using filesort') ||
            str_contains($extra, 'using temporary') ||
            ($type === 'index' && !str_contains($extra, 'using index'))
        ) {
            return true;
        }

        return false;
    }

    /**
     * Check for clues over covering index
     *
     * @param string $extra
     * @param string $type
     * @return bool
     */
    private function checkForCoveringIndex(string $extra, string $type): bool
    {
        return str_contains($extra, 'using index')
            && !str_contains($extra, 'using where')
            || str_contains($extra, 'using index')
            && str_contains($extra, 'using where')
            && in_array($type, ['range', 'ref']);
    }

    /**
     * Check if query is using an efficient access type
     *
     * @param string $type
     * @return bool
     */
    private function checkEfficientAccessTypes(string $type): bool
    {
        return in_array($type, ['const', 'eq_ref']);
    }
}
