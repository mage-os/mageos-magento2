<?php
/**
 * Copyright 2013 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Block\Dashboard;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\Dashboard\Config;
use Magento\Backend\Model\Dashboard\Period;
use Magento\Backend\Model\Dashboard\StatisticsCache;
use Magento\Framework\Module\Manager;
use Magento\Reports\Model\ResourceModel\Order\Collection;
use Magento\Reports\Model\ResourceModel\Order\CollectionFactory;
use Magento\Store\Model\Store;
use Magento\Framework\App\ObjectManager;

/**
 * Adminhtml dashboard totals bar
 * @api
 * @since 100.0.2
 */
class Totals extends Bar
{
    /**
     * @var string
     */
    protected $_template = 'Magento_Backend::dashboard/totalbar.phtml';

    /**
     * @var Manager
     */
    protected $_moduleManager;

    /**
     * @var Period
     */
    private $period;

    /**
     * @var StatisticsCache
     */
    private $statisticsCache;

    /**
     * @var Config
     */
    private $dashboardConfig;

    /**
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     * @param Manager $moduleManager
     * @param array $data
     * @param Period|null $period
     * @param StatisticsCache|null $statisticsCache
     * @param Config|null $dashboardConfig
     */
    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        Manager $moduleManager,
        array $data = [],
        ?Period $period = null,
        ?StatisticsCache $statisticsCache = null,
        ?Config $dashboardConfig = null
    ) {
        $this->_moduleManager = $moduleManager;
        $this->period = $period ?? ObjectManager::getInstance()->get(Period::class);
        $this->statisticsCache = $statisticsCache ?? ObjectManager::getInstance()->get(StatisticsCache::class);
        $this->dashboardConfig = $dashboardConfig ?? ObjectManager::getInstance()->get(Config::class);
        parent::__construct($context, $collectionFactory, $data);
    }

    /**
     * @inheritDoc
     * @return $this|void
     */
    protected function _prepareLayout()
    {
        if (!$this->_moduleManager->isEnabled('Magento_Reports')) {
            return $this;
        }
        $firstPeriod = array_key_first($this->period->getDatePeriods());
        $period = (string)$this->getRequest()->getParam('period', $firstPeriod);

        $totals = $this->statisticsCache->get(
            'totals',
            [
                'period' => $period,
                'store' => (string)$this->getRequest()->getParam('store'),
                'website' => (string)$this->getRequest()->getParam('website'),
                'group' => (string)$this->getRequest()->getParam('group'),
            ],
            $this->dashboardConfig->getTotalsCacheLifetime(),
            fn (): array => $this->loadTotals($period)
        );

        $this->addTotal(__('Revenue'), $totals['revenue']);
        $this->addTotal(__('Tax'), $totals['tax']);
        $this->addTotal(__('Shipping'), $totals['shipping']);
        $this->addTotal(__('Quantity'), $totals['quantity'], true);

        return $this;
    }

    /**
     * Query the period totals from the database
     *
     * @param string $period
     * @return array{revenue: float, tax: float, shipping: float, quantity: int}
     */
    private function loadTotals(string $period): array
    {
        $isFilter = $this->getRequest()->getParam(
            'store'
        ) || $this->getRequest()->getParam(
            'website'
        ) || $this->getRequest()->getParam(
            'group'
        );

        /* @var $collection Collection */
        $collection = $this->_collectionFactory->create()->addCreateAtPeriodFilter(
            $period
        )->calculateTotals(
            $isFilter
        );

        if ($this->getRequest()->getParam('store')) {
            $collection->addFieldToFilter('store_id', $this->getRequest()->getParam('store'));
        } else {
            if ($this->getRequest()->getParam('website')) {
                $storeIds = $this->_storeManager->getWebsite($this->getRequest()->getParam('website'))->getStoreIds();
                $collection->addFieldToFilter('store_id', ['in' => $storeIds]);
            } else {
                if ($this->getRequest()->getParam('group')) {
                    $storeIds = $this->_storeManager->getGroup($this->getRequest()->getParam('group'))->getStoreIds();
                    $collection->addFieldToFilter('store_id', ['in' => $storeIds]);
                } elseif (!$collection->isLive()) {
                    $collection->addFieldToFilter(
                        'store_id',
                        ['eq' => $this->_storeManager->getStore(Store::ADMIN_CODE)->getId()]
                    );
                }
            }
        }

        $collection->load();

        $totals = $collection->getFirstItem();

        return [
            'revenue' => (float)$totals->getRevenue(),
            'tax' => (float)$totals->getTax(),
            'shipping' => (float)$totals->getShipping(),
            'quantity' => (int)$totals->getQuantity(),
        ];
    }
}
