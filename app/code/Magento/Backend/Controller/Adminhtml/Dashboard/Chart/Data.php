<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Backend\Controller\Adminhtml\Dashboard\Chart;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Controller\Adminhtml\Dashboard;
use Magento\Backend\Model\Dashboard\Chart;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Order count and revenue series for the combined dashboard chart
 */
class Data extends Dashboard implements HttpPostActionInterface
{
    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param Chart $chart
     * @param StoreManagerInterface $storeManager
     * @param ResolverInterface $localeResolver
     */
    public function __construct(
        Context $context,
        private readonly JsonFactory $resultJsonFactory,
        private readonly Chart $chart,
        private readonly StoreManagerInterface $storeManager,
        private readonly ResolverInterface $localeResolver
    ) {
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    public function execute(): Json
    {
        $store = $this->_request->getParam('store');
        $website = $this->_request->getParam('website');
        $group = $this->_request->getParam('group');

        $series = $this->chart->getSeriesByPeriod(
            (string)$this->_request->getParam('period'),
            $store,
            $website,
            $group
        );

        return $this->resultJsonFactory->create()->setData([
            'period' => $series['period'],
            'series' => [
                'orders' => ['label' => __('Orders'), 'data' => $series[Chart::SERIES_QUANTITY]],
                'amounts' => ['label' => __('Revenue'), 'data' => $series[Chart::SERIES_REVENUE]],
            ],
            'currency' => $this->getBaseCurrencyCode($store, $website, $group),
            'locale' => str_replace('_', '-', (string)$this->localeResolver->getLocale()),
        ]);
    }

    /**
     * Base currency of the selected scope, matching what the totals bars display
     *
     * @param string|null $store
     * @param string|null $website
     * @param string|null $group
     * @return string
     */
    private function getBaseCurrencyCode(?string $store, ?string $website, ?string $group): string
    {
        if ($store) {
            return (string)$this->storeManager->getStore($store)->getBaseCurrencyCode();
        }
        if ($website) {
            return (string)$this->storeManager->getWebsite($website)->getBaseCurrencyCode();
        }
        if ($group) {
            return (string)$this->storeManager->getGroup($group)->getWebsite()->getBaseCurrencyCode();
        }
        return (string)$this->storeManager->getStore(Store::DEFAULT_STORE_ID)->getBaseCurrencyCode();
    }
}
