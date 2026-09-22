<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Backend\Model\Dashboard\Attention;

use Magento\Framework\DB\Select;

/**
 * Counts matching rows up to a cap without a COUNT(*)
 *
 * SELECT 1 ... LIMIT cap+1 is bounded by the cap regardless of table size, whereas COUNT(*) on InnoDB
 * walks every matching index entry. Results above the cap are returned as cap + 1.
 */
class CappedCounter
{
    public const CAP = 1000;

    /**
     * Count rows matched by $select, up to $cap + 1
     *
     * @param Select $select
     * @param int $cap
     * @param string|null $distinctColumn Count distinct values of this column instead of raw rows (for joins)
     * @return int
     */
    public function count(Select $select, int $cap = self::CAP, ?string $distinctColumn = null): int
    {
        $select->reset(Select::COLUMNS)
            ->reset(Select::ORDER)
            ->reset(Select::LIMIT_COUNT)
            ->reset(Select::LIMIT_OFFSET)
            ->limit($cap + 1);
        if ($distinctColumn !== null) {
            $select->distinct()->columns($distinctColumn);
        } else {
            $select->columns(new \Zend_Db_Expr('1'));
        }

        return count($select->getConnection()->fetchCol($select));
    }

    /**
     * Human-readable form of a count produced by {@see count()}
     *
     * @param int $count
     * @param int $cap
     * @return string
     */
    public function format(int $count, int $cap = self::CAP): string
    {
        return $count > $cap ? $cap . '+' : (string)$count;
    }
}
