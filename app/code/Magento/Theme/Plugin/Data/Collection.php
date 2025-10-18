<?php
/**
 * Copyright 2020 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Theme\Plugin\Data;

use Magento\Framework\Data\Collection as DataCollection;

/**
 * Plugin to return last page if current page greater then collection size.
 */
class Collection
{
    /**
     * Return last page if current page greater then last page.
     *
     * @param DataCollection $subject
     * @param int $result
     * @return int
     */
    public function afterGetCurPage(DataCollection $subject, int $result): int
    {
        if ($result > 1 && $result > $subject->getLastPageNumber()) {
            $result = 1;
        }

        return $result;
    }
}
