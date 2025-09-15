<?php
/**
 * Copyright 2021 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogGraphQl\DataProvider\Product\LayeredNavigation\Builder\Aggregations\Category;

use Magento\Catalog\Api\CategoryListInterface;
use Magento\Catalog\Model\Config\LayerCategoryConfig;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\ObjectManager\ResetAfterRequestInterface;
use Magento\Framework\Search\Response\Aggregation;
use Magento\Framework\Search\Response\AggregationFactory;
use Magento\Framework\Search\Response\BucketFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\Search\AggregationInterface;

/**
 * Class to include only direct subcategories of category in aggregation
 */
class IncludeDirectChildrenOnly implements ResetAfterRequestInterface
{
    /**
     * @var string
     */
    private const CATEGORY_BUCKET = 'category_bucket';

    /**
     * @var string
     */
    private const BUCKETS_NAME = 'buckets';

    /**
     * @var AggregationFactory
     */
    private $aggregationFactory;

    /**
     * @var BucketFactory
     */
    private $bucketFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CategoryListInterface
     */
    private $categoryList;

    /**
     * @var array
     */
    private $filter = [];

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var LayerCategoryConfig|null
     */
    private $layerCategoryConfig;

    /**
     * @param AggregationFactory $aggregationFactory
     * @param BucketFactory $bucketFactory
     * @param StoreManagerInterface $storeManager
     * @param CategoryListInterface $categoryList
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param LayerCategoryConfig|null $layerCategoryConfig
     */
    public function __construct(
        AggregationFactory $aggregationFactory,
        BucketFactory $bucketFactory,
        StoreManagerInterface $storeManager,
        CategoryListInterface $categoryList,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ?LayerCategoryConfig $layerCategoryConfig = null
    ) {
        $this->aggregationFactory = $aggregationFactory;
        $this->bucketFactory = $bucketFactory;
        $this->storeManager = $storeManager;
        $this->categoryList = $categoryList;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->layerCategoryConfig = $layerCategoryConfig ?? ObjectManager::getInstance()
                ->get(LayerCategoryConfig::class);
    }

    /**
     * Filter category aggregation to include only direct subcategories of requested category
     *
     * @param AggregationInterface $aggregation
     * @param int|null $storeId
     * @return Aggregation
     */
    public function filter(AggregationInterface $aggregation, ?int $storeId): Aggregation
    {
        if (!$this->layerCategoryConfig->isCategoryFilterVisibleInLayerNavigation()) {
            $buckets = $aggregation->getBuckets();
            unset($buckets[self::CATEGORY_BUCKET]);
        } else {
            $categoryIdsRequested = $this->filter['category'] ?? null;
            if ($categoryIdsRequested === null) {
                return $aggregation;
            }
            $buckets = $aggregation->getBuckets();
            $categoryBucket = $buckets[self::CATEGORY_BUCKET] ?? null;
            if ($categoryBucket === null || empty($categoryBucket->getValues())) {
                return $aggregation;
            }
            $categoryIdsRequested = is_array($categoryIdsRequested) ? $categoryIdsRequested : [$categoryIdsRequested];
            $bucketValuesFiltered = $this->filterBucketValues(
                $categoryBucket->getValues(),
                $categoryIdsRequested,
                $storeId
            );
            $categoryBucketResolved = $this->bucketFactory->create(
                [
                    'name' => self::CATEGORY_BUCKET,
                    'values' => $bucketValuesFiltered
                ]
            );
            $buckets[self::CATEGORY_BUCKET] = $categoryBucketResolved;
        }

        return $this->aggregationFactory->create([self::BUCKETS_NAME => $buckets]);
    }

    /**
     * Set filter for categories aggregation
     *
     * @param array $filter
     */
    public function setFilter(array $filter): void
    {
        $this->filter = $filter;
    }

    /**
     * Filter bucket values to include only direct subcategories of requested category
     *
     * @param array $categoryBucketValues
     * @param array $categoryIdsRequested
     * @param int|null $storeId
     * @return array
     */
    private function filterBucketValues(
        array $categoryBucketValues,
        array $categoryIdsRequested,
        ?int  $storeId
    ): array {
        $categoryChildIds = [];
        $storeId = $storeId !== null ? $storeId : $this->storeManager->getStore()->getId();
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('entity_id', $categoryIdsRequested, 'in')
            ->create();
        $categoriesRequested = $this->categoryList->getList($searchCriteria);
        foreach ($categoriesRequested->getItems() as $category) {
            $category->setStoreId($storeId);
            $childrenIds = $category->getChildren();
            if ($childrenIds) {
                $categoryChildIds[] = explode(',', $childrenIds);
            }
        }
        $categoryChildIds = array_merge([], ...$categoryChildIds);
        foreach ($categoryBucketValues as $key => $bucketValue) {
            $categoryId = (int)$bucketValue->getValue();
            if (!in_array($categoryId, $categoryChildIds)) {
                unset($categoryBucketValues[$key]);
            }
        }
        return array_values($categoryBucketValues);
    }

    /**
     * @inheritDoc
     */
    public function _resetState(): void
    {
        $this->filter = [];
    }
}
