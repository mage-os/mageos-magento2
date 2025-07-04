<?php
/**
 * Copyright 2025 Adobe
 * All rights reserved.
 */
declare(strict_types=1);

namespace Magento\Webapi\Model\Plugin\Framework\Api;

use Magento\Framework\Api\Filter;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Phrase;

/**
 * This plugin validates the search criteria field, value and condition type.
 */
class FilterPlugin
{
    /**
     * Validate the search criteria field.
     *
     * @param Filter $filter
     * @param string $result
     * @return string
     */
    public function afterGetField(Filter $filter, $result)
    {
        if (empty($result) && !empty($filter->getValue())) {
            throw new InputException(
                new Phrase(
                    'Invalid search filter: %1 cannot be empty.',
                    ["field"]
                )
            );
        }
        return $result;
    }

    /**
     * Validate the search criteria value.
     *
     * @param Filter $filter
     * @param string $result
     * @return string
     */
    public function afterGetValue(Filter $filter, $result)
    {
        if (empty($result) && !empty($filter->getField())) {
            throw new InputException(
                new Phrase(
                    'Invalid search filter: %1 cannot be empty.',
                    ["value"]
                )
            );
        }
        return $result;
    }

    /**
     * Validate the search criteria condition type.
     *
     * @param Filter $filter
     * @param string $result
     * @return string
     */
    public function afterGetConditionType(Filter $filter, $result)
    {
        if (empty($filter->getField()) || empty($filter->getValue())) {
            throw new InputException(
                new Phrase(
                    'Invalid search filter: %1 and %2 cannot be empty.',
                    ["field", "value"]
                )
            );
        }
        return $result;
    }
}
