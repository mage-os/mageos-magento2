<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Model\SearchCriteria\CollectionProcessor\FilterProcessor;

use Magento\Framework\Api\Filter;
use Magento\Framework\Api\SearchCriteria\CollectionProcessor\FilterProcessor\CustomFilterInterface;
use Magento\Framework\Data\Collection\AbstractDb;

class Directory implements CustomFilterInterface
{
    /**
     * @inheritDoc
     */
    public function apply(Filter $filter, AbstractDb $collection): bool
    {
        $value = $filter->getValue() !== null ? str_replace('%', '', $filter->getValue()) : '';

        /**
         * Use BINARY comparison for case-sensitive path filtering.
         * Without BINARY, MySQL's default case-insensitive comparison would match
         * directories like "Testing" and "testing" as the same, leading to incorrect
         * file visibility across directories with different case variations.
         * The regex '^{path}/[^\/]*$' ensures we only match files directly in the
         * specified directory, not in subdirectories.
         */
        $collection->getSelect()->where('BINARY path REGEXP ? ', '^' . $value . '/[^\/]*$');

        return true;
    }
}
