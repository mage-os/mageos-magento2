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
    private const FULL_TABLE_SCAN = 'FULL TABLE SCAN';

    private const NO_INDEX = 'NO INDEX';

    private const FILESORT = 'FILESORT';

    private const DEPENDENT_SUBQUERY = 'DEPENDENT SUBQUERY';

    private const PARTIAL_INDEX = 'PARTIAL INDEX USED';

    /**
     * @param ResourceConnection $resource
     */
    public function __construct(private readonly ResourceConnection $resource)
    {
    }

    /**
     * @param string $sql
     * @param array $bindings
     * @return array
     * @throws \Zend_Db_Statement_Exception|\InvalidArgumentException
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
            throw new \InvalidArgumentException("Small table");
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
        $issues = [];
        $smallTableCount = 0;
        foreach ($explainOutput as $key => $row) {
            $result = $this->getQueryIssues($row);
            if ($result === null) {
                $smallTableCount++;
            }
            $issues[$key] = $result;
        }

        if ($smallTableCount == count($explainOutput)) {
            return null;
        }
        $result = [];
        foreach ($issues as $queryIssues) {
            if ($queryIssues) {
                $result = [...$queryIssues, ...$result];
            }
        }

        return $result;
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
        if ((int) $selectDetails['rows'] < 100 && $type === 'all') {
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

        if ($selectType === 'dependent subquery') {
            return true;
        }

        return false;
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

        if (str_contains($extra, 'using filesort')) {
            return true;
        }

        return false;
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

        if (empty($key) && !str_contains($extra, 'no matching row in const table')) {
            return true;
        }

        return false;
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

        if ($type === 'all' && empty($key)) {
            return true;
        }

        return false;
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

        // Good: covering index (no need to read from table)
        if (str_contains($extra, 'using index') && !str_contains($extra, 'using where')) {
            return false;
        }

        // Good: very efficient access types
        if (in_array($type, ['const', 'eq_ref'])) {
            return false;
        }

        // Acceptable: range/index lookup with covering index (even if filtered)
        if (str_contains($extra, 'using index') &&
            str_contains($extra, 'using where') &&
            in_array($type, ['range', 'ref'])) {
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
}
