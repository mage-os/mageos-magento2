<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Framework\Data\Collection;

class CollectionTestHelper extends Collection
{
    private string $entityTypeCode = '';

    /**
     * Constructor that skips parent dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get entity type code
     *
     * @return string
     */
    public function getEntityTypeCode(): string
    {
        return $this->entityTypeCode;
    }

    /**
     * Set entity type code
     *
     * @param string $entityTypeCode
     * @return $this
     */
    public function setEntityTypeCode(string $entityTypeCode): self
    {
        $this->entityTypeCode = $entityTypeCode;
        return $this;
    }
}
