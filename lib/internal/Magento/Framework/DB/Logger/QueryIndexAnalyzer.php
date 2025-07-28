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

        $selectType = strtolower($selectDetails['select_type'] ?? '');
        $type = strtolower($selectDetails['type'] ?? '');
        $key = $selectDetails['key'] ?? null;
        $extra = strtolower($selectDetails['extra'] ?? '');

        // skip small tables
        if ((int) $selectDetails['rows'] < 100 && $type === 'all') {
            return null;
        }

        // Full table scan
        if ($type === 'all' && empty($key)) {
            $issues[] = 'FULL TABLE SCAN';
        }

        // No usable index
        if (empty($key) && !str_contains($extra, 'no matching row in const table')) {
            $issues[] = 'NO INDEX';
        }

        // Using filesort (inefficient sorting)
        if (str_contains($extra, 'using filesort')) {
            $issues[] = 'FILESORT';
        }

        // Dependent subquery (re-evaluated for every row)
        if ($selectType === 'dependent subquery') {
            $issues[] = 'DEPENDENT SUBQUERY';
        }

        if ($this->isPartialIndexUsage($selectDetails)) {
            $issues[] = 'PARTIAL INDEX USED';
        }

        return $issues;
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
        if (
            str_contains($extra, 'using filesort') ||
            str_contains($extra, 'using temporary') ||
            ($type === 'index' && !str_contains($extra, 'using index'))
        ) {
            return true;
        }

        return false;
    }
}
