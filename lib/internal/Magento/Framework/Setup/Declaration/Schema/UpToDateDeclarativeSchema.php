<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */

declare(strict_types=1);

namespace Magento\Framework\Setup\Declaration\Schema;

use Magento\Framework\Setup\Declaration\Schema\Diff\SchemaDiff;
use Magento\Framework\Setup\UpToDateValidatorInterface;

/**
 * Allows to validate if schema is up to date or not
 */
class UpToDateDeclarativeSchema implements UpToDateValidatorInterface
{
    /**
     * @var SchemaConfigInterface
     */
    private $schemaConfig;

    /**
     * @var SchemaDiff
     */
    private $schemaDiff;

    /**
     * @var array|null
     */
    private $cachedDiff = null;

    /**
     * UpToDateSchema constructor.
     * @param SchemaConfigInterface $schemaConfig
     * @param SchemaDiff $schemaDiff
     */
    public function __construct(
        SchemaConfigInterface $schemaConfig,
        SchemaDiff $schemaDiff
    ) {
        $this->schemaConfig = $schemaConfig;
        $this->schemaDiff = $schemaDiff;
    }

    /**
     * @return string
     */
    public function getNotUpToDateMessage() : string
    {
        return 'Declarative Schema is not up to date';
    }

    /**
     * @return bool
     */
    public function isUpToDate() : bool
    {
        return empty($this->calculateDiff());
    }

    /**
     * Get detailed information about schema differences
     *
     * @return array
     */
    public function getDetails() : array
    {
        $diffData = $this->calculateDiff();
        $summary = $this->buildSummary($diffData);
        $summary['timestamp'] = date('Y-m-d H:i:s');

        return $summary;
    }

    /**
     * Calculate schema differences and cache the result
     *
     * @return array
     */
    private function calculateDiff() : array
    {
        if ($this->cachedDiff === null) {
            $declarativeSchema = $this->schemaConfig->getDeclarationConfig();
            $dbSchema = $this->schemaConfig->getDbConfig();
            $diff = $this->schemaDiff->diff($declarativeSchema, $dbSchema);
            $this->cachedDiff = $diff->getAll() ?? [];
        }

        return $this->cachedDiff;
    }

