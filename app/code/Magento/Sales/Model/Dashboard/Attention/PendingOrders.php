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
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

/**
 * Orders in the "pending" state, i.e. placed but not yet processed
 */
class PendingOrders implements ItemInterface
{
    /**
     * @param CollectionFactory $collectionFactory
     * @param CappedCounter $cappedCounter
     */
    public function __construct(
        private readonly CollectionFactory $collectionFactory,
        private readonly CappedCounter $cappedCounter
    ) {
    }

    /**
     * @inheritdoc
     */
    public function getCode(): string
    {
        return 'pending_orders';
    }

    /**
     * @inheritdoc
     */
    public function getLabel(): Phrase
    {
        return __('Pending Orders');
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
        return 10;
    }

    /**
     * @inheritdoc
     */
    public function getCount(array $storeIds): ?int
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('state', Order::STATE_NEW);
        if ($storeIds) {
            $collection->addFieldToFilter('store_id', ['in' => $storeIds]);
        }
        return $this->cappedCounter->count($collection->getSelect());
    }
}
