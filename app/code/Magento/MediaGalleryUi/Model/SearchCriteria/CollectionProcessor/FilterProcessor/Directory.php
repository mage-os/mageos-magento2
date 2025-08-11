<?php
/**
 * Copyright 2019 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Model\SearchCriteria\CollectionProcessor\FilterProcessor;

use Magento\Framework\Api\Filter;
use Magento\Framework\Api\SearchCriteria\CollectionProcessor\FilterProcessor\CustomFilterInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Psr\Log\LoggerInterface;

class Directory implements CustomFilterInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function apply(Filter $filter, AbstractDb $collection): bool
    {
        $value = $filter->getValue() !== null ? str_replace('%', '', $filter->getValue()) : '';

        try {
            /**
             * Use BINARY comparison for case-sensitive path filtering.
             * Without BINARY, MySQL's default case-insensitive comparison would match
             * directories like "Testing" and "testing" as the same, leading to incorrect
             * file visibility across directories with different case variations.
             * The regex '^{path}/[^\/]*$' ensures we only match files directly in the
             * specified directory, not in subdirectories.
             */
            $collection->getSelect()->where('BINARY path REGEXP ? ', '^' . $value . '/[^\/]*$');
        } catch (\Exception $e) {
            // Log the error for debugging but continue with case-insensitive fallback
            // Note: This fallback means directory filtering will not be case-sensitive
            $this->logger->error(
                'MediaGallery Directory Filter: BINARY REGEXP not supported, ' .
                'using case-insensitive fallback: ' . $e->getMessage()
            );
            $collection->getSelect()->where('path REGEXP ? ', '^' . $value . '/[^\/]*$');
        }

        return true;
    }
}
