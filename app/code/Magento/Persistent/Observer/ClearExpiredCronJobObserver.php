<?php
/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Persistent\Observer;

use Magento\Cron\Model\Schedule;
use Magento\Persistent\Model\DeleteExpiredQuoteFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Persistent\Model\SessionFactory;
use Magento\Store\Model\ResourceModel\Website\CollectionFactory;

class ClearExpiredCronJobObserver
{
    /**
     * Website collection factory
     *
     * @var CollectionFactory
     */
    private CollectionFactory $websiteCollectionFactory;

    /**
     * Session factory
     *
     * @var SessionFactory
     */
    private SessionFactory $sessionFactory;

    /**
     * Delete expired quote factory
     *
     * @var DeleteExpiredQuoteFactory
     */
    private DeleteExpiredQuoteFactory $deleteExpiredQuoteFactory;



    /**
     * @param CollectionFactory $websiteCollectionFactory
     * @param SessionFactory $sessionFactory
     * @param DeleteExpiredQuoteFactory|null $deleteExpiredQuoteFactory
     */
    public function __construct(
        CollectionFactory $websiteCollectionFactory,
        SessionFactory $sessionFactory,
        DeleteExpiredQuoteFactory $deleteExpiredQuoteFactory = null
    ) {
        $this->websiteCollectionFactory = $websiteCollectionFactory;
        $this->sessionFactory = $sessionFactory;
        $this->deleteExpiredQuoteFactory = $deleteExpiredQuoteFactory ?:
            ObjectManager::getInstance()->get(DeleteExpiredQuoteFactory::class);
    }

    /**
     * Clear expired persistent sessions
     *
     * @param Schedule $schedule
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(Schedule $schedule)
    {
        $websiteIds = $this->websiteCollectionFactory->create()->getAllIds();
        if (!is_array($websiteIds)) {
            return $this;
        }

        foreach ($websiteIds as $websiteId) {
            $this->sessionFactory->create()->deleteExpired($websiteId);
            $this->deleteExpiredQuoteFactory->create()->deleteExpiredQuote($websiteId);
        }

        return $this;
    }
}
