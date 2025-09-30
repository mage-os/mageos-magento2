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
 */
class QuoteItemTestHelper extends Item
{
    /**
     * @var mixed
     */
    private $weeeTaxApplied = null;

    /**
     * @var mixed
     */
    private $hasChildren = null;

    /**
     * @var mixed
     */
    private $weeeTaxAppliedAmount = null;

    /**
     * @var mixed
     */
    private $weeeTaxAppliedRowAmount = null;

    /**
     * @var mixed
     */
    private $baseWeeeTaxAppliedRowAmnt = null;

    /**
     * @var mixed
     */
    private $baseWeeeTaxAppliedAmount = null;

    /**
     * @var mixed
     */
    private $weeeTaxAppliedAmountInclTax = null;

    /**
     * @var mixed
     */
    private $weeeTaxAppliedRowAmountInclTax = null;

    /**
     * @var mixed
     */
    private $baseWeeeTaxAppliedAmountInclTax = null;

    /**
     * @var mixed
     */
    private $baseWeeeTaxAppliedRowAmntInclTax = null;

    /**
     * @var array
     */
    private $associatedTaxables = [];

    /**
     * @var mixed
     */
    private $priceInclTax = null;

    /**
     * @var mixed
     */
    private $rowTotal = null;

    /**
     * @var mixed
     */
    private $rowTotalInclTax = null;

    /**
     * @var mixed
     */
    private $storeId = null;

    /**
     * @var mixed
     */
    private $baseRowTotalInclTax = null;

    /**
     * @var mixed
     */
    private $baseRowTotal = null;

    /**
     * @var mixed
     */
    private $basePrice = null;

    /**
     * @var mixed
     */
    private $baseWeeeTaxInclTax = null;

    /**
     * @var mixed
     */
    private $basePriceInclTax = null;

    /**
     * @var mixed
     */
    private $qtyOrdered = null;

    /**
     * @var mixed
     */
    private $calculationPrice = null;

    /**
     * @var mixed
     */
    private $originalItem = null;

    /**
     * @var mixed
     */
    private $children = null;

    /**
     * @var mixed
     */
    private $product = null;

    /**
     * @var mixed
     */
    private $quote = null;

    /**
     * @var mixed
     */
    private $address = null;

    /**
     * @var int
     */
    private $totalQty = 0;

    /**
     * @var mixed
     */
    private $parentItem = null;

    /**
     * @var bool
     */
    private $isChildrenCalculated = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get weee tax applied
     *
     * @return mixed
     */
    public function getWeeeTaxApplied()
    {
        return $this->weeeTaxApplied;
    }

    /**
     * Set weee tax applied
     *
     * @param mixed $value
     * @return $this
     */
    public function setWeeeTaxApplied($value): self
    {
        $this->weeeTaxApplied = $value;
        return $this;
    }

    /**
     * Get has children
     *
     * @return mixed
     */
    public function getHasChildren()
    {
        return $this->hasChildren;
    }

    /**
     * Set has children
     *
     * @param mixed $value
     * @return $this
     */
    public function setHasChildren($value): self
    {
        $this->hasChildren = $value;
        return $this;
    }

    /**
     * Get weee tax applied amount
     *
     * @return mixed
     */
    public function getWeeeTaxAppliedAmount()
    {
        return $this->weeeTaxAppliedAmount;
    }

    /**
     * Set weee tax applied amount
     *
     * @param mixed $value
     * @return $this
     */
    public function setWeeeTaxAppliedAmount($value): self
    {
        $this->weeeTaxAppliedAmount = $value;
        return $this;
    }

    /**
     * Get weee tax applied row amount
     *
     * @return mixed
     */
    public function getWeeeTaxAppliedRowAmount()
    {
        return $this->weeeTaxAppliedRowAmount;
    }

    /**
     * Set weee tax applied row amount
     *
     * @param mixed $value
     * @return $this
     */
    public function setWeeeTaxAppliedRowAmount($value): self
    {
        $this->weeeTaxAppliedRowAmount = $value;
        return $this;
    }

    /**
     * Get base weee tax applied row amount
     *
     * @return mixed
     */
    public function getBaseWeeeTaxAppliedRowAmnt()
    {
        return $this->baseWeeeTaxAppliedRowAmnt;
    }

    /**
     * Set base weee tax applied row amount
     *
     * @param mixed $value
     * @return $this
     */
    public function setBaseWeeeTaxAppliedRowAmnt($value): self
    {
        $this->baseWeeeTaxAppliedRowAmnt = $value;
        return $this;
    }

