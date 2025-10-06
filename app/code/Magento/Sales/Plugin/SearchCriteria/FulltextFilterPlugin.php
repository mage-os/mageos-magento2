<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Sales\Plugin\SearchCriteria;

use Magento\Framework\Api\Filter;
use Magento\Framework\Data\Collection;
use Magento\Sales\Model\ResourceModel\Order\Grid\Collection as OrderGridCollection;
use Magento\Framework\View\Element\UiComponent\DataProvider\FulltextFilter as UiFulltextFilter;

class FulltextFilterPlugin
{

    /**
     * Use LIKE instead of MATCH AGAINST in sales order grid to bypass MySQL stopword limitations
     *
     * @param UiFulltextFilter $subject
     * @param \Closure $proceed
     * @param Collection $collection
     * @param Filter $filter
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundApply(
        UiFulltextFilter $subject,
        \Closure $proceed,
        Collection $collection,
        Filter $filter
    ): void {
        if ($collection instanceof OrderGridCollection) {
            $value = trim((string) $filter->getValue());
            if ($value === '') {
                return;
            }

            if (preg_match('/^\{+\s*(\d+)\s*\}+$/', $value, $m)) {
                $term = $m[1];
                $collection->addFieldToFilter('increment_id', ['eq' => $term]);
                return;
            }

            $value = trim($value, '{}');
            $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $value) . '%';

            $fields = ['increment_id', 'billing_name', 'shipping_name', 'customer_email'];
            $conditions = array_fill(0, count($fields), ['like' => $like]);

            $collection->addFieldToFilter($fields, $conditions);
            return;
        }
        $proceed($collection, $filter);
    }
}
