<?php
/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Persistent\Observer;

use Magento\Cron\Model\Schedule;
use Magento\Persistent\Model\ResourceModel\DeleteExpiredQuote;
use Magento\Persistent\Model\SessionFactory;
use Magento\Store\Model\ResourceModel\Website\CollectionFactory;

class ClearExpiredCronJobObserver
{
    /**
     * A property for website collection factory
     *
     * @var CollectionFactory
     */
    protected CollectionFactory $_websiteCollectionFactory;

    /**
     * A property for session factory
     *
     * @var SessionFactory
     */
    protected SessionFactory $_sessionFactory;

    /**
     * A property for delete expired quote factory
     *
     * @var DeleteExpiredQuote
     */
    private DeleteExpiredQuote $deleteExpiredQuote;

    /**
     * @param CollectionFactory $websiteCollectionFactory
     * @param SessionFactory $sessionFactory
     * @param DeleteExpiredQuote $deleteExpiredQuote
     */
    public function __construct(
        CollectionFactory $websiteCollectionFactory,
        SessionFactory $sessionFactory,
        DeleteExpiredQuote $deleteExpiredQuote
    ) {
        $this->_websiteCollectionFactory = $websiteCollectionFactory;
        $this->_sessionFactory = $sessionFactory;
        $this->deleteExpiredQuote = $deleteExpiredQuote;
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
        $websiteIds = $this->_websiteCollectionFactory->create()->getAllIds();
        if (!is_array($websiteIds)) {
            return $this;
        }

        foreach ($websiteIds as $websiteId) {
            $this->_sessionFactory->create()->deleteExpired($websiteId);
            $this->deleteExpiredQuote->deleteExpiredQuote((int) $websiteId);
        }

        return $this;
    }
}
