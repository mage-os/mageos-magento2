<?php
/**
 * Copyright 2014 Adobe
 * All Rights Reserved.
 */
namespace Magento\Framework\DB\Logger;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\LoggerInterface;
use Magento\Framework\Debug;
use Zend_Db_Statement_Pdo;

abstract class LoggerAbstract implements LoggerInterface
{
    private const LINE_DELIMITER = "\n";

    /**
     * @var int
     */
    private $timer;

    /**
     * @var bool
     */
    private $logAllQueries;

    /**
     * @var float
     */
    private $logQueryTime;

    /**
     * @var bool
     */
    private $logCallStack;

    /**
     * @var bool
     */
    private bool $logIndexCheck;

    /**
     * @var ResourceConnection
     */
    private ResourceConnection $resource;

    /**
     * @param ResourceConnection $resource
     * @param bool $logAllQueries
     * @param float $logQueryTime
     * @param bool $logCallStack
     * @param bool $logIndexCheck
     */
    public function __construct(
        ResourceConnection $resource,
        $logAllQueries = false,
        $logQueryTime = 0.05,
        $logCallStack = false,
        $logIndexCheck = false
    ) {
        $this->resource = $resource;
        $this->logAllQueries = $logAllQueries;
        $this->logQueryTime = $logQueryTime;
        $this->logCallStack = $logCallStack;
        $this->logIndexCheck = $logIndexCheck;
    }

    /**
     * @inheritDoc
     */
    public function startTimer()
    {
        $this->timer = microtime(true);
    }

    /**
     * Get formatted statistics message
     *
     * @param string $type Type of query
     * @param string $sql
     * @param array $bind
     * @param \Zend_Db_Statement_Pdo|null $result
     * @return string
     * @throws \Zend_Db_Statement_Exception
     */
    public function getStats($type, $sql, $bind = [], $result = null)
    {
        $time = sprintf('%.4f', microtime(true) - $this->timer);

        if (!$this->logAllQueries && $time < $this->logQueryTime) {
            return '';
        }

        if ($this->isExplainQuery($sql)) {
            return '';
        }

        return $this->buildDebugMessage($type, $sql, $bind, $result, $time);
    }

    /**
     * Check if query already contains 'explain' keyword
     *
     * @param string $query
     * @return bool
     */
    private function isExplainQuery(string $query): bool
    {
        // Remove leading/trailing whitespace and normalize case
        $cleaned = ltrim($query);

        // Strip comments
        while (preg_match('/^(--[^\n]*\n|\/\*.*?\*\/\s*)/s', $cleaned, $matches)) {
            $cleaned = ltrim(substr($cleaned, strlen($matches[0])));
        }

        // Check if it starts with EXPLAIN
        return (bool) preg_match('/^EXPLAIN\b/i', $cleaned);
    }

    /**
     * Build log message based on query type
     *
     * @param string $type
     * @param string $sql
     * @param array $bind
     * @param Zend_Db_Statement_Pdo|null $result
     * @param string $time
     * @return string
     * @throws \Zend_Db_Statement_Exception
     */
    private function buildDebugMessage(
        string $type,
        string $sql,
        array $bind,
        ?Zend_Db_Statement_Pdo $result,
        string $time
    ): string {
        $message = '## ' . getmypid() . ' ## ';

        switch ($type) {
            case self::TYPE_CONNECT:
                $message .= 'CONNECT' . self::LINE_DELIMITER;
                break;
            case self::TYPE_TRANSACTION:
                $message .= 'TRANSACTION ' . $sql . self::LINE_DELIMITER;
                break;
            case self::TYPE_QUERY:
                $message .= 'QUERY' . self::LINE_DELIMITER;
                $message .= 'SQL: ' . $sql . self::LINE_DELIMITER;
                if ($bind) {
                    $message .= 'BIND: ' . var_export($bind, true) . self::LINE_DELIMITER;
                }
                if ($result instanceof \Zend_Db_Statement_Pdo) {
                    $message .= 'AFF: ' . $result->rowCount() . self::LINE_DELIMITER;
                }
                if ($this->logIndexCheck) {
                    $message .= 'INDEX CHECK: ' . $this->getIndexUsage($sql, $bind) . self::LINE_DELIMITER;
                }
                break;
        }
        $message .= 'TIME: ' . $time . self::LINE_DELIMITER;

        if ($this->logCallStack) {
            $message .= 'TRACE: ' . Debug::backtrace(true, false) . self::LINE_DELIMITER;
        }

        $message .= self::LINE_DELIMITER;

        return $message;
    }

    /**
     * Detects index usage for a given query
     *
     * @param string $sql
     * @param array $bind
     * @return string
     * @throws \Zend_Db_Statement_Exception
     */
    private function getIndexUsage(string $sql, array $bind): string
    {
        if (!$this->isSelectQuery($sql)) {
            return 'NA';
        }

        $connection = $this->resource->getConnection();
        $explainOutput = $connection->query('EXPLAIN ' . $sql, $bind)->fetchAll();

        if (empty($explainOutput)) {
            return 'NA';
        }

        try {
            $issues = $this->getPotentialQueryIssues($explainOutput);
        } catch (\Throwable) {
            return 'NA';
        }

        return empty($issues) ? 'USING INDEX' : 'POTENTIAL ISSUES - ' . implode(', ', array_unique($issues));
    }

    /**
     * Check each select from given query for potential issues
     *
     * @param array $explainOutput
     * @return array
     * @throws \Exception
     */
    private function getPotentialQueryIssues(array $explainOutput): array
    {
        $issues = [];
        foreach ($explainOutput as $row) {
            $issues = [...$issues, ...$this->getQueryIssues($row)];
        }

        return $issues;
    }

    /**
     * Check EXPLAIN output for potential issues
     *
     * @param array $selectDetails
     * @return array
     * @throws \Exception
     */
    private function getQueryIssues(array $selectDetails): array
    {
        $issues = [];
        $selectDetails = array_change_key_case($selectDetails);

        $selectType = strtolower($selectDetails['select_type'] ?? '');
        $type = strtolower($selectDetails['type'] ?? '');
        $key = $selectDetails['key'] ?? null;
        $extra = strtolower($selectDetails['extra'] ?? '');

        // skip small tables
        if ((int) $selectDetails['rows'] < 100 && strtolower($selectDetails['type']) === 'all') {
            throw new \Exception('Small table');
        }

        // Full table scan
        if ($type === 'all' && empty($key)) {
            $issues[] = 'FULL TABLE SCAN';
        }


        // No usable index
        if (empty($key)) {
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

        // Good full index usage if it's a covering index
        if (!empty($row['key']) && str_contains($extra, 'using index') && !str_contains($extra, 'using where')) {
            return false;
        }

        // If using index but still filtering or sorting, it's partial
        if (!empty($row['key']) && (
                str_contains($extra, 'using where') ||
                str_contains($extra, 'using filesort') ||
                str_contains($extra, 'using temporary')
            )) {
            return true;
        }

        // If an index is used but with full index scan
        if ($row['type'] === 'index' && !str_contains($extra, 'using index')) {
            return true;
        }

        return false;
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
}
