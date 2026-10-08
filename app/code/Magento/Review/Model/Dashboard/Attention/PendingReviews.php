<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Review\Model\Dashboard\Attention;

use Magento\Backend\Model\Dashboard\Attention\CappedCounter;
use Magento\Backend\Model\Dashboard\Attention\ItemInterface;
use Magento\Framework\Phrase;
use Magento\Review\Model\ResourceModel\Review\CollectionFactory;
use Magento\Review\Model\Review;

/**
 * Product reviews awaiting moderation
 */
class PendingReviews implements ItemInterface
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
        return 'pending_reviews';
    }

    /**
     * @inheritdoc
     */
    public function getLabel(): Phrase
    {
        return __('Pending Reviews');
    }

    /**
     * @inheritdoc
     */
    public function getUrlPath(): string
    {
        return 'review/product/pending';
    }

    /**
     * @inheritdoc
     */
    public function getAclResource(): string
    {
        return 'Magento_Review::pending';
    }

    /**
     * @inheritdoc
     */
    public function getSortOrder(): int
    {
        return 20;
    }

    /**
     * @inheritdoc
     */
    public function getCount(array $storeIds): ?int
    {
        $collection = $this->collectionFactory->create();
        $collection->addStatusFilter(Review::STATUS_PENDING);
        if ($storeIds) {
            $collection->addStoreFilter($storeIds);
        }
        return $this->cappedCounter->count($collection->getSelect(), CappedCounter::CAP, 'main_table.review_id');
    }
}
