<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Backend\Model\Dashboard\Attention;

use Magento\Framework\Phrase;

/**
 * A "needs attention" tile on the admin dashboard
 *
 * Implementations are registered on {@see Pool} via di.xml. Counting must stay cheap at any
 * table size: use {@see CappedCounter} rather than COUNT(*).
 *
 * @api
 */
interface ItemInterface
{
    /**
     * Stable identifier, also used as the cache key
     *
     * @return string
     */
    public function getCode(): string;

    /**
     * Tile label
     *
     * @return Phrase
     */
    public function getLabel(): Phrase;

    /**
     * Admin route the tile links to, e.g. "sales/order/index"
     *
     * @return string
     */
    public function getUrlPath(): string;

    /**
     * ACL resource the admin must hold for the tile to render
     *
     * @return string
     */
    public function getAclResource(): string;

    /**
     * Ordering among tiles, ascending
     *
     * @return int
     */
    public function getSortOrder(): int;

    /**
     * Number of items needing attention, or null to hide the tile
     *
     * Values above {@see CappedCounter::CAP} are rendered as "1000+".
     *
     * @param int[] $storeIds Store views in scope; empty means all
     * @return int|null
     */
    public function getCount(array $storeIds): ?int;
}
