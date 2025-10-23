<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Model\Quote\Item;

/**
 * Test helper for Quote Item
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 */
class QuoteItemTestHelper extends Item
{
    /** @var mixed */
    private $weeeTaxApplied = null;
    /** @var mixed */
    private $hasChildren = null;
    /** @var mixed */
    private $weeeTaxAppliedAmount = null;
    /** @var mixed */
    private $weeeTaxAppliedRowAmount = null;
    /** @var mixed */
    private $baseWeeeTaxAppliedRowAmnt = null;
    /** @var mixed */
    private $baseWeeeTaxAppliedAmount = null;
    /** @var mixed */
    private $weeeTaxAppliedAmountInclTax = null;
    /** @var mixed */
    private $weeeTaxAppliedRowAmountInclTax = null;
    /** @var mixed */
    private $baseWeeeTaxAppliedAmountInclTax = null;
    /** @var mixed */
    private $baseWeeeTaxAppliedRowAmntInclTax = null;
    /** @var array */
    private $associatedTaxables = [];
    /** @var mixed */
    private $priceInclTax = null;
    /** @var mixed */
    private $rowTotal = null;
    /** @var mixed */
    private $rowTotalInclTax = null;
    /** @var mixed */
    private $storeId = null;
    /** @var mixed */
    private $baseRowTotalInclTax = null;
    /** @var mixed */
    private $baseRowTotal = null;
    /** @var mixed */
    private $basePrice = null;
    /** @var mixed */
    private $basePriceInclTax = null;
    /** @var mixed */
    private $qtyOrdered = null;
    /** @var mixed */
    private $calculationPrice = null;
    /** @var mixed */
    private $originalItem = null;
    /** @var mixed */
    private $children = null;
    /** @var mixed */
    private $product = null;
    /** @var int */
    private $totalQty = 0;
    /** @var mixed */
    private $parentItem = null;
    /** @var bool */
    private $isChildrenCalculated = false;
    /** @var mixed */
    private $productId = null;
    /** @var mixed */
    private $buyRequest = null;

    public function __construct()
    {
        $this->_data = [];
    }

    public function getWeeeTaxApplied()
    {
        return $this->weeeTaxApplied;
    }

    public function setWeeeTaxApplied($value): self
    {
        $this->weeeTaxApplied = $value;
        return $this;
    }

    public function getHasChildren()
    {
        return $this->hasChildren;
    }

    public function setHasChildren($value): self
    {
        $this->hasChildren = $value;
        return $this;
    }

    public function getWeeeTaxAppliedAmount()
    {
        return $this->weeeTaxAppliedAmount;
    }

    public function setWeeeTaxAppliedAmount($value): self
    {
        $this->weeeTaxAppliedAmount = $value;
        return $this;
    }

    public function getWeeeTaxAppliedRowAmount()
    {
        return $this->weeeTaxAppliedRowAmount;
    }

    public function setWeeeTaxAppliedRowAmount($value): self
    {
        $this->weeeTaxAppliedRowAmount = $value;
        return $this;
    }

    public function getBaseWeeeTaxAppliedRowAmnt()
    {
        return $this->baseWeeeTaxAppliedRowAmnt;
    }

    public function setBaseWeeeTaxAppliedRowAmnt($value): self
    {
        $this->baseWeeeTaxAppliedRowAmnt = $value;
        return $this;
    }

    public function getBaseWeeeTaxAppliedAmount()
    {
        return $this->baseWeeeTaxAppliedAmount;
    }

    public function setBaseWeeeTaxAppliedAmount($value): self
    {
        $this->baseWeeeTaxAppliedAmount = $value;
        return $this;
    }

    public function getOriginalItem()
    {
        return $this->originalItem;
    }

