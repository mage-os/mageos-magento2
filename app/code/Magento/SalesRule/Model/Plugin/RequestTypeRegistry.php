<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
namespace Magento\SalesRule\Model\Plugin;

/**
 * Tracks whether the request is http post or mutation
 *
 * This class is used to optimize performance by only loading salesrule product attributes
 * when they're actually needed during totals collection, rather than on every quote load.
 */
class RequestTypeRegistry
{
    /**
     * @var bool
     */
    private $isGetRequestOrQuery= false;

    /**
     * Set Request state
     *
     * @param bool $state
     * @return void
     */
    public function setIsGetRequestOrQuery(bool $state): void
    {
        $this->isGetRequestOrQuery = $state;
    }

    /**
     * Check if request is a get or query
     *
     * @return bool
     */
    public function isGetRequestOrQuery(): bool
    {
        return $this->isGetRequestOrQuery;
    }
}