    /**
     * Build a summary of schema differences
     *
     * @param array $diffData
     * @return array
     */
    private function buildSummary(array $diffData) : array
    {
        $summary = [
            'timestamp' => date('Y-m-d H:i:s'),
            'total_differences' => 0,
            'by_change_type' => [],
            'affected_tables' => [],
            'changes' => []
        ];

        try {
            foreach ($diffData as $key => $operations) {
                if (!is_array($operations)) {
                    continue;
                }

                foreach ($operations as $operationType => $changes) {
                    if (!isset($summary['by_change_type'][$operationType])) {
                        $summary['by_change_type'][$operationType] = 0;
                    }

                    $changeCount = is_array($changes) ? count($changes) : 1;
                    $summary['by_change_type'][$operationType] += $changeCount;
                    $summary['total_differences'] += $changeCount;

                    if (is_array($changes)) {
                        foreach ($changes as $changeIndex => $change) {
                            $changeInfo = [
                                'operation' => $operationType,
                                'index' => $changeIndex
                            ];

                            $tableName = $this->safeGetTableName($change);
                            if ($tableName) {
                                $changeInfo['table'] = $tableName;

                                if (!isset($summary['affected_tables'][$tableName])) {
                                    $summary['affected_tables'][$tableName] = [];
                                }

                                if (!isset($summary['affected_tables'][$tableName][$operationType])) {
                                    $summary['affected_tables'][$tableName][$operationType] = 0;
                                }

                                $summary['affected_tables'][$tableName][$operationType]++;
                            }

                            // Add any other safely extractable information
                            if ($change instanceof ElementHistory) {
                                $changeInfo = $this->processElementHistory($change, $changeInfo);
                            } elseif (is_array($change) && isset($change['name'])) {
                                $changeInfo['name'] = $change['name'];
                            } elseif (is_object($change) && method_exists($change, 'getName')) {
                                $changeInfo['name'] = $change->getName();

                                // Special handling for index elements
                                if (method_exists($change, 'getType') && ($change->getType() === 'index' || $change->getType() === 'constraint')) {
                                    $changeInfo['type'] = $change->getType();

                                    // Try to get the index columns if available
                                    if (method_exists($change, 'getColumns')) {
                                        $columns = $change->getColumns();
                                        if (is_array($columns)) {
                                            $changeInfo['columns'] = [];
                                            foreach ($columns as $column) {
                                                if (is_object($column) && method_exists($column, 'getName')) {
                                                    $changeInfo['columns'][] = $column->getName();
                                                } elseif (is_string($column)) {
                                                    $changeInfo['columns'][] = $column;
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            $summary['changes'][] = $changeInfo;
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $summary['error'] = $e->getMessage();
        }

        return $summary;
    }

    /**
     * Safely get table name from any change object
     *
     * @param mixed $change
     * @return string|null
     */
    private function safeGetTableName($change): ?string
    {
        try {
            // Option 1: ElementHistory with getNew() or getOld()
            if ($change instanceof ElementHistory) {
                $element = $change->getNew() ?: $change->getOld();
                if ($element) {
                    // If element is a table
                    if (method_exists($element, 'getType') && $element->getType() === 'table' &&
                        method_exists($element, 'getName')) {
                        return $element->getName();
                    }

                    // If element belongs to a table
                    if (method_exists($element, 'getTable')) {
                        $table = $element->getTable();
                        if ($table && method_exists($table, 'getName')) {
                            return $table->getName();
                        }
                    }
                }
            }

            // Option 2: Array with 'table' key
            if (is_array($change) && isset($change['table'])) {
                return $change['table'];
            }

            // Option 3: Object with getTable() method
            if (is_object($change) && method_exists($change, 'getTable')) {
                $table = $change->getTable();
                if (is_string($table)) {
                    return $table;
                } elseif (is_object($table) && method_exists($table, 'getName')) {
                    return $table->getName();
                }
            }

            // Option 4: Object is itself a table
            if (is_object($change) && method_exists($change, 'getType') &&
                $change->getType() === 'table' && method_exists($change, 'getName')) {
                return $change->getName();
            }
        } catch (\Exception $e) {
            // Silently fail and return null
        }

        return null;
    }

    /**
     * Process ElementHistory object to extract useful information
     *
     * @param ElementHistory $change
     * @param array $changeInfo
     * @return array
     */
    private function processElementHistory($change, array $changeInfo): array
    {
        try {
            $newElement = $change->getNew();
            $oldElement = $change->getOld();

            // Get element name
            if ($newElement && method_exists($newElement, 'getName')) {
                $changeInfo['name'] = $newElement->getName();
            } elseif ($oldElement && method_exists($oldElement, 'getName')) {
                $changeInfo['name'] = $oldElement->getName();
            }

            // Get element type
            if ($newElement && method_exists($newElement, 'getType')) {
                $changeInfo['type'] = $newElement->getType();
            } elseif ($oldElement && method_exists($oldElement, 'getType')) {
                $changeInfo['type'] = $oldElement->getType();
            }

            // For modify operations, add basic diff information
            if (($changeInfo['operation'] === 'modify_column' || $changeInfo['operation'] === 'modify_table') && $oldElement && $newElement) {
                // Check for comment differences (most common issue)
                if (method_exists($oldElement, 'getComment') && method_exists($newElement, 'getComment')) {
                    $oldComment = $oldElement->getComment();
                    $newComment = $newElement->getComment();

                    if ($oldComment !== $newComment) {
                        $changeInfo['comment_changed'] = true;
                        $changeInfo['old_comment'] = $oldComment;
                        $changeInfo['new_comment'] = $newComment;
                    }
                }
            }
        } catch (\Exception $e) {
            // Silently fail and return original changeInfo
        }

        return $changeInfo;
    }
}