    /**
     * Get base weee tax applied amount
     *
     * @return mixed
     */
    public function getBaseWeeeTaxAppliedAmount()
    {
        return $this->baseWeeeTaxAppliedAmount;
    }

    /**
     * Set base weee tax applied amount
     *
     * @param mixed $value
     * @return $this
     */
    public function setBaseWeeeTaxAppliedAmount($value): self
    {
        $this->baseWeeeTaxAppliedAmount = $value;
        return $this;
    }

    /**
     * Get original item
     *
     * @return mixed
     */
    public function getOriginalItem()
    {
        return $this->originalItem;
    }

    /**
     * Set original item
     *
     * @param mixed $item
     * @return $this
     */
    public function setOriginalItem($item): self
    {
        $this->originalItem = $item;
        return $this;
    }

    /**
     * Get children
     *
     * @return mixed
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set children
     *
     * @param mixed $children
     * @return $this
     */
    public function setChildren($children): self
    {
        $this->children = $children;
        return $this;
    }

    /**
     * Get product
     *
     * @return mixed
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set product
     *
     * @param mixed $product
     * @return $this
     */
    public function setProduct($product): self
    {
        $this->product = $product;
        return $this;
    }

    /**
     * Get quote
     *
     * @return mixed
     */
    public function getQuote()
    {
        return $this->quote;
    }

    /**
     * Set quote
     *
     * @param mixed $quote
     * @return $this
     */
    public function setQuote($quote): self
    {
        $this->quote = $quote;
        return $this;
    }

    /**
     * Get address
     *
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set address
     *
     * @param mixed $address
     * @return $this
     */
    public function setAddress($address): self
    {
        $this->address = $address;
        return $this;
    }

    /**
     * Get total qty
     *
     * @return int
     */
    public function getTotalQty(): int
    {
        return $this->totalQty;
    }

    /**
     * Set total qty
     *
     * @param int|float $totalQty
     * @return $this
     */
    public function setTotalQty($totalQty): self
    {
        $this->totalQty = (int) $totalQty;
        return $this;
    }

    /**
     * Get parent item
     *
     * @return mixed
     */
    public function getParentItem()
    {
        return $this->parentItem;
    }

    /**
     * Set parent item
     *
     * @param mixed $parentItem
     * @return $this
     */
    public function setParentItem($parentItem): self
    {
        $this->parentItem = $parentItem;
        return $this;
    }

    /**
     * Is children calculated
     *
     * @return bool
     */
    public function isChildrenCalculated(): bool
    {
        return $this->isChildrenCalculated;
    }

    /**
     * Set is children calculated
     *
     * @param bool $isChildrenCalculated
     * @return $this
     */
    public function setIsChildrenCalculated(bool $isChildrenCalculated): self
    {
        $this->isChildrenCalculated = $isChildrenCalculated;
        return $this;
    }

    /**
     * Get data by key
     *
     * @param string $key
     * @param mixed $index
     * @return mixed
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
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
                'children' => $this->children,
                'product' => $this->product,
                'quote' => $this->quote,
                'address' => $this->address,
                'total_qty' => $this->totalQty,
                'parent_item' => $this->parentItem,
                'is_children_calculated' => $this->isChildrenCalculated,
                'original_item' => $this->originalItem,
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
            case 'children':
                return $this->children;
            case 'product':
                return $this->product;
            case 'quote':
                return $this->quote;
            case 'address':
                return $this->address;
            case 'total_qty':
                return $this->totalQty;
            case 'parent_item':
                return $this->parentItem;
            case 'is_children_calculated':
                return $this->isChildrenCalculated;
            case 'original_item':
                return $this->originalItem;
            default:
                return null;
        }
    }

    /**
     * Get associated taxables
     *
     * @return array
     */
    public function getAssociatedTaxables()
    {
        return $this->associatedTaxables;
    }

    /**
     * Set associated taxables
     *
     * @param array $associatedTaxables
     * @return $this
     */
    public function setAssociatedTaxables($associatedTaxables)
    {
        $this->associatedTaxables = $associatedTaxables;
        return $this;
    }

    /**
     * Get price incl tax
     *
     * @return mixed
     */
    public function getPriceInclTax()
    {
        return $this->priceInclTax;
    }

    /**
     * Set price incl tax
     *
     * @param mixed $price
     * @return $this
     */
    public function setPriceInclTax($price)
    {
        $this->priceInclTax = $price;
        return $this;
    }