    public function setOriginalItem($item): self
    {
        $this->originalItem = $item;
        return $this;
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function setChildren($children): self
    {
        $this->children = $children;
        return $this;
    }

    public function getProduct()
    {
        return $this->product;
    }

    public function setProduct($product): self
    {
        $this->product = $product;
        return $this;
    }

    public function getTotalQty(): int
    {
        return $this->totalQty;
    }

    public function setTotalQty($totalQty): self
    {
        $this->totalQty = (int) $totalQty;
        return $this;
    }

    public function getParentItem()
    {
        return $this->parentItem;
    }

    public function setParentItem($parentItem): self
    {
        $this->parentItem = $parentItem;
        return $this;
    }

    public function isChildrenCalculated(): bool
    {
        return $this->isChildrenCalculated;
    }

    public function setIsChildrenCalculated(bool $isChildrenCalculated): self
    {
        $this->isChildrenCalculated = $isChildrenCalculated;
        return $this;
    }

    public function getData($key = '', $index = null)
    {
        if ($key === '' || $key === null) {
            return [
                'weee_tax_applied_amount' => $this->weeeTaxAppliedAmount,
                'weee_tax_applied_row_amount' => $this->weeeTaxAppliedRowAmount,
                'base_weee_tax_applied_row_amnt' => $this->baseWeeeTaxAppliedRowAmnt,
                'base_weee_tax_applied_amount' => $this->baseWeeeTaxAppliedAmount,
                'weee_tax_applied_amount_incl_tax' => $this->weeeTaxAppliedAmountInclTax,
                'weee_tax_applied_row_amount_incl_tax' => $this->weeeTaxAppliedRowAmountInclTax,
                'base_weee_tax_applied_amount_incl_tax' => $this->baseWeeeTaxAppliedAmountInclTax,
                'base_weee_tax_applied_row_amnt_incl_tax' => $this->baseWeeeTaxAppliedRowAmntInclTax,
                'weee_tax_applied' => $this->weeeTaxApplied,
                'has_children' => $this->hasChildren,
                'original_item' => $this->originalItem,
                'children' => $this->children,
                'product' => $this->product,
                'total_qty' => $this->totalQty,
                'parent_item' => $this->parentItem,
                'is_children_calculated' => $this->isChildrenCalculated,
            ];
        }
        
        switch ($key) {
            case 'weee_tax_applied_amount':
                return $this->weeeTaxAppliedAmount;
            case 'weee_tax_applied_row_amount':
                return $this->weeeTaxAppliedRowAmount;
            case 'base_weee_tax_applied_row_amnt':
                return $this->baseWeeeTaxAppliedRowAmnt;
            case 'base_weee_tax_applied_amount':
                return $this->baseWeeeTaxAppliedAmount;
            case 'weee_tax_applied_amount_incl_tax':
                return $this->weeeTaxAppliedAmountInclTax;
            case 'weee_tax_applied_row_amount_incl_tax':
                return $this->weeeTaxAppliedRowAmountInclTax;
            case 'base_weee_tax_applied_amount_incl_tax':
                return $this->baseWeeeTaxAppliedAmountInclTax;
            case 'base_weee_tax_applied_row_amnt_incl_tax':
                return $this->baseWeeeTaxAppliedRowAmntInclTax;
            case 'weee_tax_applied':
                return $this->weeeTaxApplied ?? [];
            case 'has_children':
                return $this->hasChildren;
            case 'original_item':
                return $this->originalItem;
            case 'children':
                return $this->children;
            case 'product':
                return $this->product;
            case 'total_qty':
                return $this->totalQty;
            case 'parent_item':
                return $this->parentItem;
            case 'is_children_calculated':
                return $this->isChildrenCalculated;
            default:
                return null;
        }
    }

    public function getAssociatedTaxables()
    {
        return $this->associatedTaxables;
    }

    public function setAssociatedTaxables($associatedTaxables)
    {
        $this->associatedTaxables = $associatedTaxables;
        return $this;
    }

    public function getPriceInclTax()
    {
        return $this->priceInclTax;
    }

    public function setPriceInclTax($price)
    {
        $this->priceInclTax = $price;
        return $this;
    }

    public function getRowTotal()
    {
        return $this->rowTotal;
    }

    public function setRowTotal($total)
    {
        $this->rowTotal = $total;
        return $this;
    }

    public function getRowTotalInclTax()
    {
        return $this->rowTotalInclTax;
    }

    public function setRowTotalInclTax($total)
    {
        $this->rowTotalInclTax = $total;
        return $this;
    }

    public function getStoreId()
    {
        return $this->storeId;
    }

    public function setStoreId($id)
    {
        $this->storeId = $id;
        return $this;
    }

    public function getBaseRowTotalInclTax()
    {
        return $this->baseRowTotalInclTax;
    }

    public function setBaseRowTotalInclTax($total)
    {
        $this->baseRowTotalInclTax = $total;
        return $this;
    }

    public function getBaseRowTotal()
    {
        return $this->baseRowTotal;
    }

    public function setBaseRowTotal($total)
    {
        $this->baseRowTotal = $total;
        return $this;
    }

    public function getBasePrice()
    {
        return $this->basePrice;
    }

    public function setBasePrice($price)
    {
        $this->basePrice = $price;
        return $this;
    }

    public function getBasePriceInclTax()
    {
        return $this->basePriceInclTax;
    }

    public function setBasePriceInclTax($price)
    {
        $this->basePriceInclTax = $price;
        return $this;
    }

    public function getQtyOrdered()
    {
        return $this->qtyOrdered;
    }

    public function setQtyOrdered($qty)
    {
        $this->qtyOrdered = $qty;
        return $this;
    }

    public function getCalculationPrice()
    {
        return $this->calculationPrice;
    }

    public function setCalculationPrice($price)
    {
        $this->calculationPrice = $price;
        return $this;
    }

    public function setData($key, $value = null): self
    {
        switch ($key) {
            case 'weee_tax_applied_amount':
                $this->weeeTaxAppliedAmount = $value;
                break;
            case 'weee_tax_applied_row_amount':
                $this->weeeTaxAppliedRowAmount = $value;
                break;
            case 'base_weee_tax_applied_row_amnt':
                $this->baseWeeeTaxAppliedRowAmnt = $value;
                break;
            case 'base_weee_tax_applied_amount':
                $this->baseWeeeTaxAppliedAmount = $value;
                break;
            case 'weee_tax_applied_amount_incl_tax':
                $this->weeeTaxAppliedAmountInclTax = $value;
                break;
            case 'weee_tax_applied_row_amount_incl_tax':
                $this->weeeTaxAppliedRowAmountInclTax = $value;
                break;
            case 'base_weee_tax_applied_amount_incl_tax':
                $this->baseWeeeTaxAppliedAmountInclTax = $value;
                break;
            case 'base_weee_tax_applied_row_amnt_incl_tax':
                $this->baseWeeeTaxAppliedRowAmntInclTax = $value;
                break;
            case 'weee_tax_applied':
                $this->weeeTaxApplied = $value;
                break;
            case 'has_children':
                $this->hasChildren = $value;
                break;
            case 'original_item':
                $this->originalItem = $value;
                break;
            case 'children':
                $this->children = $value;
                break;
            case 'product':
                $this->product = $value;
                break;
            case 'total_qty':
                $this->totalQty = (int) $value;
                break;
            case 'parent_item':
                $this->parentItem = $value;
                break;
            case 'is_children_calculated':
                $this->isChildrenCalculated = $value;
                break;
        }
        return $this;
    }

    public function setProductId($productId): self
    {
        $this->productId = $productId;
        return $this;
    }

    public function getProductId()
    {
        return $this->productId;
    }

    public function setBuyRequest($buyRequest): self
    {
        $this->buyRequest = $buyRequest;
        return $this;
    }

    public function getBuyRequest()
    {
        return $this->buyRequest;
    }
}
