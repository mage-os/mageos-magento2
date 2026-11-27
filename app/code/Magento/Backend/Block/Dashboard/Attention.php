<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Backend\Block\Dashboard;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\Dashboard\Attention\CappedCounter;
use Magento\Backend\Model\Dashboard\Attention\ItemInterface;
use Magento\Backend\Model\Dashboard\Attention\Pool;
use Magento\Backend\Model\Dashboard\Config;
use Magento\Backend\Model\Dashboard\StatisticsCache;
use Psr\Log\LoggerInterface;

/**
 * "Needs attention" tiles: pending orders, pending reviews, invalid indexers and anything else registered on the pool
 */
class Attention extends Template
{
    private const CACHE_KEY = 'attention';

    /**
     * @var string
     */
    protected $_template = 'Magento_Backend::dashboard/attention.phtml';

    /**
     * @var array|null
     */
    private ?array $items = null;

    /**
     * @param Context $context
     * @param Pool $pool
     * @param StatisticsCache $statisticsCache
     * @param Config $dashboardConfig
     * @param CappedCounter $cappedCounter
     * @param LoggerInterface $logger
     * @param array $data
     */
    public function __construct(
        Context $context,
        private readonly Pool $pool,
        private readonly StatisticsCache $statisticsCache,
        private readonly Config $dashboardConfig,
        private readonly CappedCounter $cappedCounter,
        private readonly LoggerInterface $logger,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Tiles the current admin may see, each as [code, label, url, count, display, active]
     *
     * @return array
     */
    public function getItems(): array
    {
        if ($this->items !== null) {
            return $this->items;
        }

        $visible = array_filter(
            $this->pool->getItems(),
            fn (ItemInterface $item) => $this->_authorization->isAllowed($item->getAclResource())
        );
        if (!$visible) {
            return $this->items = [];
        }

        $counts = $this->statisticsCache->get(
            self::CACHE_KEY,
            $this->getScopeParams(),
            $this->dashboardConfig->getTotalsCacheLifetime(),
            fn (): array => $this->loadCounts()
        );

        $this->items = [];
        foreach ($visible as $item) {
            $count = $counts[$item->getCode()] ?? null;
            if ($count === null) {
                continue;
            }
            $this->items[] = [
                'code' => $item->getCode(),
                'label' => $item->getLabel(),
                'url' => $this->getUrl($item->getUrlPath()),
                'count' => $count,
                'display' => $this->cappedCounter->format($count),
                'active' => $count > 0,
            ];
        }

        return $this->items;
    }

    /**
     * Count every registered item (ACL is applied afterwards so the cache is shared between admins)
     *
     * @return array<string, int|null>
     */
    private function loadCounts(): array
    {
        $storeIds = $this->getStoreIds();
        $counts = [];
        foreach ($this->pool->getItems() as $item) {
            try {
                $counts[$item->getCode()] = $item->getCount($storeIds);
            } catch (\Throwable $e) {
                $this->logger->error(
                    sprintf('Dashboard attention item "%s" failed: %s', $item->getCode(), $e->getMessage()),
                    ['exception' => $e]
                );
                $counts[$item->getCode()] = null;
            }
        }
        return $counts;
    }

    /**
     * Store views selected in the dashboard scope switcher; empty means all
     *
     * @return int[]
     */
    private function getStoreIds(): array
    {
        $request = $this->getRequest();
        if ($request->getParam('store')) {
            return [(int)$request->getParam('store')];
        }
        if ($request->getParam('website')) {
            return array_map('intval', $this->_storeManager->getWebsite($request->getParam('website'))->getStoreIds());
        }
        if ($request->getParam('group')) {
            return array_map('intval', $this->_storeManager->getGroup($request->getParam('group'))->getStoreIds());
        }
        return [];
    }

    /**
     * Request parameters that influence the counts
     *
     * @return array
     */
    private function getScopeParams(): array
    {
        return [
            'store' => (string)$this->getRequest()->getParam('store'),
            'website' => (string)$this->getRequest()->getParam('website'),
            'group' => (string)$this->getRequest()->getParam('group'),
        ];
    }
}
