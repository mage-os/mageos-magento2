<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\SalesSequence\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\SalesSequence\Model\Sequence\DeleteByStore;

/**
 * Removes sequences of store views deleted together with their website or store group.
 *
 * Such store views are removed by a foreign key cascade, so "store_delete" is not dispatched for them.
 */
class ScopeSequenceRemovalObserver implements ObserverInterface
{
    /**
     * @param DeleteByStore $deleteByStore
     */
    public function __construct(
        private readonly DeleteByStore $deleteByStore
    ) {
    }

    /**
     * Collect store ids before the delete and drop their sequences after commit
     *
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        $scope = $observer->getEvent()->getData('data_object');
        // Read from a new collection: getStoreIds() may return stale cached membership
        $storeIds = $scope->getStoreCollection()->getAllIds();
        if (!$storeIds) {
            return;
        }

        $scope->getResource()->addCommitCallback(
            function () use ($storeIds) {
                foreach ($storeIds as $storeId) {
                    $this->deleteByStore->execute((int)$storeId);
                }
            }
        );
    }
}
