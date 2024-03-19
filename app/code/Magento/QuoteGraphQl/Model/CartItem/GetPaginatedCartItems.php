<?php
/**
 * Copyright 2023 Adobe
 * All Rights Reserved.
 *
 * NOTICE: All information contained herein is, and remains
 * the property of Adobe and its suppliers, if any. The intellectual
 * and technical concepts contained herein are proprietary to Adobe
 * and its suppliers and are protected by all applicable intellectual
 * property laws, including trade secret and copyright laws.
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained from
 * Adobe.
 */
declare(strict_types=1);

namespace Magento\QuoteGraphQl\Model\CartItem;

use Magento\Quote\Model\Quote;
use Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory as ItemCollectionFactory;

/**
 * Fetch Cart items and product models corresponding to a cart
 */
class GetPaginatedCartItems
{
    /**
     * @param ItemCollectionFactory $itemCollectionFactory
     */
    public function __construct(
        private readonly ItemCollectionFactory $itemCollectionFactory
    ) {
    }

    /**
     * Get visible cart items and product data for cart items
     *
     * @param Quote $cart
     * @param int $pageSize
     * @param int $offset
     * @param string $orderBy
     * @param string $order
     * @return array
     */
    public function execute(Quote $cart, int $pageSize, int $offset, string $orderBy, string $order): array
    {
        if (!$cart->getId()) {
            return [
                'total' => 0,
                'items' => []
            ];
        }
        /** @var \Magento\Framework\Data\Collection $itemCollection */
        $itemCollection =  $this->itemCollectionFactory->create()
            ->addFieldToFilter('parent_item_id', ['null' => true])
            ->addFieldToFilter('quote_id', $cart->getId())
            ->setOrder($orderBy, $order)
            ->setCurPage($offset)
            ->setPageSize($pageSize);

        $items = [];
        $itemDeletedCount = 0;
        /** @var \Magento\Quote\Model\Quote\Item $item */
        foreach ($itemCollection->getItems() as $item) {
            if (!$item->isDeleted()) {
                $items[] = $item;
            } else {
                $itemDeletedCount++;
            }
        }

        return [
            'total' => $itemCollection->getSize() - $itemDeletedCount,
            'items' => $items
        ];
    }
}
