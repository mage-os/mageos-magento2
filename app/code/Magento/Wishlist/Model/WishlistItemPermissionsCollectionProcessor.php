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
     * @param ProductRepositoryInterface $productRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
    }

    /**
     * @param Collection $collection
     * @return Collection
     * @throws \Exception
     */
    public function execute(Collection $collection): Collection
    {
        $items = $collection->getItems();
        $productIds = array_map(static fn ($item) => $item->getProductId(), $items);
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('entity_id', $productIds, 'in')
            ->create();
        $productCollection = $this->productRepository->getList($searchCriteria);
        $products = array_combine(
            array_map(fn ($p) => $p->getId(), $productCollection->getItems()),
            $productCollection->getItems()
        );

        $validItems = [];
        $collection->removeAllItems();
        foreach ($items as $item) {
            if (!isset($products[$item->getProductId()]) || $products[$item->getProductId()]->getIsHidden()) {
                continue;
            }
            $validItems[] = $item->getProductId();
            $collection->addItem($item);
        }
        if (!empty($validItems)) {
            $collection->addFieldToFilter('main_table.product_id', ['in' => $validItems]);
        }

        return $collection;
    }
}
