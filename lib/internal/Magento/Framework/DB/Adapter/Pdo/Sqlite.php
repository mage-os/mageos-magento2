<?php
/**
 * Copyright © Mage-OS, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\DB\Adapter\Pdo;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Sql\Expression;
use Magento\Framework\DB\Sql\SqliteQueryRewriter;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

/**
 * SQLite database adapter for development environments
 *
 * WARNING: This adapter is intended for DEVELOPMENT MODE ONLY.
 * Do not use in production environments.
 *
 * @api
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Sqlite extends Mysql
{
    /**
     * SQLite-specific DDL cache prefix
     */
    public const DDL_CACHE_PREFIX = 'DB_PDO_SQLITE_DDL';
    public const DDL_CACHE_TAG = 'DB_PDO_SQLITE_DDL';

    /**
     * Default statement class for SQLite
     *
     * @var string
     */
    protected $_defaultStmtClass = \Magento\Framework\DB\Statement\Pdo\Mysql::class;

    /**
     * Query rewriter for MySQL → SQLite translation
     *
     * @var SqliteQueryRewriter
     */
    protected $queryRewriter;

    /**
     * Query logging enabled flag
     *
     * @var bool
     */
    protected $queryLoggingEnabled = false;

    /**
     * Type mapping from MySQL to SQLite
     *
     * @var array
     */
    protected $typeMapping = [
        'int' => 'INTEGER',
        'integer' => 'INTEGER',
        'smallint' => 'INTEGER',
        'bigint' => 'INTEGER',
        'tinyint' => 'INTEGER',
        'mediumint' => 'INTEGER',
        'decimal' => 'REAL',
        'numeric' => 'REAL',
        'float' => 'REAL',
        'double' => 'REAL',
        'varchar' => 'TEXT',
        'char' => 'TEXT',
        'text' => 'TEXT',
        'mediumtext' => 'TEXT',
        'longtext' => 'TEXT',
        'tinytext' => 'TEXT',
        'blob' => 'BLOB',
        'mediumblob' => 'BLOB',
        'longblob' => 'BLOB',
        'tinyblob' => 'BLOB',
        'varbinary' => 'BLOB',
        'timestamp' => 'INTEGER',
        'datetime' => 'TEXT',
        'date' => 'TEXT',
        'time' => 'TEXT',
        'year' => 'INTEGER',
    ];

    /**
     * Begin new DB transaction for connection
     *
     * SQLite uses BEGIN IMMEDIATE for write locks
     *
     * @return $this
     */
    public function beginTransaction()
    {
        if ($this->_transactionLevel === 0) {
            $this->_connection->exec('BEGIN IMMEDIATE');
        }
        $this->_transactionLevel++;
        return $this;
    }

    /**
     * Initialize query rewriter and logging
     *
     * @return void
     */
    protected function _initializeQueryRewriter()
    {
        if ($this->queryRewriter === null) {
            $this->queryRewriter = new SqliteQueryRewriter();
        }

        // Check if query logging is enabled
        if (isset($this->_config['driver_options']['sqlite_query_logging'])) {
            $this->queryLoggingEnabled = (bool)$this->_config['driver_options']['sqlite_query_logging'];
        }
    }

    /**
     * Enable SQLite optimizations for development
     *
     * @return $this
     */
    protected function _applyDevOptimizations()
    {
        // Enable Write-Ahead Logging for better concurrency
        $this->_connection->exec('PRAGMA journal_mode=WAL');

        // Faster writes, acceptable for dev environments
        $this->_connection->exec('PRAGMA synchronous=NORMAL');

        // Increase cache size (10MB)
        $this->_connection->exec('PRAGMA cache_size=10000');

        // Store temp tables in memory
        $this->_connection->exec('PRAGMA temp_store=MEMORY');

        // Enable foreign keys
        $this->_connection->exec('PRAGMA foreign_keys=ON');

        // Initialize query rewriter
        $this->_initializeQueryRewriter();

        return $this;
    }

    /**
     * Setup connection and apply SQLite-specific optimizations
     *
     * @return void
     */
    public function _connect()
    {
        parent::_connect();
        $this->_applyDevOptimizations();
    }

    /**
     * Run additional environment before setup
     *
     * SQLite-specific setup without MySQL variables
     *
     * @return $this
     */
    public function startSetup()
    {
        // Disable foreign key checks during setup
        $this->rawQuery('PRAGMA foreign_keys=OFF');
        return $this;
    }

    /**
     * Run additional environment after setup
     *
     * Re-enable foreign keys after setup
     *
     * @return $this
     */
    public function endSetup()
    {
        // Re-enable foreign keys
        $this->rawQuery('PRAGMA foreign_keys=ON');
        return $this;
    }

    /**
     * Returns the column descriptions for a table (SQLite version)
     *
     * @param string $tableName
     * @param string $schemaName
     * @return array
     */
    public function describeTable($tableName, $schemaName = null)
    {
        $cacheKey = $this->_getTableName($tableName, $schemaName);
        $ddl = $this->loadDdlCache($cacheKey, self::DDL_DESCRIBE);
        if ($ddl === false) {
            $ddl = $this->_describeTableSqlite($tableName, $schemaName);
            $this->saveDdlCache($cacheKey, self::DDL_DESCRIBE, $ddl);
        }
        return $ddl;
    }

    /**
     * Retrieve column descriptions from SQLite
     *
     * @param string $tableName
     * @param string $schemaName
     * @return array
     */
    protected function _describeTableSqlite($tableName, $schemaName = null)
    {
        $table = $this->_getTableName($tableName, $schemaName);
        $sql = sprintf('PRAGMA table_info(%s)', $this->quoteIdentifier($table));
        $result = $this->fetchAll($sql);

        $columns = [];
        foreach ($result as $row) {
            $type = strtolower($row['type']);
            $typeParts = explode('(', $type);
            $baseType = $typeParts[0];

            // Extract length/precision if present
            $length = null;
            $scale = null;
            $precision = null;

            if (isset($typeParts[1])) {
                $params = rtrim($typeParts[1], ')');
                $paramParts = explode(',', $params);
                if (count($paramParts) === 2) {
                    $precision = (int)$paramParts[0];
                    $scale = (int)$paramParts[1];
                } else {
                    $length = (int)$paramParts[0];
                }
            }

            $columns[strtoupper($row['name'])] = [
                'SCHEMA_NAME' => $schemaName,
                'TABLE_NAME' => $tableName,
                'COLUMN_NAME' => $row['name'],
                'COLUMN_POSITION' => $row['cid'] + 1,
                'DATA_TYPE' => $baseType,
                'DEFAULT' => $row['dflt_value'],
                'NULLABLE' => !$row['notnull'],
                'LENGTH' => $length,
                'SCALE' => $scale,
                'PRECISION' => $precision,
                'UNSIGNED' => false, // SQLite doesn't have UNSIGNED
                'PRIMARY' => $row['pk'] > 0,
                'PRIMARY_POSITION' => $row['pk'] > 0 ? $row['pk'] : null,
                'IDENTITY' => $row['pk'] > 0 && strpos(strtolower($row['type']), 'autoincrement') !== false,
            ];
        }

        return $columns;
    }

    /**
     * Translate MySQL column definition to SQLite
     *
     * @param array|string $definition
     * @return string
     */
    protected function _translateColumnDefinition($definition)
    {
        if (is_string($definition)) {
            return $this->_translateRawSql($definition);
        }

        if (!is_array($definition)) {
            return 'TEXT';
        }

        $type = isset($definition['type']) ? strtolower($definition['type']) : 'text';
        $sqliteType = $this->typeMapping[$type] ?? 'TEXT';

        $sql = $sqliteType;

        // Add length for certain types (though SQLite largely ignores it)
        if (isset($definition['length']) && in_array($sqliteType, ['TEXT', 'BLOB'])) {
            $sql .= '(' . $definition['length'] . ')';
        }

        // Handle decimal/numeric precision
        if (isset($definition['precision']) && isset($definition['scale'])) {
            $sql .= '(' . $definition['precision'] . ',' . $definition['scale'] . ')';
        }

        // Primary key
        if (!empty($definition['primary'])) {
            $sql .= ' PRIMARY KEY';
        }

        // Auto increment
        if (!empty($definition['identity']) || !empty($definition['auto_increment'])) {
            $sql .= ' AUTOINCREMENT';
        }

        // Nullable
        if (isset($definition['nullable']) && !$definition['nullable']) {
            $sql .= ' NOT NULL';
        }

        // Default value
        if (array_key_exists('default', $definition)) {
            if ($definition['default'] instanceof Expression) {
                $sql .= ' DEFAULT ' . $definition['default'];
            } elseif ($definition['default'] === null) {
                $sql .= ' DEFAULT NULL';
            } elseif (is_numeric($definition['default'])) {
                $sql .= ' DEFAULT ' . $definition['default'];
            } else {
                $sql .= ' DEFAULT ' . $this->quote($definition['default']);
            }
        }

        return $sql;
    }

    /**
     * Translate raw MySQL SQL to SQLite
     *
     * @param string $sql
     * @return string
     */
    protected function _translateRawSql($sql)
    {
        // Remove MySQL-specific keywords
        $sql = preg_replace('/\s+UNSIGNED/i', '', $sql);
        $sql = preg_replace('/\s+AUTO_INCREMENT/i', ' AUTOINCREMENT', $sql);
        $sql = preg_replace('/ENGINE\s*=\s*\w+/i', '', $sql);
        $sql = preg_replace('/DEFAULT\s+CHARSET\s*=\s*\w+/i', '', $sql);
        $sql = preg_replace('/COLLATE\s+\w+/i', '', $sql);
        $sql = preg_replace('/CHARACTER\s+SET\s+\w+/i', '', $sql);
        $sql = preg_replace('/COMMENT\s+\'[^\']*\'/i', '', $sql);

        // Convert MySQL types to SQLite
        foreach ($this->typeMapping as $mysqlType => $sqliteType) {
            $pattern = '/\b' . preg_quote($mysqlType, '/') . '\b/i';
            $sql = preg_replace($pattern, $sqliteType, $sql);
        }

        return trim($sql);
    }

    /**
     * Generate fragment of SQL, that check condition and return true or false value
     *
     * @param string $condition
     * @param string $true
     * @param string $false
     * @return Expression
     */
    public function getCheckSql($condition, $true, $false)
    {
        // SQLite uses CASE WHEN instead of IF
        return new Expression("CASE WHEN {$condition} THEN {$true} ELSE {$false} END");
    }

    /**
     * Returns valid IFNULL expression for SQLite
     *
     * @param string $expression
     * @param string|int $value
     * @return Expression
     */
    public function getIfNullSql($expression, $value = 0)
    {
        // SQLite uses IFNULL (same as MySQL)
        return new Expression(sprintf('IFNULL(%s, %s)', $expression, $value));
    }

    /**
     * Generate fragment of SQL, that combine together (concatenate) the results from data array
     *
     * @param array $data
     * @param string $separator
     * @return Expression
     */
    public function getConcatSql(array $data, $separator = null)
    {
        if ($separator === null) {
            // Simple concatenation
            return new Expression(implode(' || ', $data));
        } else {
            // Concatenation with separator
            $separator = $this->quote($separator);
            $parts = [];
            foreach ($data as $i => $item) {
                if ($i > 0) {
                    $parts[] = $separator;
                }
                $parts[] = $item;
            }
            return new Expression(implode(' || ', $parts));
        }
    }

    /**
     * Generate fragment of SQL that returns length of character string
     *
     * @param string $string
     * @return Expression
     */
    public function getLengthSql($string)
    {
        // SQLite uses LENGTH (same as MySQL CHAR_LENGTH)
        return new Expression(sprintf('LENGTH(%s)', $string));
    }

    /**
     * Format date as specified
     *
     * @param \Zend_Db_Expr|string $date
     * @param string $format
     * @return Expression
     */
    public function getDateFormatSql($date, $format)
    {
        // SQLite uses strftime for date formatting
        // Convert MySQL format to SQLite strftime format
        $sqliteFormat = str_replace(
            ['%Y', '%m', '%d', '%H', '%i', '%s'],
            ['%Y', '%m', '%d', '%H', '%M', '%S'],
            $format
        );

        return new Expression(sprintf("strftime('%s', %s)", $sqliteFormat, $date));
    }

    /**
     * Extract the date part of a date or datetime expression
     *
     * @param \Zend_Db_Expr|string $date
     * @return Expression
     */
    public function getDatePartSql($date)
    {
        return new Expression(sprintf("date(%s)", $date));
    }

    /**
     * Add time values (intervals) to a date value
     *
     * @param \Zend_Db_Expr|string $date
     * @param int $interval
     * @param string $unit
     * @return Expression
     */
    public function getDateAddSql($date, $interval, $unit)
    {
        // SQLite datetime function: datetime(date, '+5 days')
        $sqliteUnit = $this->_convertIntervalUnit($unit);
        return new Expression(sprintf("datetime(%s, '+%d %s')", $date, $interval, $sqliteUnit));
    }

    /**
     * Subtract time values (intervals) to a date value
     *
     * @param \Zend_Db_Expr|string $date
     * @param int|string $interval
     * @param string $unit
     * @return Expression
     */
    public function getDateSubSql($date, $interval, $unit)
    {
        $sqliteUnit = $this->_convertIntervalUnit($unit);
        return new Expression(sprintf("datetime(%s, '-%d %s')", $date, $interval, $sqliteUnit));
    }

    /**
     * Convert MySQL interval unit to SQLite
     *
     * @param string $unit
     * @return string
     */
    protected function _convertIntervalUnit($unit)
    {
        $map = [
            self::INTERVAL_SECOND => 'seconds',
            self::INTERVAL_MINUTE => 'minutes',
            self::INTERVAL_HOUR => 'hours',
            self::INTERVAL_DAY => 'days',
            self::INTERVAL_MONTH => 'months',
            self::INTERVAL_YEAR => 'years',
        ];

        return $map[strtoupper($unit)] ?? 'days';
    }

    /**
     * Returns the table index information
     *
     * @param string $tableName
     * @param string $schemaName
     * @return array
     */
    public function getIndexList($tableName, $schemaName = null)
    {
        $table = $this->_getTableName($tableName, $schemaName);
        $cacheKey = $table;
        $ddl = $this->loadDdlCache($cacheKey, self::DDL_INDEX);

        if ($ddl === false) {
            $ddl = [];

            // Get all indexes
            $sql = sprintf('PRAGMA index_list(%s)', $this->quoteIdentifier($table));
            $indexes = $this->fetchAll($sql);

            foreach ($indexes as $index) {
                $indexName = $index['name'];

                // Get index columns
                $colSql = sprintf('PRAGMA index_info(%s)', $this->quoteIdentifier($indexName));
                $columns = $this->fetchAll($colSql);

                $columnNames = [];
                foreach ($columns as $col) {
                    $columnNames[] = $col['name'];
                }

                // Determine index type
                $indexType = AdapterInterface::INDEX_TYPE_INDEX;
                if ($index['unique']) {
                    $indexType = AdapterInterface::INDEX_TYPE_UNIQUE;
                }
                if ($indexName === 'PRIMARY' || strpos($indexName, 'sqlite_autoindex') === 0) {
                    $indexType = AdapterInterface::INDEX_TYPE_PRIMARY;
                }

                $keyName = strtoupper($indexName);

                $ddl[$keyName] = [
                    'SCHEMA_NAME' => $schemaName,
                    'TABLE_NAME' => $tableName,
                    'KEY_NAME' => $indexName,
                    'COLUMNS_LIST' => $columnNames,
                    'INDEX_TYPE' => $indexType,
                    'INDEX_METHOD' => '', // SQLite doesn't have index methods like BTREE
                    'type' => $indexType,
                    'fields' => $columnNames,
                ];
            }

            $this->saveDdlCache($cacheKey, self::DDL_INDEX, $ddl);
        }

        return $ddl;
    }

    /**
     * Check if table exists
     *
     * @param string $tableName
     * @param string $schemaName
     * @return bool
     */
    public function isTableExists($tableName, $schemaName = null)
    {
        $table = $this->_getTableName($tableName, $schemaName);
        $sql = "SELECT name FROM sqlite_master WHERE type='table' AND name=?";
        $result = $this->fetchOne($sql, [$table]);

        return $result !== false;
    }

    /**
     * Retrieve tables list
     *
     * @param string|null $likeCondition
     * @return array
     */
    public function getTables($likeCondition = null)
    {
        $sql = "SELECT name FROM sqlite_master WHERE type='table'";

        if ($likeCondition !== null) {
            $sql .= " AND name LIKE " . $this->quote($likeCondition);
        }

        $sql .= " ORDER BY name";

        return $this->fetchCol($sql);
    }

    /**
     * Check support for straight join (SQLite doesn't optimize STRAIGHT_JOIN)
     *
     * @return bool
     */
    public function supportStraightJoin()
    {
        return false;
    }

    /**
     * SQLite doesn't support table checksums like MySQL
     *
     * @param array|string $tableNames
     * @param string $schemaName
     * @return array
     */
    public function getTablesChecksum($tableNames, $schemaName = null)
    {
        // Return empty array - checksums not supported in SQLite
        if (!is_array($tableNames)) {
            $tableNames = [$tableNames];
        }

        $checksums = [];
        foreach ($tableNames as $tableName) {
            $checksums[$tableName] = 0;
        }

        return $checksums;
    }

    /**
     * Create table from DDL object - SQLite version
     *
     * Generates native SQLite CREATE TABLE statements from Table DDL objects.
     * This avoids MySQL-specific syntax and provides better compatibility.
     *
     * @param Table $table
     * @throws \Zend_Db_Exception
     * @return \Zend_Db_Statement_Interface
     */
    public function createTable(Table $table)
    {
        $this->getSchemaListener()->createTable($table);

        $sqlFragment = array_merge(
            $this->_getSqliteColumnsDefinition($table),
            $this->_getSqliteIndexesDefinition($table),
            $this->_getSqliteForeignKeysDefinition($table)
        );

        $sql = sprintf(
            "CREATE TABLE IF NOT EXISTS %s (\n%s\n)",
            $this->quoteIdentifier($table->getName()),
            implode(",\n", $sqlFragment)
        );

        if ($this->getTransactionLevel() > 0) {
            $result = $this->createConnection()->query($sql);
        } else {
            $result = $this->query($sql);
        }

        $this->resetDdlCache($table->getName(), $table->getSchema());

        // Create non-primary key indexes separately
        $this->_createSqliteIndexes($table);

        return $result;
    }

    /**
     * Generate SQLite-compatible column definitions
     *
     * @param Table $table
     * @return array
     * @throws \Zend_Db_Exception
     */
    protected function _getSqliteColumnsDefinition(Table $table)
    {
        $definition = [];
        $primary = [];
        $columns = $table->getColumns();

        if (empty($columns)) {
            throw new \Zend_Db_Exception('Table columns are not defined');
        }

        foreach ($columns as $columnData) {
            $columnDefinition = $this->_getSqliteColumnDefinition($columnData);

            if ($columnData['PRIMARY']) {
                $primary[$columnData['COLUMN_NAME']] = $columnData['PRIMARY_POSITION'];
            }

            $definition[] = sprintf(
                '  %s %s',
                $this->quoteIdentifier($columnData['COLUMN_NAME']),
                $columnDefinition
            );
        }

        // Add composite primary key if needed
        if (count($primary) > 0) {
            asort($primary);
            $primaryColumns = array_keys($primary);

            // For single primary key with AUTOINCREMENT, it's already in column definition
            // For composite or non-auto primary keys, add PRIMARY KEY constraint
            if (count($primary) > 1 || !$this->_hasAutoIncrement($table, $primaryColumns[0])) {
                $definition[] = sprintf(
                    '  PRIMARY KEY (%s)',
                    implode(', ', array_map([$this, 'quoteIdentifier'], $primaryColumns))
                );
            }
        }

        return $definition;
    }

    /**
     * Check if column has autoincrement
     *
     * @param Table $table
     * @param string $columnName
     * @return bool
     */
    protected function _hasAutoIncrement(Table $table, $columnName)
    {
        $columns = $table->getColumns();
        foreach ($columns as $col) {
            if ($col['COLUMN_NAME'] === $columnName && $col['IDENTITY']) {
                return true;
            }
        }
        return false;
    }

    /**
     * Generate SQLite-compatible column definition
     *
     * @param array $columnData
     * @return string
     * @throws \Zend_Db_Exception
     */
    protected function _getSqliteColumnDefinition($columnData)
    {
        $type = $columnData['DATA_TYPE'];
        $sqliteType = $this->typeMapping[strtolower($type)] ?? 'TEXT';

        $definition = $sqliteType;

        // Add length/precision for certain types
        if (isset($columnData['LENGTH']) && $columnData['LENGTH'] && in_array($sqliteType, ['TEXT', 'BLOB'])) {
            // SQLite doesn't enforce length but we keep it for compatibility
            $definition .= '(' . $columnData['LENGTH'] . ')';
        } elseif (isset($columnData['PRECISION']) && isset($columnData['SCALE'])) {
            $definition .= '(' . $columnData['PRECISION'] . ',' . $columnData['SCALE'] . ')';
        }

        // Primary key with autoincrement (only for single column primary key)
        if ($columnData['PRIMARY'] && $columnData['IDENTITY']) {
            $definition .= ' PRIMARY KEY AUTOINCREMENT';
        }

        // NOT NULL constraint
        if ($columnData['NULLABLE'] === false) {
            $definition .= ' NOT NULL';
        }

        // Default value
        if (array_key_exists('DEFAULT', $columnData) && $columnData['DEFAULT'] !== null) {
            if ($columnData['DEFAULT'] instanceof Expression) {
                $definition .= ' DEFAULT ' . $columnData['DEFAULT'];
            } else {
                $defaultValue = $columnData['DEFAULT'];

                // Handle special timestamp defaults
                if ($columnData['DATA_TYPE'] === Table::TYPE_TIMESTAMP) {
                    if ($defaultValue === Table::TIMESTAMP_INIT) {
                        $definition .= ' DEFAULT CURRENT_TIMESTAMP';
                    } elseif ($defaultValue === Table::TIMESTAMP_INIT_UPDATE) {
                        // SQLite doesn't support ON UPDATE, just use CURRENT_TIMESTAMP
                        $definition .= ' DEFAULT CURRENT_TIMESTAMP';
                    } else {
                        $definition .= ' DEFAULT ' . $this->quote($defaultValue);
                    }
                } elseif (is_numeric($defaultValue)) {
                    $definition .= ' DEFAULT ' . $defaultValue;
                } else {
                    $definition .= ' DEFAULT ' . $this->quote($defaultValue);
                }
            }
        }

        return $definition;
    }

    /**
     * Generate SQLite-compatible index definitions (for CREATE TABLE)
     *
     * Only UNIQUE constraints go in CREATE TABLE for SQLite
     *
     * @param Table $table
     * @return array
     */
    protected function _getSqliteIndexesDefinition(Table $table)
    {
        $definition = [];
        $indexes = $table->getIndexes();

        if (!empty($indexes)) {
            foreach ($indexes as $indexData) {
                // Only add UNIQUE constraints here; regular indexes created separately
                if ($indexData['INDEX_TYPE'] === AdapterInterface::INDEX_TYPE_UNIQUE) {
                    $columns = $indexData['COLUMNS_LIST'];
                    $definition[] = sprintf(
                        '  UNIQUE (%s)',
                        implode(', ', array_map([$this, 'quoteIdentifier'], $columns))
                    );
                }
                // Skip FULLTEXT indexes - will handle in Stage 6
            }
        }

        return $definition;
    }

    /**
     * Create regular (non-unique) indexes separately
     *
     * SQLite requires indexes to be created with separate CREATE INDEX statements
     *
     * @param Table $table
     * @return void
     */
    protected function _createSqliteIndexes(Table $table)
    {
        $indexes = $table->getIndexes();

        if (!empty($indexes)) {
            foreach ($indexes as $indexData) {
                // Skip primary and unique (already in CREATE TABLE)
                if ($indexData['INDEX_TYPE'] === AdapterInterface::INDEX_TYPE_PRIMARY ||
                    $indexData['INDEX_TYPE'] === AdapterInterface::INDEX_TYPE_UNIQUE) {
                    continue;
                }

                // Skip fulltext for now (Stage 6)
                if ($indexData['INDEX_TYPE'] === AdapterInterface::INDEX_TYPE_FULLTEXT) {
                    continue;
                }

                $indexName = $indexData['INDEX_NAME'];
                $columns = $indexData['COLUMNS_LIST'];

                $sql = sprintf(
                    'CREATE INDEX IF NOT EXISTS %s ON %s (%s)',
                    $this->quoteIdentifier($indexName),
                    $this->quoteIdentifier($table->getName()),
                    implode(', ', array_map([$this, 'quoteIdentifier'], $columns))
                );

                $this->query($sql);
            }
        }
    }

    /**
     * Generate SQLite-compatible foreign key definitions
     *
     * @param Table $table
     * @return array
     */
    protected function _getSqliteForeignKeysDefinition(Table $table)
    {
        $definition = [];
        $foreignKeys = $table->getForeignKeys();

        if (!empty($foreignKeys)) {
            foreach ($foreignKeys as $fkData) {
                $definition[] = sprintf(
                    '  FOREIGN KEY (%s) REFERENCES %s (%s) ON DELETE %s',
                    $this->quoteIdentifier($fkData['COLUMN_NAME']),
                    $this->quoteIdentifier($fkData['REF_TABLE_NAME']),
                    $this->quoteIdentifier($fkData['REF_COLUMN_NAME']),
                    $fkData['ON_DELETE']
                );
            }
        }

        return $definition;
    }

    /**
     * Create temporary table - SQLite version
     *
     * @param Table $table
     * @throws \Zend_Db_Exception
     * @return \Zend_Db_Statement_Interface
     */
    public function createTemporaryTable(Table $table)
    {
        $sqlFragment = array_merge(
            $this->_getSqliteColumnsDefinition($table),
            $this->_getSqliteIndexesDefinition($table),
            $this->_getSqliteForeignKeysDefinition($table)
        );

        $sql = sprintf(
            "CREATE TEMPORARY TABLE %s (\n%s\n)",
            $this->quoteIdentifier($table->getName()),
            implode(",\n", $sqlFragment)
        );

        return $this->query($sql);
    }

    /**
     * Execute SQL query with automatic MySQL → SQLite translation
     *
     * @param string|\Magento\Framework\DB\Select $sql
     * @param array $bind
     * @return \Zend_Db_Statement_Interface
     */
    public function query($sql, $bind = [])
    {
        // Initialize rewriter if not already done
        if ($this->queryRewriter === null) {
            $this->_initializeQueryRewriter();
        }

        // Convert Select object to string
        $originalSql = $sql instanceof \Magento\Framework\DB\Select ? $sql->__toString() : $sql;

        // Translate query
        $translatedSql = $this->queryRewriter->translate($originalSql);

        // Log if enabled
        if ($this->queryLoggingEnabled && $originalSql !== $translatedSql) {
            $this->_logQueryTranslation($originalSql, $translatedSql);
        }

        // Execute translated query
        try {
            return parent::query($translatedSql, $bind);
        } catch (\Exception $e) {
            // Log failed queries
            if ($this->queryLoggingEnabled) {
                $this->_logQueryError($originalSql, $translatedSql, $e->getMessage());
            }
            throw $e;
        }
    }

    /**
     * Log query translation
     *
     * @param string $original
     * @param string $translated
     * @return void
     */
    protected function _logQueryTranslation(string $original, string $translated): void
    {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'type' => 'translation',
            'original' => $this->_truncateForLog($original),
            'translated' => $this->_truncateForLog($translated),
            'backtrace' => $this->_getBacktraceForLog(),
        ];

        $this->_writeQueryLog($logEntry, 'var/log/sqlite-queries.log');
    }

    /**
     * Log query error
     *
     * @param string $original
     * @param string $translated
     * @param string $error
     * @return void
     */
    protected function _logQueryError(string $original, string $translated, string $error): void
    {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'type' => 'error',
            'original' => $this->_truncateForLog($original),
            'translated' => $this->_truncateForLog($translated),
            'error' => $error,
            'backtrace' => $this->_getBacktraceForLog(),
        ];

        $this->_writeQueryLog($logEntry, 'var/log/sqlite-incompatible.log');
    }

    /**
     * Write log entry
     *
     * @param array $entry
     * @param string $file
     * @return void
     */
    protected function _writeQueryLog(array $entry, string $file): void
    {
        $logFile = defined('BP') ? BP . '/' . $file : $file;
        $logDir = dirname($logFile);

        if (!is_dir($logDir)) {
            @mkdir($logDir, 0770, true);
        }

        $json = json_encode($entry, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n";
        @file_put_contents($logFile, $json, FILE_APPEND | LOCK_EX);
    }

    /**
     * Truncate SQL for logging
     *
     * @param string $sql
     * @param int $maxLength
     * @return string
     */
    protected function _truncateForLog(string $sql, int $maxLength = 1000): string
    {
        if (strlen($sql) <= $maxLength) {
            return $sql;
        }

        return substr($sql, 0, $maxLength) . '... [truncated]';
    }

    /**
     * Get backtrace for logging
     *
     * @param int $limit
     * @return array
     */
    protected function _getBacktraceForLog(int $limit = 5): array
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, $limit + 5);

        // Skip internal adapter frames
        $filtered = [];
        foreach ($trace as $frame) {
            if (isset($frame['class']) &&
                (strpos($frame['class'], 'Magento\\Framework\\DB\\Adapter') === 0 ||
                 strpos($frame['class'], 'Zend_Db') === 0)) {
                continue;
            }

            $filtered[] = sprintf(
                '%s%s%s',
                $frame['class'] ?? '',
                $frame['type'] ?? '',
                $frame['function'] ?? ''
            );

            if (count($filtered) >= $limit) {
                break;
            }
        }

        return $filtered;
    }
}
