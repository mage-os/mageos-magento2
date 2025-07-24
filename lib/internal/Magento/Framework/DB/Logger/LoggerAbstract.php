<?php
/**
 * Copyright 2014 Adobe
 * All Rights Reserved.
 */
namespace Magento\Framework\DB\Logger;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\LoggerInterface;
use Magento\Framework\Debug;

abstract class LoggerAbstract implements LoggerInterface
{
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
     * {@inheritdoc}
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
        $message = '## ' . getmypid() . ' ## ';
        $nl   = "\n";
        $time = sprintf('%.4f', microtime(true) - $this->timer);

        if (!$this->logAllQueries && $time < $this->logQueryTime) {
            return '';
        }
        switch ($type) {
            case self::TYPE_CONNECT:
                $message .= 'CONNECT' . $nl;
                break;
            case self::TYPE_TRANSACTION:
                $message .= 'TRANSACTION ' . $sql . $nl;
                break;
            case self::TYPE_QUERY:
                $message .= 'QUERY' . $nl;
                $message .= 'SQL: ' . $sql . $nl;
                if ($bind) {
                    $message .= 'BIND: ' . var_export($bind, true) . $nl;
                }
                if ($result instanceof \Zend_Db_Statement_Pdo) {
                    $message .= 'AFF: ' . $result->rowCount() . $nl;
                }
                if ($this->logIndexCheck) {
                    $message .= 'INDEX CHECK: ' . $this->getIndexUsage($sql, $bind) . $nl;
                }
                break;
        }
        $message .= 'TIME: ' . $time . $nl;

        if ($this->logCallStack) {
            $message .= 'TRACE: ' . Debug::backtrace(true, false) . $nl;
        }

        $message .= $nl;

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

        $issues = [];
        foreach ($explainOutput as $row) {
            $row = array_change_key_case($row);

            $selectType = strtolower($row['select_type'] ?? '');
            $type = strtolower($row['type'] ?? '');
            $key = $row['key'] ?? null;
            $keyLen = $row['key_len'] ?? null;
            $extra = strtolower($row['extra'] ?? '');

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

            // Partial index usage
            if (!empty($key) && !empty($keyLen) && (int)$keyLen < 4) {
                $issues[] = 'PARTIAL INDEX USED';
            }
        }

        return empty($issues) ? 'USING INDEX' : implode(', ', array_unique($issues));
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
