<?php
/**
 * Copyright 2013 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Block\Dashboard;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\Dashboard\Config;
use Magento\Backend\Model\Dashboard\StatisticsCache;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Module\Manager;
use Magento\Reports\Model\ResourceModel\Order\CollectionFactory;

/**
 * Adminhtml dashboard sales statistics bar
 *
 * The lifetime figures scan the whole order table when aggregated statistics are disabled, so they are
 * cached (see admin/dashboard/lifetime_cache_lifetime). On a cache miss the block renders placeholders
 * and loads the figures asynchronously so the dashboard page itself is never blocked by that query.
 *
 * @api
 * @since 100.0.2
 */
class Sales extends Bar
{
    public const CACHE_KEY = 'sales';

    public const DEFERRED_ELEMENT_ID = 'dashboard_sales_deferred';

    /**
     * @var string
     */
    protected $_template = 'Magento_Backend::dashboard/salebar.phtml';

    /**
     * @var Manager
     */
    protected $_moduleManager;

    /**
     * @var StatisticsCache
     */
    private $statisticsCache;

    /**
     * @var Config
     */
    private $dashboardConfig;

    /**
     * @var bool
     */
    private $isDeferred = false;

    /**
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     * @param Manager $moduleManager
     * @param array $data
     * @param StatisticsCache|null $statisticsCache
     * @param Config|null $dashboardConfig
     */
    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        Manager $moduleManager,
        array $data = [],
        ?StatisticsCache $statisticsCache = null,
        ?Config $dashboardConfig = null
    ) {
        $this->_moduleManager = $moduleManager;
        $this->statisticsCache = $statisticsCache ?? ObjectManager::getInstance()->get(StatisticsCache::class);
        $this->dashboardConfig = $dashboardConfig ?? ObjectManager::getInstance()->get(Config::class);
        parent::__construct($context, $collectionFactory, $data);
    }

    /**
     * Prepare layout.
     *
     * @return $this|void
     */
    protected function _prepareLayout()
    {
        if (!$this->_moduleManager->isEnabled('Magento_Reports')) {
            return $this;
        }

        $lifetime = $this->dashboardConfig->getLifetimeCacheLifetime();
        $scope = $this->getScopeParams();

        if ($lifetime > 0 && !$this->getData('render_sync')) {
            $figures = $this->statisticsCache->load(self::CACHE_KEY, $scope);
            if ($figures === null) {
                $this->isDeferred = true;
                $this->addTotal(__('Lifetime Sales'), $this->getPlaceholderHtml(), true);
                $this->addTotal(__('Average Order'), $this->getPlaceholderHtml(), true);
                return $this;
            }
        } else {
            $figures = $this->statisticsCache->get(
                self::CACHE_KEY,
                $scope,
                $lifetime,
                fn (): array => $this->loadFigures()
            );
        }

        $this->addTotal(__('Lifetime Sales'), $figures['lifetime']);
        $this->addTotal(__('Average Order'), $figures['average']);

        return $this;
    }

    /**
     * Whether the figures are being loaded asynchronously
     *
     * @return bool
     */
    public function isDeferred(): bool
    {
        return $this->isDeferred;
    }

    /**
     * URL the deferred figures are fetched from
     *
     * @return string
     */
    public function getDeferredUrl(): string
    {
        return $this->getUrl('adminhtml/dashboard/ajaxBlock', ['_current' => true, 'block' => self::CACHE_KEY]);
    }

    /**
     * Query the lifetime figures from the database
     *
     * @return array{lifetime: float, average: float}
     */
    private function loadFigures(): array
    {
        $isFilter = $this->getRequest()->getParam(
            'store'
        ) || $this->getRequest()->getParam(
            'website'
        ) || $this->getRequest()->getParam(
            'group'
        );

        $collection = $this->_collectionFactory->create()->calculateSales($isFilter);

        if ($this->getRequest()->getParam('store')) {
            $collection->addFieldToFilter('store_id', $this->getRequest()->getParam('store'));
        } elseif ($this->getRequest()->getParam('website')) {
            $storeIds = $this->_storeManager->getWebsite($this->getRequest()->getParam('website'))->getStoreIds();
            $collection->addFieldToFilter('store_id', ['in' => $storeIds]);
        } elseif ($this->getRequest()->getParam('group')) {
            $storeIds = $this->_storeManager->getGroup($this->getRequest()->getParam('group'))->getStoreIds();
            $collection->addFieldToFilter('store_id', ['in' => $storeIds]);
        }

        $collection->load();
        $sales = $collection->getFirstItem();

        return [
            'lifetime' => (float)$sales->getLifetime(),
            'average' => (float)$sales->getAverage(),
        ];
    }

    /**
     * Request parameters that influence the figures
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

    /**
     * Placeholder shown while the figures load
     *
     * @return string
     */
    private function getPlaceholderHtml(): string
    {
        return '<span class="dashboard-sales-loading" aria-busy="true" aria-live="polite">'
            . $this->escapeHtml(__('Loading...')) . '</span>';
    }

    /**
     * Wrap deferred output so the asynchronous response can replace it regardless of the template used
     *
     * @param string $html
     * @return string
     */
    protected function _afterToHtml($html)
    {
        $html = parent::_afterToHtml($html);
        if (!$this->isDeferred) {
            return $html;
        }
        $init = json_encode(
            [
                '#' . self::DEFERRED_ELEMENT_ID => [
                    'Magento_Backend/js/dashboard/sales' => ['updateUrl' => $this->getDeferredUrl()],
                ],
            ],
            JSON_UNESCAPED_SLASHES
        );
        return '<div id="' . self::DEFERRED_ELEMENT_ID . '">' . $html
            . '<script type="text/x-magento-init">' . $init . '</script></div>';
    }
}
