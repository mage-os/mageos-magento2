<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */

namespace Magento\CatalogRule\Observer;

class RulePricesStorage
{
    /**
     * Store calculated catalog rules prices for products
     * Prices collected per website, customer group, date and product
     *
     * @var array
     */
    private $rulePrices = [];

    /**
     * @param string $id
     * @return false|float
     */
    public function getRulePrice($id)
    {
        return $this->rulePrices[$id] ?? false;
    }

    /**
     * @param string $id
     * @return bool
     */
    public function hasRulePrice($id)
    {
        return isset($this->rulePrices[$id]);
    }

    /**
     * @param string $id
     * @param float $price
     * @return void
     */
    public function setRulePrice($id, $price)
    {
        $this->rulePrices[$id] = $price;
    }
}
