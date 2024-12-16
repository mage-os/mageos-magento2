<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\SalesRule\Model\Rule\Action\Discount;

class ExistingDiscountRuleCollector
{
    /**
     * @var array
     */
    private array $ruleDiscounts = [];

    /**
     * @param int $ruleId
     * @param float $discountAmount
     * @return void
     */
    public function setExistingRuleDiscount(int $ruleId, float $discountAmount): void
    {
        $this->ruleDiscounts[$ruleId] = $discountAmount;
    }

    /**
     * @param int $ruleId
     * @return float|null
     */
    public function getExistingRuleDiscount(int $ruleId): ?float
    {
        return $this->ruleDiscounts[$ruleId] ?? null;
    }
}
