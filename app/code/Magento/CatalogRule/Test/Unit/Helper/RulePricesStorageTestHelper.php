<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogRule\Test\Unit\Helper;

use Magento\CatalogRule\Observer\RulePricesStorage;

/**
 * TestHelper for RulePricesStorage with dynamic methods
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class RulePricesStorageTestHelper extends RulePricesStorage
{
    /** @var int|null */
    private $websiteId = null;
    /** @var int|null */
    private $customerGroupId = null;
    /** @var float|null */
    private $rulePrice = null;

    public function __construct()
    {
        // Skip parent constructor to avoid complex dependencies
    }

    // Dynamic methods from addMethods
    public function getWebsiteId()
    {
        return $this->websiteId;
    }

    public function setWebsiteId($value)
    {
        $this->websiteId = $value;
        return $this;
    }

    public function getCustomerGroupId()
    {
        return $this->customerGroupId;
    }

    public function setCustomerGroupId($value)
    {
        $this->customerGroupId = $value;
        return $this;
    }

    // Methods from onlyMethods
    public function getRulePrice($id)
    {
        return $this->rulePrice;
    }

    public function setRulePrice($id, $price)
    {
        $this->rulePrice = $price;
        return $this;
    }
}
