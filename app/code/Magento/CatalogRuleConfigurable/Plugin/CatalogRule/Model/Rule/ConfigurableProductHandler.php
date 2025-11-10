<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogRuleConfigurable\Plugin\CatalogRule\Model\Rule;

use Magento\CatalogRule\Model\Rule;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable as ConfigurableProductsResourceModel;
use Magento\Framework\App\ResourceConnection;

/**
 * Add configurable sub products to catalog rule indexer on full reindex
 */
class ConfigurableProductHandler
{
    /**
     * @var ConfigurableProductsResourceModel
     */
    private ConfigurableProductsResourceModel $configurable;

    /**
     * @var ResourceConnection
     */
    private ResourceConnection $resourceConnection;

    /**
     * @var array|null
     */
    private static ?array $allConfigurableProductIds = null;

    /**
     * @var array
     */
    private static array $childrenProducts = [];

    /**
     * @param ConfigurableProductsResourceModel $configurable
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ConfigurableProductsResourceModel $configurable,
        ResourceConnection $resourceConnection
    ) {
        $this->configurable = $configurable;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Match configurable child products if configurable product matches the condition
     *
     * @param Rule $rule
     * @param \Closure $proceed
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGetMatchingProductIds(
        Rule $rule,
        \Closure $proceed
    ): array {
        $productsFilter = $rule->getProductsFilter() ? (array) $rule->getProductsFilter() : [];

        $this->updateProductsFilter($rule, $productsFilter);

        $productIds = $proceed();

        if (self::$allConfigurableProductIds === null) {
            self::$allConfigurableProductIds = $this->loadAllConfigurableProductIds();
        }

        return $this->processConfigurableProducts($productIds, $productsFilter);
    }

    /**
     * Update products filter to include parent IDs and remove duplicate children
     *
     * @param Rule $rule
     * @param array $productsFilter
     * @return void
     */
    private function updateProductsFilter(
        Rule $rule,
        array $productsFilter
    ): void {
        if (!$productsFilter) {
            return;
        }

        $parentIds = $this->configurable->getParentIdsByChild($productsFilter);
        if (!$parentIds) {
            return;
        }

        $childrenToRemove = $this->getChildrenToRemove($productsFilter, $parentIds);
        $filteredProducts = array_diff($productsFilter, $childrenToRemove);

        $rule->setProductsFilter(
            array_unique(
                array_merge(
                    $filteredProducts,
                    $parentIds
                )
            )
        );
    }

    /**
     * Get children to remove from filter to avoid redundancy
     *
     * @param array $productsFilter
     * @param array $parentIds
     * @return array
     */
    private function getChildrenToRemove(array $productsFilter, array $parentIds): array
    {
        $childrenToRemove = [];

        foreach ($parentIds as $parentId) {
            if (in_array($parentId, $productsFilter)) {
                continue;
            }

            if (!isset(self::$childrenProducts[$parentId])) {
                self::$childrenProducts[$parentId] = $this->configurable->getChildrenIds($parentId)[0];
            }
            $children = self::$childrenProducts[$parentId];

            $childrenInFilter = array_intersect($productsFilter, $children);
            if (count($childrenInFilter) > 1) {
                $childrenInFilterArray = array_values($childrenInFilter);
                $childrenCount = count($childrenInFilterArray);
                for ($i = 1; $i < $childrenCount; $i++) {
                    $childrenToRemove[] = $childrenInFilterArray[$i];
                }
            }
        }

        return $childrenToRemove;
    }

    /**
     * Process configurable products and apply parent validation to children
     *
     * @param array $productIds
     * @param array $productsFilter
     * @return array
     */
    private function processConfigurableProducts(array $productIds, array $productsFilter): array
    {
        foreach (array_keys($productIds) as $productId) {
            if (isset(self::$allConfigurableProductIds[$productId])) {
                if (!isset(self::$childrenProducts[$productId])) {
                    self::$childrenProducts[$productId] = $this->configurable->getChildrenIds($productId)[0];
                }

                $parentValidationResult = array_filter($productIds[$productId]);
                $processAllChildren = !$productsFilter || in_array($productId, $productsFilter);

                foreach (self::$childrenProducts[$productId] as $childProductId) {
                    if ($processAllChildren || in_array($childProductId, $productsFilter)) {
                        $childValidationResult = isset($productIds[$childProductId])
                            ? array_filter($productIds[$childProductId])
                            : [];
                        $productIds[$childProductId] = $parentValidationResult + $childValidationResult;
                    }
                }
                unset($productIds[$productId]);
            }
        }

        return $productIds;
    }

    /**
     * Load all configurable product IDs at once
     *
     * @return array
     */
    private function loadAllConfigurableProductIds(): array
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from(
                ['e' => $this->resourceConnection->getTableName('catalog_product_entity')],
                ['entity_id']
            )
            ->where('e.type_id = ?', 'configurable');

        $result = $connection->fetchCol($select);

        return array_flip($result);
    }
}
