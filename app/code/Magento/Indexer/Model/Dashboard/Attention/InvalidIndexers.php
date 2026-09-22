<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Indexer\Model\Dashboard\Attention;

use Magento\Backend\Model\Dashboard\Attention\CappedCounter;
use Magento\Backend\Model\Dashboard\Attention\ItemInterface;
use Magento\Framework\Indexer\StateInterface;
use Magento\Framework\Phrase;
use Magento\Indexer\Model\ResourceModel\Indexer\State\CollectionFactory;

/**
 * Indexers whose state is "invalid" (Reindex Required)
 */
class InvalidIndexers implements ItemInterface
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
        return 'invalid_indexers';
    }

    /**
     * @inheritdoc
     */
    public function getLabel(): Phrase
    {
        return __('Indexers Requiring Reindex');
    }

    /**
     * @inheritdoc
     */
    public function getUrlPath(): string
    {
        return 'indexer/indexer/list';
    }

    /**
     * @inheritdoc
     */
    public function getAclResource(): string
    {
        return 'Magento_Indexer::index';
    }

    /**
     * @inheritdoc
     */
    public function getSortOrder(): int
    {
        return 100;
    }

    /**
     * @inheritdoc
     *
     * Indexer state is global, so the store scope is ignored.
     */
    public function getCount(array $storeIds): ?int
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('status', StateInterface::STATUS_INVALID);
        return $this->cappedCounter->count($collection->getSelect());
    }
}
