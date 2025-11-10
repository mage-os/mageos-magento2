<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogRuleConfigurable\Plugin\CatalogRule\Model\Rule;

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
     * @param \Magento\CatalogRule\Model\Rule $rule
     * @param \Closure $proceed
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function aroundGetMatchingProductIds(
        \Magento\CatalogRule\Model\Rule $rule,
        \Closure $proceed
    ): array {
        $productsFilter = $rule->getProductsFilter() ? (array) $rule->getProductsFilter() : [];
        if ($productsFilter) {
            $parentIds = $this->configurable->getParentIdsByChild($productsFilter);
            if ($parentIds) {
                $rule->setProductsFilter(
                    array_unique(
                        array_merge(
                            $productsFilter,
                            $parentIds
                        )
                    )
                );
            }
        }

        $productIds = $proceed();

        if (self::$allConfigurableProductIds === null) {
            self::$allConfigurableProductIds = $this->loadAllConfigurableProductIds();
        }

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
