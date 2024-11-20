<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Theme\Ui\Component\Design\Config;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Data Provider for 'design_config_form' and 'design_config_listing' components
 *
 * @api
 * @since 100.1.0
 */
class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    /**
     * @var StoreManagerInterface
     * @since 100.1.0
     */
    protected $storeManager;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param StoreManagerInterface $storeManager
     * @param ResourceConnection $resourceConnection
     * @param array $meta
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        StoreManagerInterface $storeManager,
        ResourceConnection $resourceConnection,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
        $this->storeManager = $storeManager;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Get data
     *
     * @return array
     * @since 100.1.0
     */
    public function getData()
    {
        if ($this->storeManager->isSingleStoreMode()) {
            $websites = $this->storeManager->getWebsites();
            $singleStoreWebsite = array_shift($websites);

            $this->addFilter(
                $this->filterBuilder->setField('store_website_id')
                    ->setValue($singleStoreWebsite->getId())
                    ->create()
            );
            $this->addFilter(
                $this->filterBuilder->setField('store_group_id')
                    ->setConditionType('null')
                    ->create()
            );
        }
        $data = parent::getData();
        foreach ($data['items'] as & $item) {
            $item += ['default' => __('Global')];

            $scope = ($item['store_id']) ? 'stores' : (($item['store_website_id']) ? 'websites' : 'default');
            $scopeId = (int) $item['store_website_id'] ?? 0;
            $themeId = (int) $item['theme_theme_id'] ?? 0;
            $usingDefaultTheme = $this->isUsingDefaultTheme($scopeId, $themeId, $scope);
            $item += ['short_description' => $usingDefaultTheme ? __('Using Default Theme') : ''];
        }

        return $data;
    }

    /**
     * Check if theme used is default theme
     *
     * @param int $scopeId
     * @param int $themeId
     * @param string $scope
     * @return bool
     */
    private function isUsingDefaultTheme(int $scopeId, int $themeId, string $scope): bool
    {
        $connection = $this->resourceConnection->getConnection();
        $configId = $connection->fetchOne(
            $connection->select()->from(
                $connection->getTableName('core_config_data'),
                ['config_id']
            )->where('value = ?', $themeId)
            ->where('scope_id = ?', $scopeId)
            ->where('path = ?', 'design/theme/theme_id')
            ->where('scope = ?', $scope)
        );
        return !$configId;
    }
}
