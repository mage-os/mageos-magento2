<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Wishlist\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Wishlist\Model\ResourceModel\Item\Collection;

class WishlistItemPermissionsCollectionProcessor
{
    /**
     * @var array
     */
    private array $validProductIds = [];

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
    }

    /**
     * Remove wishlist items from collection if category permissions in effect
     *
     * @param Collection $collection
     * @return Collection
     * @throws \Exception
     */
    public function execute(Collection $collection): Collection
    {
        $items = $collection->getItems();
        if (empty($items)) {
            return $collection;
        }
        $productIds = $collection->getColumnValues('product_id');
        $cacheKey = sha1(implode("-", $productIds));
        if (!isset($this->validProductIds[$cacheKey])) {
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter('entity_id', $productIds, 'in')
                ->create();
            $productCollection = $this->productRepository->getList($searchCriteria);
            $products = array_combine(
                array_map(fn ($p) => $p->getId(), $productCollection->getItems()),
                $productCollection->getItems()
            );

            $validItems = [];
            foreach ($items as $item) {
                if (!isset($products[$item->getProductId()]) || $products[$item->getProductId()]->getIsHidden()) {
                    continue;
                }
                $validItems[] = (int)$item->getProductId();
            }
            $this->validProductIds[$cacheKey] = $validItems;
        }

        if (!empty($this->validProductIds[$cacheKey])) {
            $collection->addFilter(
                'main_table.product_id',
                'main_table.product_id IN(' . implode(",", $this->validProductIds[$cacheKey]) . ')',
                'string'
            );
            $collection->clear();
        }

        return $collection;
    }
}
