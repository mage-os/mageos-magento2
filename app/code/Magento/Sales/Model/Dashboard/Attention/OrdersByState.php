<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Sales\Model\Dashboard\Attention;

use Magento\Backend\Model\Dashboard\Attention\CappedCounter;
use Magento\Backend\Model\Dashboard\Attention\ItemInterface;
use Magento\Framework\Phrase;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

/**
 * Dashboard attention tile counting orders in one state, scoped to the selected store views
 *
 * The state column is indexed, so the capped count stays cheap regardless of order volume.
 */
class OrdersByState implements ItemInterface
{
    /**
     * @param CollectionFactory $collectionFactory
     * @param CappedCounter $cappedCounter
     * @param string $state Order state to count
     * @param string $code Tile code
     * @param string $label Tile label (translated when rendered)
     * @param int $sortOrder Position among tiles
     * @param bool $hideWhenEmpty Hide the tile instead of showing 0
     */
    public function __construct(
        private readonly CollectionFactory $collectionFactory,
        private readonly CappedCounter $cappedCounter,
        private readonly string $state,
        private readonly string $code,
        private readonly string $label,
        private readonly int $sortOrder = 10,
        private readonly bool $hideWhenEmpty = false
    ) {
    }

    /**
     * @inheritdoc
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @inheritdoc
     */
    public function getLabel(): Phrase
    {
        return __($this->label);
    }

    /**
     * @inheritdoc
     */
    public function getUrlPath(): string
    {
        return 'sales/order/index';
    }

    /**
     * @inheritdoc
     */
    public function getAclResource(): string
    {
        return 'Magento_Sales::sales_order';
    }

    /**
     * @inheritdoc
     */
    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    /**
     * @inheritdoc
     */
    public function getCount(array $storeIds): ?int
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('state', $this->state);
        if ($storeIds) {
            $collection->addFieldToFilter('store_id', ['in' => $storeIds]);
        }
        $count = $this->cappedCounter->count($collection->getSelect());

        return $count === 0 && $this->hideWhenEmpty ? null : $count;
    }
}
