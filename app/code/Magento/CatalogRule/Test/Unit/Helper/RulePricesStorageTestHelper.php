<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogRule\Test\Unit\Helper;

use Magento\CatalogRule\Observer\RulePricesStorage;

/**
 * TestHelper for RulePricesStorage
 */
class RulePricesStorageTestHelper extends RulePricesStorage
{
    /** @var float|null */
    private $rulePrice = null;

    public function __construct()
    {
        // Skip parent constructor to avoid complex dependencies
    }

    /**
     * Custom method for testing
     *
     * @param int $value
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setCustomerGroupId($value)
    {
        return $this;
    }

    /**
     * Override parent
     *
     * @param string $id
     * @return false|float
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getRulePrice($id)
    {
        return $this->rulePrice ?? false;
    }

    /**
     * Override parent
     *
     * @param string $id
     * @param float $price
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setRulePrice($id, $price)
    {
        $this->rulePrice = $price;
    }
}
