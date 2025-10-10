<?php
/**
 * Copyright © Mage-OS, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\DB\Sql;

/**
 * Query rewriter for translating MySQL queries to SQLite-compatible syntax
 *
 * This class handles runtime SQL translation for queries that don't go through
 * the declarative schema DDL layer. Primarily used for legacy install scripts,
 * raw queries, and third-party extensions.
 *
 * @api
 */
class SqliteQueryRewriter
{
    /**
     * Keywords that indicate MySQL-specific syntax
     *
     * @var array
     */
    private $mysqlKeywords = [
        'ENGINE',
        'AUTO_INCREMENT',
        'UNSIGNED',
        'CHARACTER SET',
        'COLLATE',
        'COMMENT',
        'ON UPDATE CURRENT_TIMESTAMP',
        'INSERT IGNORE',
        'ON DUPLICATE KEY UPDATE',
        'STRAIGHT_JOIN',
        'SQL_CALC_FOUND_ROWS'
    ];

    /**
     * Check if query needs translation
     *
     * @param string $sql
     * @return bool
     */
    public function needsTranslation(string $sql): bool
    {
        $sqlUpper = strtoupper($sql);

        foreach ($this->mysqlKeywords as $keyword) {
            if (strpos($sqlUpper, $keyword) !== false) {
                return true;
            }
        }

        // Check for MySQL functions
        $mysqlFunctions = [
            'IF(',
            'IFNULL(',
            'CONCAT_WS(',
            'GROUP_CONCAT(',
            'DATE_FORMAT(',
        ];

        foreach ($mysqlFunctions as $func) {
            if (stripos($sql, $func) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Translate MySQL query to SQLite
     *
     * @param string $sql
     * @return string
     */
    public function translate(string $sql): string
    {
        // Quick check - if no translation needed, return as-is
        if (!$this->needsTranslation($sql)) {
            return $sql;
        }

        $sql = $this->translateDdl($sql);
        $sql = $this->translateDml($sql);
        $sql = $this->translateFunctions($sql);
        $sql = $this->translateInsertIgnore($sql);
        $sql = $this->translateOnDuplicateKeyUpdate($sql);

        return $sql;
    }

    /**
     * Translate DDL statements (CREATE TABLE, ALTER TABLE, etc.)
     *
     * @param string $sql
     * @return string
     */
    protected function translateDdl(string $sql): string
    {
        // Remove MySQL-specific table options
        $sql = preg_replace('/ENGINE\s*=\s*\w+/i', '', $sql);
        $sql = preg_replace('/AUTO_INCREMENT\s*=\s*\d+/i', '', $sql);
        $sql = preg_replace('/DEFAULT\s+CHARSET\s*=\s*\w+/i', '', $sql);
        $sql = preg_replace('/COLLATE\s*=\s*\w+/i', '', $sql);
        $sql = preg_replace('/CHARACTER\s+SET\s+\w+/i', '', $sql);

        // Remove column comments
        $sql = preg_replace('/COMMENT\s+\'[^\']*\'/i', '', $sql);
        $sql = preg_replace('/COMMENT\s+"[^"]*"/i', '', $sql);

        // Replace AUTO_INCREMENT with AUTOINCREMENT
        $sql = preg_replace('/\bAUTO_INCREMENT\b/i', 'AUTOINCREMENT', $sql);

        // Remove UNSIGNED
        $sql = preg_replace('/\bUNSIGNED\b/i', '', $sql);

        // Handle ON UPDATE CURRENT_TIMESTAMP (SQLite doesn't support)
        $sql = preg_replace('/ON\s+UPDATE\s+CURRENT_TIMESTAMP/i', '', $sql);

        // Remove SQL_CALC_FOUND_ROWS
        $sql = preg_replace('/SQL_CALC_FOUND_ROWS/i', '', $sql);

        // STRAIGHT_JOIN → JOIN
        $sql = preg_replace('/STRAIGHT_JOIN/i', 'JOIN', $sql);

        return trim($sql);
    }

    /**
     * Translate DML statements (INSERT, UPDATE, DELETE)
     *
     * @param string $sql
     * @return string
     */
    protected function translateDml(string $sql): string
    {
        // REPLACE INTO → INSERT OR REPLACE INTO
        if (preg_match('/^\s*REPLACE\s+INTO/i', $sql)) {
            $sql = preg_replace('/^\s*REPLACE\s+INTO/i', 'INSERT OR REPLACE INTO', $sql);
        }

        return $sql;
    }

    /**
     * Translate MySQL functions to SQLite equivalents
     *
     * @param string $sql
     * @return string
     */
    protected function translateFunctions(string $sql): string
    {
        // IF(condition, true, false) → CASE WHEN condition THEN true ELSE false END
        $sql = $this->translateIfFunction($sql);

        // CONCAT_WS(separator, str1, str2, ...) → (str1 || separator || str2 || ...)
        $sql = $this->translateConcatWs($sql);

        // GROUP_CONCAT(expr SEPARATOR ',') → GROUP_CONCAT(expr, ',')
        $sql = $this->translateGroupConcat($sql);

        // IFNULL is same in both, no change needed

        return $sql;
    }

    /**
     * Translate IF() function
     *
     * IF(condition, true_val, false_val) → CASE WHEN condition THEN true_val ELSE false_val END
     *
     * @param string $sql
     * @return string
     */
    protected function translateIfFunction(string $sql): string
    {
        // This is complex due to nested parentheses - simple regex won't work perfectly
        // For now, handle simple cases
        $pattern = '/\bIF\s*\(\s*([^,]+?)\s*,\s*([^,]+?)\s*,\s*([^)]+?)\s*\)/i';

        $sql = preg_replace_callback($pattern, function ($matches) {
            return sprintf(
                'CASE WHEN %s THEN %s ELSE %s END',
                $matches[1],
                $matches[2],
                $matches[3]
            );
        }, $sql);

        return $sql;
    }

    /**
     * Translate CONCAT_WS() function
     *
     * CONCAT_WS(separator, str1, str2) → (str1 || separator || str2)
     *
     * @param string $sql
     * @return string
     */
    protected function translateConcatWs(string $sql): string
    {
        // Match CONCAT_WS with its arguments
        $pattern = '/CONCAT_WS\s*\(\s*([^,]+)\s*,\s*(.*?)\s*\)/is';

        $sql = preg_replace_callback($pattern, function ($matches) {
            $separator = trim($matches[1]);
            $args = explode(',', $matches[2]);

            // Trim each argument
            $args = array_map('trim', $args);

            if (count($args) < 1) {
                return $matches[0]; // Return original if malformed
            }

            // Build concatenation: (arg1 || sep || arg2 || sep || arg3)
            $parts = [];
            foreach ($args as $i => $arg) {
                if ($i > 0) {
                    $parts[] = $separator;
                }
                $parts[] = $arg;
            }

            return '(' . implode(' || ', $parts) . ')';
        }, $sql);

        return $sql;
    }

    /**
     * Translate GROUP_CONCAT() function
     *
     * GROUP_CONCAT(expr SEPARATOR ',') → GROUP_CONCAT(expr, ',')
     *
     * @param string $sql
     * @return string
     */
    protected function translateGroupConcat(string $sql): string
    {
        // GROUP_CONCAT(column SEPARATOR 'sep') → GROUP_CONCAT(column, 'sep')
        $pattern = '/GROUP_CONCAT\s*\(\s*(.*?)\s+SEPARATOR\s+([^)]+)\s*\)/is';

        $sql = preg_replace_callback($pattern, function ($matches) {
            return sprintf('GROUP_CONCAT(%s, %s)', $matches[1], $matches[2]);
        }, $sql);

        return $sql;
    }

    /**
     * Translate INSERT IGNORE
     *
     * INSERT IGNORE INTO → INSERT OR IGNORE INTO
     *
     * @param string $sql
     * @return string
     */
    protected function translateInsertIgnore(string $sql): string
    {
        if (preg_match('/^\s*INSERT\s+IGNORE\s+INTO/i', $sql)) {
            $sql = preg_replace('/^\s*INSERT\s+IGNORE\s+INTO/i', 'INSERT OR IGNORE INTO', $sql);
        }

        return $sql;
    }

    /**
     * Translate INSERT ... ON DUPLICATE KEY UPDATE
     *
     * This is complex and requires knowing the primary key.
     * For now, convert to INSERT OR REPLACE which has different semantics
     * but works for many cases.
     *
     * TODO: In future, parse and convert to proper UPSERT syntax
     *
     * @param string $sql
     * @return string
     */
    protected function translateOnDuplicateKeyUpdate(string $sql): string
    {
        // Check if query contains ON DUPLICATE KEY UPDATE
        if (stripos($sql, 'ON DUPLICATE KEY UPDATE') === false) {
            return $sql;
        }

        // For now, use INSERT OR REPLACE strategy
        // This has limitations (deletes and reinserts, changes rowid)
        // but works for simple cases
        $sql = preg_replace('/ON\s+DUPLICATE\s+KEY\s+UPDATE\s+.*$/is', '', $sql);

        // Change INSERT INTO to INSERT OR REPLACE INTO
        $sql = preg_replace('/^\s*INSERT\s+INTO/i', 'INSERT OR REPLACE INTO', $sql);

        return trim($sql);
    }

    /**
     * Get list of MySQL-specific patterns this rewriter handles
     *
     * @return array
     */
    public function getSupportedPatterns(): array
    {
        return [
            'ENGINE=InnoDB' => 'Removed',
            'AUTO_INCREMENT' => 'AUTOINCREMENT',
            'UNSIGNED' => 'Removed',
            'COMMENT' => 'Removed',
            'IF(condition, t, f)' => 'CASE WHEN condition THEN t ELSE f END',
            'CONCAT_WS(sep, a, b)' => '(a || sep || b)',
            'GROUP_CONCAT(x SEPARATOR \',\')' => 'GROUP_CONCAT(x, \',\')',
            'INSERT IGNORE' => 'INSERT OR IGNORE',
            'ON DUPLICATE KEY UPDATE' => 'INSERT OR REPLACE (simplified)',
            'REPLACE INTO' => 'INSERT OR REPLACE INTO',
        ];
    }
}