    /**
     * Get row total
     *
     * @return mixed
     */
    public function getRowTotal()
    {
        return $this->rowTotal;
    }

    /**
     * Set row total
     *
     * @param mixed $total
     * @return $this
     */
    public function setRowTotal($total)
    {
        $this->rowTotal = $total;
        return $this;
    }

    /**
     * Get row total incl tax
     *
     * @return mixed
     */
    public function getRowTotalInclTax()
    {
        return $this->rowTotalInclTax;
    }

    /**
     * Set row total incl tax
     *
     * @param mixed $total
     * @return $this
     */
    public function setRowTotalInclTax($total)
    {
        $this->rowTotalInclTax = $total;
        return $this;
    }

    /**
     * Get store ID
     *
     * @return mixed
     */
    public function getStoreId()
    {
        return $this->storeId;
    }

    /**
     * Set store ID
     *
     * @param mixed $id
     * @return $this
     */
    public function setStoreId($id)
    {
        $this->storeId = $id;
        return $this;
    }

    /**
     * Get base row total incl tax
     *
     * @return mixed
     */
    public function getBaseRowTotalInclTax()
    {
        return $this->baseRowTotalInclTax;
    }

    /**
     * Set base row total incl tax
     *
     * @param mixed $total
     * @return $this
     */
    public function setBaseRowTotalInclTax($total)
    {
        $this->baseRowTotalInclTax = $total;
        return $this;
    }

    /**
     * Get base row total
     *
     * @return mixed
     */
    public function getBaseRowTotal()
    {
        return $this->baseRowTotal;
    }

    /**
     * Set base row total
     *
     * @param mixed $total
     * @return $this
     */
    public function setBaseRowTotal($total)
    {
        $this->baseRowTotal = $total;
        return $this;
    }

    /**
     * Get base price
     *
     * @return mixed
     */
    public function getBasePrice()
    {
        return $this->basePrice;
    }

    /**
     * Set base price
     *
     * @param mixed $price
     * @return $this
     */
    public function setBasePrice($price)
    {
        $this->basePrice = $price;
        return $this;
    }

    /**
     * Get base weee tax incl tax
     *
     * @return mixed
     */
    public function getBaseWeeeTaxInclTax()
    {
        return $this->baseWeeeTaxInclTax;
    }

    /**
     * Set base weee tax incl tax
     *
     * @param mixed $tax
     * @return $this
     */
    public function setBaseWeeeTaxInclTax($tax)
    {
        $this->baseWeeeTaxInclTax = $tax;
        return $this;
    }

    /**
     * Get base price incl tax
     *
     * @return mixed
     */
    public function getBasePriceInclTax()
    {
        return $this->basePriceInclTax;
    }

    /**
     * Set base price incl tax
     *
     * @param mixed $price
     * @return $this
     */
    public function setBasePriceInclTax($price)
    {
        $this->basePriceInclTax = $price;
        return $this;
    }

    /**
     * Get qty ordered
     *
     * @return mixed
     */
    public function getQtyOrdered()
    {
        return $this->qtyOrdered;
    }

    /**
     * Set qty ordered
     *
     * @param mixed $qty
     * @return $this
     */
    public function setQtyOrdered($qty)
    {
        $this->qtyOrdered = $qty;
        return $this;
    }

    /**
     * Get calculation price
     *
     * @return mixed
     */
    public function getCalculationPrice()
    {
        return $this->calculationPrice;
    }

    /**
     * Set calculation price
     *
     * @param mixed $price
     * @return $this
     */
    public function setCalculationPrice($price)
    {
        $this->calculationPrice = $price;
        return $this;
    }

    /**
     * Set data by key
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
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
            case 'children':
                $this->children = $value;
                break;
            case 'product':
                $this->product = $value;
                break;
            case 'quote':
                $this->quote = $value;
                break;
            case 'address':
                $this->address = $value;
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
            case 'original_item':
                $this->originalItem = $value;
                break;
        }
        return $this;
    }

    /**
     * Set product ID
     *
     * @param mixed $productId
     * @return $this
     */
    public function setProductId($productId): self
    {
        $this->productId = $productId;
        return $this;
    }

    /**
     * Get product ID
     *
     * @return mixed
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * Set buy request
     *
     * @param mixed $buyRequest
     * @return $this
     */
    public function setBuyRequest($buyRequest): self
    {
        $this->buyRequest = $buyRequest;
        return $this;
    }

    /**
     * Get buy request
     *
     * @return mixed
     */
    public function getBuyRequest()
    {
        return $this->buyRequest;
    }
}
